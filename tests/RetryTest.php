<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests;

use GuzzleHttp\Exception\ConnectException;
use Kevin4845\LaravelFenerum\Exception\ApiException;
use Kevin4845\LaravelFenerum\Exception\NetworkException;
use Kevin4845\LaravelFenerum\FenerumConfig;
use Kevin4845\LaravelFenerum\Tests\Support\FixedClock;
use Kevin4845\LaravelFenerum\Tests\Support\RecordingSleeper;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\RequestInterface;

final class RetryTest extends FenerumTestCase
{
    public function testDeltaSecondsAreRetriedWithoutWaiting(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 2, 60, [
            $this->throttled('0'),
            $this->jsonResponse(200, ['count' => 0, 'next' => null, 'previous' => null, 'results' => []]),
        ]);

        $client->accounts()->list();

        self::assertSame([0.0], $sleeper->delays);
        self::assertCount(2, $this->history);
        self::assertSame('GET', $this->requestAt(1)->getMethod());
    }

    public function testAPositiveIntegerRetryAfterIsHonored(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 1, 60, [
            $this->throttled('7'),
            $this->jsonResponse(200, ['count' => 0, 'next' => null, 'previous' => null, 'results' => []]),
        ]);

        $client->accounts()->list();

        self::assertSame([7.0], $sleeper->delays);
        self::assertCount(2, $this->history);
    }

    public function testAPastHttpDateRetriesImmediately(): void
    {
        $sleeper = new RecordingSleeper();
        $now = 1_000_000_000.0;
        $client = $this->retryingClient($sleeper, 1, 60, [
            $this->throttled(gmdate('D, d M Y H:i:s', (int) $now - 30) . ' GMT'),
            $this->jsonResponse(200, ['uuid' => 'acct']),
        ], new FixedClock($now));

        $client->accounts()->get('CUST001');

        self::assertSame([0.0], $sleeper->delays);
        self::assertCount(2, $this->history);
    }

    #[DataProvider('unusableRetryAfter')]
    public function testUnusableRetryAfterValuesAreNotRetried(string $header): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 3, 60, [
            $this->throttled($header),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(429, $exception->statusCode);
            self::assertSame(0, $exception->retries);
            self::assertSame(0.0, $exception->retryDelaySeconds);
            self::assertStringNotContainsString('secret-token', $exception->getMessage());
        }

        self::assertSame([], $sleeper->delays);
        self::assertCount(1, $this->history);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function unusableRetryAfter(): array
    {
        return [
            'prose' => ['42 seconds'],
            'negative' => ['-1'],
            'fraction' => ['42.5'],
            'blank' => ['   '],
        ];
    }

    public function testAnHttpDateDelayIsMeasuredFromTheClock(): void
    {
        $sleeper = new RecordingSleeper();
        $retryAt = 1_000_000_005;
        $client = $this->retryingClient($sleeper, 1, 60, [
            $this->throttled(gmdate('D, d M Y H:i:s', $retryAt) . ' GMT'),
            $this->jsonResponse(200, ['uuid' => 'acct']),
        ], new FixedClock(1_000_000_000));

        $client->accounts()->get('CUST001');

        self::assertSame([5.0], $sleeper->delays);
    }

    #[DataProvider('httpDates')]
    public function testBothObsoleteHttpDateFormatsAreAccepted(string $header): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 1, 60, [
            $this->throttled($header),
            $this->jsonResponse(200, ['uuid' => 'acct']),
        ], new FixedClock(784111777));

        $client->accounts()->get('CUST001');

        self::assertSame([0.0], $sleeper->delays);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function httpDates(): array
    {
        return [
            'rfc850' => ['Sunday, 06-Nov-94 08:49:37 GMT'],
            'asctime' => ['Sun Nov  6 08:49:37 1994'],
        ];
    }

    public function testAMissingOrUnusableRetryAfterIsNotRetried(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 3, 60, [
            $this->throttled(null),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(429, $exception->statusCode);
            self::assertSame(0, $exception->retries);
            self::assertNull($exception->retryAfter);
        }

        self::assertSame([], $sleeper->delays);
        self::assertCount(1, $this->history);
    }

    public function testRetriesStopAtTheConfiguredCount(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 2, 60, [
            $this->throttled('1'),
            $this->throttled('1'),
            $this->throttled('1'),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(2, $exception->retries);
            self::assertSame(2.0, $exception->retryDelaySeconds);
            self::assertSame('1', $exception->retryAfter);
            self::assertStringContainsString('Retried 2 time(s).', $exception->getMessage());
        }

        self::assertSame([1.0, 1.0], $sleeper->delays);
        self::assertCount(3, $this->history);
    }

    public function testADelayBeyondTheBudgetIsNotSlept(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 2, 60, [
            $this->throttled('50'),
            $this->throttled('50'),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(1, $exception->retries);
            self::assertSame(50.0, $exception->retryDelaySeconds);
        }

        self::assertSame([50.0], $sleeper->delays);
        self::assertCount(2, $this->history);
    }

    public function testAFirstDelayAboveTheBudgetIsNotSlept(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 2, 60, [
            $this->throttled('61'),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(0, $exception->retries);
            self::assertSame(0.0, $exception->retryDelaySeconds);
            self::assertSame('61', $exception->retryAfter);
        }

        self::assertSame([], $sleeper->delays);
        self::assertCount(1, $this->history);
    }

    public function testANetworkErrorIsNotRetried(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 3, 60, [
            static function (RequestInterface $request): never {
                throw new ConnectException('Could not connect using secret-token', $request);
            },
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected a network exception.');
        } catch (NetworkException $exception) {
            self::assertNull($exception->getPrevious());
            self::assertStringNotContainsString('secret-token', (string) $exception);
        }

        self::assertSame([], $sleeper->delays);
        self::assertCount(1, $this->history);
    }

    public function testAServerErrorIsNotRetried(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 3, 60, [
            $this->jsonResponse(500, ['detail' => 'Unavailable secret-token']),
        ]);

        try {
            $client->accounts()->list();
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(500, $exception->statusCode);
            self::assertSame(0, $exception->retries);
            self::assertStringNotContainsString('secret-token', $exception->getMessage());
            self::assertStringNotContainsString('secret-token', $exception->responseBody);
        }

        self::assertSame([], $sleeper->delays);
        self::assertCount(1, $this->history);
    }

    public function testPutIsRetriedAndPostIsNot(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 2, 60, [
            $this->throttled('0'),
            $this->jsonResponse(200, ['uuid' => 'acct']),
            $this->throttled('0'),
        ]);

        $client->request('PUT', '/api/v1/accounts/CUST001/', json: ['code' => 'CUST001']);

        try {
            $client->request('POST', '/api/v1/accounts/', json: ['code' => 'CUST001']);
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(0, $exception->retries);
        }

        self::assertSame([0.0], $sleeper->delays);
        self::assertSame('PUT', $this->requestAt(0)->getMethod());
        self::assertSame('POST', $this->requestAt(2)->getMethod());
        self::assertCount(3, $this->history);
    }

    public function testPatchAndClientErrorsAreNotRetried(): void
    {
        $sleeper = new RecordingSleeper();
        $client = $this->retryingClient($sleeper, 4, 60, [
            $this->throttled('0'),
            $this->jsonResponse(400, ['detail' => 'Invalid.']),
            $this->jsonResponse(403, ['detail' => 'Forbidden.']),
        ]);

        try {
            $client->request('PATCH', '/api/v1/accounts/CUST001/', json: ['code' => 'CUST001']);
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(429, $exception->statusCode);
        }

        try {
            $client->accounts()->get('CUST001');
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(400, $exception->statusCode);
            self::assertSame(0, $exception->retries);
        }

        try {
            $client->accounts()->get('CUST001');
            self::fail('Expected an API exception.');
        } catch (ApiException $exception) {
            self::assertSame(403, $exception->statusCode);
        }

        self::assertSame([], $sleeper->delays);
        self::assertCount(3, $this->history);
    }

    /**
     * @param list<\GuzzleHttp\Psr7\Response|callable> $responses
     */
    private function retryingClient(
        RecordingSleeper $sleeper,
        int $maxRetries,
        float $maxDelay,
        array $responses,
        ?FixedClock $clock = null,
    ): \Kevin4845\LaravelFenerum\FenerumClient {
        return $this->client(
            $responses,
            new FenerumConfig(
                'secret-token',
                'TestApp',
                'jane',
                'https://fenerum.test',
                3.5,
                $maxRetries,
                $maxDelay,
            ),
            $sleeper,
            $clock,
        );
    }

    private function throttled(?string $retryAfter): \GuzzleHttp\Psr7\Response
    {
        $headers = $retryAfter === null ? [] : ['Retry-After' => $retryAfter];

        return new \GuzzleHttp\Psr7\Response(
            429,
            $headers,
            '{"detail":"Request was throttled."}',
        );
    }
}
