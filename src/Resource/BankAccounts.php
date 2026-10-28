<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class BankAccounts extends Resource
{
    /**
     * GET /api/v1/bank-accounts/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('bank-accounts'), $query);
    }

    /**
     * GET /api/v1/bank-accounts/cash-balance/
     *
     * Response schema: CashBalance. Optional query parameters: page, page_size. Documented success
     * status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function cashBalance(array $query = []): array
    {
        return $this->object('GET', $this->path('bank-accounts', 'cash-balance'), $query, null);
    }

    /**
     * GET /api/v1/bank-accounts/cash-flow/
     *
     * This endpoint will return an average of all bank account lines summarized in the organization's
     * default currency for the last three whole months. It only includes bank account lines from bank
     * accounts that are active and not hidden. Amounts are converted to the organization's default
     * currency using the exchange rate for the current day.
     * Response schema: CashFlow. Optional query parameters: page, page_size. Documented success
     * status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function cashFlow(array $query = []): array
    {
        return $this->object('GET', $this->path('bank-accounts', 'cash-flow'), $query, null);
    }

    /**
     * GET /api/v1/bank-accounts/{id}/
     *
     * Response schema: BankAccount. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('bank-accounts', $id), [], null);
    }
}
