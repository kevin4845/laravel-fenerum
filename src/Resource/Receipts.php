<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Receipts extends Resource
{
    /**
     * GET /api/v1/receipts/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * currency, status, pending_match, verified, document_date__lte, document_date__gte,
     * created_date__lte, created_date__org_tz_lte, created_date__gte, created_date__org_tz_gte,
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('receipts'), $query);
    }

    /**
     * POST /api/v1/receipts/bulk-action/
     *
     * Response schema: BulkActionJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function bulkAction(array $payload = []): array
    {
        return $this->object('POST', $this->path('receipts', 'bulk-action'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/receipts/{uuid}/
     *
     * Response schema: Receipt. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('receipts', $uuid), [], null);
    }
}
