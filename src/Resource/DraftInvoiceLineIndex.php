<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class DraftInvoiceLineIndex extends Resource
{
    /**
     * GET /api/v1/draft-invoice-lines-list/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * include_in_next_renewal_invoice, currency, account, ordering, search, page, page_size.
     * Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('draft-invoice-lines-list'), $query);
    }

    /**
     * POST /api/v1/draft-invoice-lines-list/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('draft-invoice-lines-list', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/draft-invoice-lines-list/{uuid}/
     *
     * Response schema: DraftInvoiceLineList. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('draft-invoice-lines-list', $uuid), [], null);
    }
}
