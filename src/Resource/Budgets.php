<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Budgets extends Resource
{
    /**
     * GET /api/v1/budgets-list/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * active, ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('budgets-list'), $query);
    }
}
