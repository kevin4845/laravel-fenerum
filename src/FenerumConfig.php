<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum;

use InvalidArgumentException;

final class FenerumConfig
{
    public readonly string $baseUrl;

    public readonly ?string $user;

    public function __construct(
        private readonly string $token,
        public readonly string $clientSystem,
        ?string $user = null,
        string $baseUrl = 'https://app.fenerum.com',
        public readonly float $timeout = 10.0,
        public readonly int $maxRetries = 2,
        public readonly float $maxRetryDelaySeconds = 60.0,
    ) {
        if ($this->token === '' || preg_match('/\s/', $this->token) === 1) {
            throw new InvalidArgumentException(
                'The Fenerum API token must be a non-empty value without whitespace. Pass the token itself, without the "Token " prefix.',
            );
        }

        if ($this->clientSystem === '' || preg_match('/[\r\n]/', $this->clientSystem) === 1) {
            throw new InvalidArgumentException('X-Client-System must be a non-empty value without newlines.');
        }

        if ($user !== null && ($user === '' || preg_match('/[\r\n]/', $user) === 1)) {
            throw new InvalidArgumentException('X-User must be null or a non-empty value without newlines.');
        }

        $parts = parse_url($baseUrl);
        $scheme = is_array($parts) ? ($parts['scheme'] ?? null) : null;
        $host = is_array($parts) ? ($parts['host'] ?? null) : null;
        if (!is_array($parts) || !is_string($scheme) || !is_string($host) || $host === '') {
            throw new InvalidArgumentException('The Fenerum base URL must include a scheme and host.');
        }

        if ($scheme !== 'https' && $scheme !== 'http') {
            throw new InvalidArgumentException('The Fenerum base URL must use http or https.');
        }

        $path = $parts['path'] ?? '';
        if (($path !== '' && $path !== '/') || isset($parts['query']) || isset($parts['user'])) {
            throw new InvalidArgumentException(
                'The Fenerum base URL must be an origin such as https://app.fenerum.com, without credentials, a path, or a query.',
            );
        }

        if ($this->timeout <= 0 || !is_finite($this->timeout)) {
            throw new InvalidArgumentException('The request timeout must be a positive number of seconds.');
        }

        if ($this->maxRetries < 0) {
            throw new InvalidArgumentException('The maximum number of retries must be zero or greater.');
        }

        if ($this->maxRetryDelaySeconds < 0 || !is_finite($this->maxRetryDelaySeconds)) {
            throw new InvalidArgumentException('The maximum retry delay must be zero or a positive number of seconds.');
        }

        $this->user = $user;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function withUser(?string $user): self
    {
        return new self(
            $this->token,
            $this->clientSystem,
            $user,
            $this->baseUrl,
            $this->timeout,
            $this->maxRetries,
            $this->maxRetryDelaySeconds,
        );
    }

    /**
     * Authentication headers always come from this configuration.
     * A per-request X-User header is kept when present.
     *
     * @param array<string, string> $additional
     * @return array<string, string>
     */
    public function headers(array $additional = []): array
    {
        $headers = ['Accept' => 'application/json'];

        foreach ($additional as $name => $value) {
            if (preg_match('/[\r\n]/', $name) === 1 || preg_match('/[\r\n]/', $value) === 1) {
                throw new InvalidArgumentException('Header names and values must not contain newlines.');
            }

            if (strcasecmp($name, 'Authorization') === 0 || strcasecmp($name, 'X-Client-System') === 0) {
                continue;
            }

            $headers[$name] = $value;
        }

        $headers['Authorization'] = 'Token ' . $this->token;
        $headers['X-Client-System'] = $this->clientSystem;

        if (!array_key_exists('X-User', $headers) && $this->user !== null) {
            $headers['X-User'] = $this->user;
        }

        return $headers;
    }

    public function redact(string $value): string
    {
        return str_replace($this->token, '[redacted]', $value);
    }

    /**
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return [
            'baseUrl' => $this->baseUrl,
            'clientSystem' => $this->clientSystem,
            'user' => $this->user ?? '',
            'timeout' => (string) $this->timeout,
            'maxRetries' => (string) $this->maxRetries,
            'maxRetryDelaySeconds' => (string) $this->maxRetryDelaySeconds,
            'token' => '[redacted]',
        ];
    }
}
