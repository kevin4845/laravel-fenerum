<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use JsonException;
use Kevin4845\LaravelFenerum\Exception\ApiException;
use Kevin4845\LaravelFenerum\Exception\InvalidResponseException;
use Kevin4845\LaravelFenerum\Exception\NetworkException;
use Kevin4845\LaravelFenerum\Http\Clock;
use Kevin4845\LaravelFenerum\Http\GuzzleTransport;
use Kevin4845\LaravelFenerum\Http\HttpResponse;
use Kevin4845\LaravelFenerum\Http\HttpTransport;
use Kevin4845\LaravelFenerum\Http\RawResponse;
use Kevin4845\LaravelFenerum\Http\Sleeper;
use Kevin4845\LaravelFenerum\Http\SystemClock;
use Kevin4845\LaravelFenerum\Http\UsleepSleeper;
use Kevin4845\LaravelFenerum\Resource\Accounts;
use Kevin4845\LaravelFenerum\Resource\Invoices;
use Kevin4845\LaravelFenerum\Resource\PlanTerms;
use Kevin4845\LaravelFenerum\Resource\Plans;
use Kevin4845\LaravelFenerum\Resource\ProductPricings;
use Kevin4845\LaravelFenerum\Resource\Products;
use Kevin4845\LaravelFenerum\Resource\SubscriptionChanges;
use Kevin4845\LaravelFenerum\Resource\Subscriptions;
use Kevin4845\LaravelFenerum\Resource\Webhooks;
use Kevin4845\LaravelFenerum\Webhook\WebhookParser;

final class FenerumClient
{
    use ResourceAccess;

    private ?Accounts $accounts = null;

    private ?Subscriptions $subscriptions = null;

    private ?SubscriptionChanges $subscriptionChanges = null;

    private ?Plans $plans = null;

    private ?PlanTerms $planTerms = null;

    private ?Products $products = null;

    private ?ProductPricings $productPricings = null;

    private ?Invoices $invoices = null;

    private ?Webhooks $webhooks = null;

    private ?WebhookParser $webhookParser = null;

    public function __construct(
        private readonly FenerumConfig $config,
        private readonly HttpTransport $transport,
        private readonly Sleeper $sleeper = new UsleepSleeper(),
        private readonly Clock $clock = new SystemClock(),
    ) {
    }

    public static function create(
        string $token,
        string $clientSystem,
        ?string $user = null,
        string $baseUrl = 'https://app.fenerum.com',
        float $timeout = 10.0,
        ?ClientInterface $httpClient = null,
        int $maxRetries = 2,
        float $maxRetryDelaySeconds = 60.0,
        ?Sleeper $sleeper = null,
        ?Clock $clock = null,
    ): self {
        return new self(
            new FenerumConfig(
                $token,
                $clientSystem,
                $user,
                $baseUrl,
                $timeout,
                $maxRetries,
                $maxRetryDelaySeconds,
            ),
            new GuzzleTransport($httpClient ?? new Client()),
            $sleeper ?? new UsleepSleeper(),
            $clock ?? new SystemClock(),
        );
    }

    public function config(): FenerumConfig
    {
        return $this->config;
    }

    public function withUser(?string $user): self
    {
        return new self($this->config->withUser($user), $this->transport, $this->sleeper, $this->clock);
    }

    public function accounts(): Accounts
    {
        return $this->accounts ??= new Accounts($this);
    }

    public function subscriptions(): Subscriptions
    {
        return $this->subscriptions ??= new Subscriptions($this);
    }

    public function subscriptionChanges(): SubscriptionChanges
    {
        return $this->subscriptionChanges ??= new SubscriptionChanges($this);
    }

    public function plans(): Plans
    {
        return $this->plans ??= new Plans($this);
    }

    public function planTerms(): PlanTerms
    {
        return $this->planTerms ??= new PlanTerms($this);
    }

    public function products(): Products
    {
        return $this->products ??= new Products($this);
    }

    public function productPricings(): ProductPricings
    {
        return $this->productPricings ??= new ProductPricings($this);
    }

    public function invoices(): Invoices
    {
        return $this->invoices ??= new Invoices($this);
    }

