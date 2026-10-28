<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kevin4845\LaravelFenerum\FenerumClient;
use Kevin4845\LaravelFenerum\FenerumConfig;
use Kevin4845\LaravelFenerum\Http\Clock;
use Kevin4845\LaravelFenerum\Http\GuzzleTransport;
use Kevin4845\LaravelFenerum\Http\Sleeper;
use Kevin4845\LaravelFenerum\Http\SystemClock;
use Kevin4845\LaravelFenerum\Http\UsleepSleeper;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

abstract class FenerumTestCase extends TestCase
{
    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $history = [];

    protected ?float $capturedTimeout = null;

    protected mixed $capturedRedirects = null;

    protected function setUp(): void
    {
        $this->history = [];
        $this->capturedTimeout = null;
        $this->capturedRedirects = null;
    }

    /**
     * @param list<Response|callable> $responses
     */
    protected function client(
        array $responses,
        ?FenerumConfig $config = null,
        ?Sleeper $sleeper = null,
        ?Clock $clock = null,
    ): FenerumClient {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(function (callable $handler): callable {
            return function (RequestInterface $request, array $options) use ($handler): mixed {
                $this->history[] = ['request' => $request];
                $timeout = $options['timeout'] ?? null;
                $this->capturedTimeout = is_int($timeout) || is_float($timeout) ? (float) $timeout : null;
                $this->capturedRedirects = $options['allow_redirects'] ?? null;

                return $handler($request, $options);
            };
        });

        $config ??= new FenerumConfig('secret-token', 'TestApp', 'jane', 'https://fenerum.test', 3.5, 0);

        return new FenerumClient(
            $config,
            new GuzzleTransport(new Client(['handler' => $stack])),
            $sleeper ?? new UsleepSleeper(),
            $clock ?? new SystemClock(),
        );
    }

    protected function lastRequest(): RequestInterface
    {
        $index = array_key_last($this->history);
        if ($index === null) {
            self::fail('No HTTP request was recorded.');
        }

        return $this->requestAt($index);
    }

    protected function requestAt(int $index): RequestInterface
    {
        $request = $this->history[$index]['request'] ?? null;
        if (!$request instanceof RequestInterface) {
            self::fail('Recorded history did not contain a request.');
        }

        return $request;
    }

    /**
     * @param array<mixed> $body
     * @param array<string, string> $headers
     */
    protected function jsonResponse(int $status, array $body, array $headers = []): Response
    {
        return new Response($status, $headers, json_encode($body, JSON_THROW_ON_ERROR));
    }
}
