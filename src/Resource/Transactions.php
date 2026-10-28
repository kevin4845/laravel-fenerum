<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Transactions extends Resource
{
    /**
     * GET /api/v1/transactions/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * method, type, currency, import_source, invoice, account, account_uuid, is_loss, is_settlement,
     * date__lte, date__gte, created_date__lte, created_date__org_tz_lte, created_date__gte,
     * created_date__org_tz_gte, ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('transactions'), $query);
    }

    /**
     * POST /api/v1/transactions/
     *
     * Response schema: Transaction. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('transactions'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/transactions/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('transactions', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/transactions/{uuid}/
     *
     * Response schema: Transaction. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('transactions', $uuid), [], null);
    }

    /**
     * GET /api/v1/transactions/{uuid}/full/
     *
     * Response schema: TransactionDetail. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function getFull(string $uuid): array
    {
        return $this->object('GET', $this->path('transactions', $uuid, 'full'), [], null);
    }
}
