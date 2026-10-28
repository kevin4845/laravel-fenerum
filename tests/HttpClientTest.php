<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Kevin4845\LaravelFenerum\Exception\ApiException;
use Kevin4845\LaravelFenerum\Exception\InvalidResponseException;
use Kevin4845\LaravelFenerum\Exception\NetworkException;
use Kevin4845\LaravelFenerum\FenerumConfig;
use Psr\Http\Message\RequestInterface;

final class HttpClientTest extends FenerumTestCase
{
    public function testGetSendsAuthenticationHeadersAndTrailingSlash(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'account-1', 'code' => 'ACME 1']),
        ]);

        $account = $client->accounts()->get('ACME 1', 'uuid,code');

        self::assertSame('account-1', $account['uuid']);
        $request = $this->lastRequest();
        self::assertSame('GET', $request->getMethod());
        self::assertSame('https://fenerum.test/api/v1/accounts/ACME%201/', (string) $request->getUri());
        self::assertSame('Token secret-token', $request->getHeaderLine('Authorization'));
        self::assertSame('TestApp', $request->getHeaderLine('X-Client-System'));
        self::assertSame('jane', $request->getHeaderLine('X-User'));
        self::assertSame('application/json', $request->getHeaderLine('Accept'));
        self::assertSame('uuid,code', $request->getHeaderLine('Fields'));
        self::assertSame(3.5, $this->capturedTimeout);
        self::assertFalse($this->capturedRedirects);
    }

    public function testListSendsQueryParameters(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [
                'count' => 1,
                'next' => null,
                'previous' => null,
                'results' => [['code' => 'CUST001']],
            ]),
        ]);

        $page = $client->accounts()->list([
            'code' => 'CUST001',
            'page' => 2,
            'page_size' => 50,
            'is_archived' => false,
            'search' => null,
        ]);

        self::assertSame(1, $page->count);
        self::assertNull($page->next);
        self::assertSame('CUST001', $page->results[0]['code']);
        self::assertSame(
            'code=CUST001&page=2&page_size=50&is_archived=false',
            $this->lastRequest()->getUri()->getQuery(),
        );
    }

    public function testCreateSendsAJsonBody(): void
    {
        $client = $this->client([
            $this->jsonResponse(201, ['code' => 'CUST001', 'company_name' => 'Acme']),
        ]);

        $created = $client->accounts()->create([
            'code' => 'CUST001',
            'company_name' => 'Acme',
            'legal_address' => 'Street 1',
            'legal_zipcode' => '8000',
            'legal_city' => 'Aarhus',
            'legal_country' => 'DK',
        ]);

        self::assertSame('CUST001', $created['code']);
        self::assertSame('POST', $this->lastRequest()->getMethod());
        self::assertSame('application/json', $this->lastRequest()->getHeaderLine('Content-Type'));
        self::assertSame(
            [
                'code' => 'CUST001',
                'company_name' => 'Acme',
                'legal_address' => 'Street 1',
                'legal_zipcode' => '8000',
                'legal_city' => 'Aarhus',
                'legal_country' => 'DK',
            ],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testPerRequestUserOverridesTheConfiguredUser(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['count' => 0, 'next' => null, 'previous' => null, 'results' => []]),
        ]);

        $client->withUser(null)->request('GET', '/api/v1/accounts/', headers: ['X-User' => 'other']);

        self::assertSame('other', $this->lastRequest()->getHeaderLine('X-User'));
        self::assertSame('Token secret-token', $this->lastRequest()->getHeaderLine('Authorization'));
    }

    public function testAuthenticationFailureDoesNotExposeTheToken(): void
    {
        $client = $this->client([
            $this->jsonResponse(401, ['detail' => 'Invalid token secret-token.']),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(401, $exception->statusCode);
            self::assertSame('Fenerum API request failed with HTTP 401. Invalid token [redacted].', $exception->getMessage());
            self::assertStringNotContainsString('secret-token', $exception->responseBody);
            self::assertStringNotContainsString('secret-token', (string) $exception);
            self::assertNull($exception->getPrevious());
        }
    }

    public function testValidationErrorsExposeFieldMessages(): void
    {
        $client = $this->client([
            $this->jsonResponse(400, ['legal_country' => ['This field is required.']]),
        ]);

        try {
            $client->accounts()->create(['code' => 'CUST001']);
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(400, $exception->statusCode);
            self::assertSame(['legal_country' => ['This field is required.']], $exception->errors);
        }
    }

    public function testRateLimitExposesRetryAfter(): void
    {
        $client = $this->client([
            new Response(
                429,
                ['Retry-After' => '42'],
                json_encode(['detail' => 'Request was throttled. Expected available in 42 seconds.'], JSON_THROW_ON_ERROR),
            ),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(429, $exception->statusCode);
            self::assertSame('42', $exception->retryAfter);
            self::assertSame(0, $exception->retries);
            self::assertStringContainsString('throttled', $exception->getMessage());
        }
    }

    public function testWebhookPasswordIsRemovedFromErrorBodies(): void
    {
        $client = $this->client([
            $this->jsonResponse(400, ['detail' => 'Rejected webhook-password and secret-token']),
        ]);

        try {
            $client->webhooks()->create([
                'endpoint' => 'https://example.test/hooks',
                'basic_auth_password' => 'webhook-password',
            ]);
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertStringNotContainsString('webhook-password', $exception->getMessage());
            self::assertStringNotContainsString('secret-token', $exception->responseBody);
            self::assertStringNotContainsString('webhook-password', $exception->responseBody);
        }
    }

    public function testNetworkFailureDoesNotExposeTheToken(): void
    {
        $client = $this->client([
            static function (RequestInterface $request): never {
                throw new ConnectException('Could not connect using secret-token', $request);
            },
        ]);

        try {
            $client->accounts()->get('CUST001');
            self::fail('Expected a network exception.');
        } catch (NetworkException $exception) {
            self::assertSame(
                'Fenerum API request failed because the connection could not be completed.',
                $exception->getMessage(),
            );
            self::assertStringNotContainsString('secret-token', (string) $exception);
            self::assertNull($exception->getPrevious());
        }
    }

    public function testInvalidJsonIsRejected(): void
    {
        $client = $this->client([
            new Response(200, [], '<html>secret-token</html>'),
        ]);

        try {
            $client->accounts()->get('CUST001');
            self::fail('Expected an invalid response exception.');
        } catch (InvalidResponseException $exception) {
            self::assertSame(200, $exception->statusCode);
            self::assertStringNotContainsString('secret-token', $exception->responseBody);
            self::assertStringContainsString('[redacted]', $exception->responseBody);
        }
    }

    public function testConfigurationRejectsSecretsInTheWrongShape(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FenerumConfig('secret token', 'TestApp');
    }

    public function testConfigurationHidesTheTokenFromDebugOutput(): void
    {
        $config = new FenerumConfig('secret-token', 'TestApp', null, 'https://app.fenerum.com/');

        ob_start();
        var_dump($config);
        $dump = ob_get_clean();

        self::assertIsString($dump);
        self::assertStringNotContainsString('secret-token', $dump);
        self::assertStringContainsString('[redacted]', $dump);
        self::assertSame('https://app.fenerum.com', $config->baseUrl);
    }

    public function testBaseUrlCannotIncludeAPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FenerumConfig('secret-token', 'TestApp', null, 'https://app.fenerum.com/api/v1');
    }
}