    public function webhooks(): Webhooks
    {
        return $this->webhooks ??= new Webhooks($this);
    }

    public function webhookParser(): WebhookParser
    {
        return $this->webhookParser ??= new WebhookParser();
    }

    /**
     * Send a request to a Fenerum path and decode a JSON object or array.
     *
     * Paths must start with /api/ and end with the trailing slash Fenerum requires.
     * Successful empty responses return null.
     *
     * HTTP 429 is retried only for GET, PUT, and DELETE, and only when Retry-After is a
     * non-negative integer (delta-seconds) or an HTTP-date. POST and PATCH are not retried.
     * Retries stop after maxRetries additional attempts or when the next delay would exceed
     * maxRetryDelaySeconds. The final 429 is still thrown.
     *
     * @param array<string, scalar|null> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     * @return array<mixed>|null
     */
    public function request(
        string $method,
        string $path,
        array $query = [],
        array|\stdClass|null $json = null,
        array $headers = [],
    ): ?array {
        $secrets = $this->bodySecrets($json);
        $response = $this->send($method, $path, $query, $json, $headers, $secrets);

        return $this->decode($response, $secrets);
    }

    /**
     * Send a request and return the raw success body.
     *
     * Used when the schema describes a non-JSON payload, such as the KPI spreadsheet export.
     * Error statuses still raise ApiException.
     *
     * @param array<string, scalar|null> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     */
    public function requestRaw(
        string $method,
        string $path,
        array $query = [],
        array|\stdClass|null $json = null,
        array $headers = [],
    ): RawResponse {
        $secrets = $this->bodySecrets($json);
        $response = $this->send($method, $path, $query, $json, $headers, $secrets);

        return new RawResponse($response->statusCode, $response->body, $response->header('Content-Type'));
    }

    /**
     * @param array<string, scalar|null> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     * @param list<string> $secrets
     */
    private function send(
        string $method,
        string $path,
        array $query,
        array|\stdClass|null $json,
        array $headers,
        array $secrets,
    ): HttpResponse {
        if (!str_starts_with($path, '/api/') || !str_ends_with($path, '/')) {
            throw new InvalidArgumentException(
                'Fenerum paths must start with /api/ and end with a trailing slash.',
            );
        }

        $method = strtoupper($method);
        $query = $this->normalizeQuery($query);
        $attempt = 0;
        $waited = 0.0;

        while (true) {
            try {
                $response = $this->transport->send(
                    $method,
                    $this->config->baseUrl . $path,
                    $query,
                    $json,
                    $this->config->headers($headers),
                    $this->config->timeout,
                );
            } catch (NetworkException) {
                throw new NetworkException(
                    'Fenerum API request failed because the connection could not be completed.',
                );
            }

            if ($response->statusCode !== 429 || !$this->canRetry($method, $attempt)) {
                break;
            }

            $delay = $this->retryDelaySeconds($response);
            $remaining = $this->config->maxRetryDelaySeconds - $waited;
            if ($delay === null || $delay > $remaining) {
                break;
            }

            $this->sleeper->sleep($delay);
            $waited += $delay;
            $attempt++;
        }

        if ($response->statusCode < 200 || $response->statusCode >= 300) {
            throw $this->apiException($response, $secrets, $attempt, $waited);
        }

        return $response;
    }

    private function canRetry(string $method, int $attempt): bool
    {
        return $attempt < $this->config->maxRetries && in_array($method, ['GET', 'PUT', 'DELETE'], true);
    }

    /**
     * Retry-After as delta-seconds, or as an HTTP-date (IMF-fixdate, RFC 850, or asctime).
     * Any other value disables the retry. Delta-seconds are recognized before date parsing.
     */
    private function retryDelaySeconds(HttpResponse $response): ?float
    {
        $header = $response->header('Retry-After');
        if ($header === null) {
            return null;
        }

        $header = trim($header);
        if (preg_match('/^\d+$/', $header) === 1) {
            $delay = (float) $header;

            return is_finite($delay) ? $delay : null;
        }

        $timestamp = $this->httpDateTimestamp($header);
        if ($timestamp === null) {
            return null;
        }

        return max(0.0, $timestamp - $this->clock->now());
    }

