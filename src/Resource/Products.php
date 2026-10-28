<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Products extends Resource
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('products'), $query);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('products', $uuid));
    }

    /**
     * Required by the Product schema: name, revenue_group, product_type.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->object('POST', $this->path('products'), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload): array
    {
        return $this->object('PUT', $this->path('products', $uuid), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload): array
    {
        return $this->object('PATCH', $this->path('products', $uuid), [], $payload);
    }

    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('products', $uuid));
    }


    /**
     * POST /api/v1/products/export-pricing/
     *
     * The published body schema is Product, the parent serializer, while the response is ExportJob. No
     * action-specific fields are documented separately. The default body is an empty object; a payload
     * is forwarded when you pass one. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function exportPricing(array $payload = []): array
    {
        return $this->object('POST', $this->path('products', 'export-pricing'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/products/export/
     *
     * The published body schema is Product, the parent serializer, while the response is ExportJob. No
     * action-specific fields are documented separately. The default body is an empty object; a payload
     * is forwarded when you pass one. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('products', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
