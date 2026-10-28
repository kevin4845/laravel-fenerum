<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class Search extends Resource
{
    /**
     * GET /api/v1/search/
     *
     * Response schema: SearchResults. Optional query parameters: q. Documented success status: HTTP
     * 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function list(array $query = []): array
    {
        return $this->object('GET', $this->path('search'), $query, null);
    }
}
