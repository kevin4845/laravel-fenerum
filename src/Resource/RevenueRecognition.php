<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class RevenueRecognition extends Resource
{
    /**
     * POST /api/v1/revenue-recognition-algorithms/examples/
     *
     * The response schema is not published. Decoded JSON is returned as an array, or null when the
     * body is empty. Documented success status: HTTP 201.
     *
     * @return array<mixed>|null
     */
    public function examples(): ?array
    {
        return $this->call('POST', $this->path('revenue-recognition-algorithms', 'examples'), [], null);
    }

    /**
     * GET /api/v1/revenue-recognition-algorithms/settings/
     *
     * The response schema is not published. Decoded JSON is returned as an array, or null when the
     * body is empty. Optional query parameters: page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<mixed>|null
     */
    public function settings(array $query = []): ?array
    {
        return $this->call('GET', $this->path('revenue-recognition-algorithms', 'settings'), $query, null);
    }
}
