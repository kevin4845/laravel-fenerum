<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class EInvoiceFieldMappings extends Resource
{
    /**
     * GET /api/v1/einvoice-field-mappings/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('einvoice-field-mappings'), $query);
    }

    /**
     * GET /api/v1/einvoice-field-mappings/available-fields/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function availableFields(array $query = []): Page
    {
        return $this->page($this->path('einvoice-field-mappings', 'available-fields'), $query);
    }

    /**
     * PUT /api/v1/einvoice-field-mappings/save-mappings/
     *
     * Response schema: EInvoiceFieldMapping. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function saveMappings(array $payload = []): array
    {
        return $this->object('PUT', $this->path('einvoice-field-mappings', 'save-mappings'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
