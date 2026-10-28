<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

interface HttpTransport
{
    /**
     * @param array<string, string|int|float> $query
     * @param array<mixed>|\stdClass|null $json
     * @param array<string, string> $headers
     */
    public function send(
        string $method,
        string $uri,
        array $query,
        array|\stdClass|null $json,
        array $headers,
        float $timeout,
    ): HttpResponse;
}