    private function httpDateTimestamp(string $value): ?float
    {
        $formats = [
            'D, d M Y H:i:s \G\M\T',
            'l, d-M-y H:i:s \G\M\T',
            'D M d H:i:s Y',
            'D M  j H:i:s Y',
        ];

        foreach ($formats as $format) {
            $parsed = \DateTimeImmutable::createFromFormat('!' . $format, $value, new \DateTimeZone('GMT'));
            if ($parsed === false) {
                continue;
            }

            $errors = \DateTimeImmutable::getLastErrors();
            if (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                continue;
            }

            return (float) $parsed->getTimestamp();
        }

        return null;
    }

    /**
     * @param list<string> $secrets
     * @return array<mixed>|null
     */
    private function decode(HttpResponse $response, array $secrets): ?array
    {
        if (trim($response->body) === '') {
            return null;
        }

        try {
            $decoded = json_decode($response->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InvalidResponseException(
                'Fenerum API returned a response that is not valid JSON.',
                $response->statusCode,
                $this->redact($response->body, $secrets),
            );
        }

        if ($decoded === null) {
            return null;
        }

        if (!is_array($decoded)) {
            throw new InvalidResponseException(
                'Fenerum API returned a JSON value that is not an object or array.',
                $response->statusCode,
                $this->redact($response->body, $secrets),
            );
        }

        return $decoded;
    }

    /**
     * @param array<string, scalar|null> $query
     * @return array<string, string|int|float>
     */
    private function normalizeQuery(array $query): array
    {
        $normalized = [];

        foreach ($query as $key => $value) {
            if ($key === '') {
                throw new InvalidArgumentException('Query parameter names must not be empty.');
            }

            if ($value === null) {
                continue;
            }

            if (is_bool($value)) {
                $normalized[$key] = $value ? 'true' : 'false';
                continue;
            }

            if (is_int($value) || is_string($value)) {
                $normalized[$key] = $value;
                continue;
            }

            if (!is_finite($value)) {
                throw new InvalidArgumentException('Query parameters must be finite numbers.');
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    /**
     * @param list<string> $secrets
     */
    private function apiException(HttpResponse $response, array $secrets, int $retries, float $waited): ApiException
    {
        $body = $this->redact($response->body, $secrets);
        $errors = null;
        $message = sprintf('Fenerum API request failed with HTTP %d.', $response->statusCode);

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            $decoded = null;
        }

        if (is_array($decoded) && !array_is_list($decoded)) {
            $errors = $decoded;
            $detail = $errors['detail'] ?? null;
            if (is_string($detail)) {
                if (strlen($detail) > 500) {
                    $detail = substr($detail, 0, 500) . '...';
                }

                $message .= ' ' . $detail;
            }

            $names = [];
            foreach ($errors as $key => $value) {
                if ($key === 'detail') {
                    continue;
                }

                $names[] = (string) $key;
            }

            if ($names !== []) {
                $message .= ' Invalid fields: ' . implode(', ', $names) . '.';
            }
        }

        if ($retries > 0) {
            $message .= sprintf(' Retried %d time(s).', $retries);
        }

        $retryAfter = $response->header('Retry-After');
        if ($retryAfter !== null) {
            $retryAfter = $this->redact($retryAfter, $secrets);
        }

        return new ApiException($message, $response->statusCode, $errors, $body, $retryAfter, $retries, $waited);
    }

    /**
     * @param array<mixed>|\stdClass|null $json
     * @return list<string>
     */
    private function bodySecrets(array|\stdClass|null $json): array
    {
        if (!is_array($json)) {
            return [];
        }

        $secrets = [];
        foreach ($json as $key => $value) {
            if (!is_string($key) || !is_string($value) || preg_match('/password|token|secret|authorization/i', $key) !== 1) {
                continue;
            }

            if (strlen($value) >= 4) {
                $secrets[] = $value;
            }
        }

        return $secrets;
    }

    /**
     * @param list<string> $secrets
     */
    private function redact(string $value, array $secrets): string
    {
        $value = $this->config->redact($value);
        foreach ($secrets as $secret) {
            $value = str_replace($secret, '[redacted]', $value);
        }

        return $value;
    }
}
