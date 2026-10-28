<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class AccountBudgets extends Resource
{
    /**
     * GET /api/v1/account-budgets/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * budget_id, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('account-budgets'), $query);
    }

    /**
     * GET /api/v1/account-budgets/{id}/
     *
     * Response schema: ERPAccountBudget. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('account-budgets', $id), [], null);
    }
}
