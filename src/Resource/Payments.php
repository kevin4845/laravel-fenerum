<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Payments extends Resource
{
    /**
     * GET /api/v1/payments-list/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * payment_type, payment_status, payment_gateway, account, ordering, search, page, page_size.
     * Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('payments-list'), $query);
    }

    /**
     * POST /api/v1/payments-list/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('payments-list', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/payments-list/{id}/
     *
     * Response schema: PaymentList. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('payments-list', $id), [], null);
    }

    /**
     * GET /api/v1/payments-list/{id}/full/
     *
     * Response schema: PaymentDetail. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function getFull(string $id): array
    {
        return $this->object('GET', $this->path('payments-list', $id, 'full'), [], null);
    }
}
