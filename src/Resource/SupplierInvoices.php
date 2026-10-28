<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class SupplierInvoices extends Resource
{
    /**
     * GET /api/v1/supplier-invoices/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * currency, approved, status, pending_match, verified, document_date__lte, document_date__gte,
     * created_date__lte, created_date__org_tz_lte, created_date__gte, created_date__org_tz_gte,
     * pending_approval, pending_payment, payment_scheduled, ordering, page, page_size. Documented
     * success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('supplier-invoices'), $query);
    }

    /**
     * POST /api/v1/supplier-invoices/bulk-action/
     *
     * Response schema: BulkActionJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function bulkAction(array $payload = []): array
    {
        return $this->object('POST', $this->path('supplier-invoices', 'bulk-action'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/supplier-invoices/{uuid}/
     *
     * Response schema: SupplierInvoice. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('supplier-invoices', $uuid), [], null);
    }
}
