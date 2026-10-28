<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class DraftInvoiceLineProducts extends Resource
{
    /**
     * GET /api/v1/draft-invoice-lines/products/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for Invoice lines
     * creation using Products feature.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * account, date_from__lte, date_from__gte, date_to__lte, date_to__gte, last_modified_date__lte,
     * last_modified_date__org_tz_lte, last_modified_date__gte, last_modified_date__org_tz_gte, page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('draft-invoice-lines', 'products'), $query);
    }

    /**
     * POST /api/v1/draft-invoice-lines/products/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for Invoice lines
     * creation using Products feature.
     * Response schema: ProductDraftInvoiceLine. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('draft-invoice-lines', 'products'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/draft-invoice-lines/products/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for Invoice lines
     * creation using Products feature.
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('draft-invoice-lines', 'products', $uuid), []);
    }

    /**
     * GET /api/v1/draft-invoice-lines/products/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for Invoice lines
     * creation using Products feature.
     * Response schema: ProductDraftInvoiceLine. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('draft-invoice-lines', 'products', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/draft-invoice-lines/products/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for Invoice lines
     * creation using Products feature.
     * Response schema: ProductDraftInvoiceLine. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('draft-invoice-lines', 'products', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/draft-invoice-lines/products/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for Invoice lines
     * creation using Products feature.
     * Response schema: ProductDraftInvoiceLine. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('draft-invoice-lines', 'products', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
