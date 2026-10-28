<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class RevenueGroupIndex extends Resource
{
    /**
     * GET /api/v1/revenue-groups-list/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: name,
     * ordering, search, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('revenue-groups-list'), $query);
    }

    /**
     * POST /api/v1/revenue-groups-list/export/
     *
     * Required fields: name. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @return array<string, mixed>
     */
    public function export(string $name): array
    {
        $payload = [];
        $payload['name'] = $name;
        return $this->object('POST', $this->path('revenue-groups-list', 'export'), [], $payload);
    }

    /**
     * GET /api/v1/revenue-groups-list/{id}/
     *
     * Response schema: RevenueGroupList. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('revenue-groups-list', $id), [], null);
    }

    /**
     * GET /api/v1/revenue-groups-list/{id}/full/
     *
     * Response schema: RevenueGroupDetail. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function getFull(string $id): array
    {
        return $this->object('GET', $this->path('revenue-groups-list', $id, 'full'), [], null);
    }
}
