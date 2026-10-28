<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class PlanCatalog extends Resource
{
    /**
     * GET /api/v1/plans-list/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: code,
     * with_code, status, last_modified_date__lte, last_modified_date__org_tz_lte,
     * last_modified_date__gte, last_modified_date__org_tz_gte, ordering, search, page, page_size.
     * Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('plans-list'), $query);
    }

    /**
     * POST /api/v1/plans-list/export/
     *
     * Required fields: name. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @return array<string, mixed>
     */
    public function export(string $name, ?string $code = null): array
    {
        $payload = [];
        $payload['name'] = $name;
        if ($code !== null) {
            $payload['code'] = $code;
        }
        return $this->object('POST', $this->path('plans-list', 'export'), [], $payload);
    }

    /**
     * GET /api/v1/plans-list/{uuid}/
     *
     * Response schema: PlanList. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('plans-list', $uuid), [], null);
    }

    /**
     * GET /api/v1/plans-list/{uuid}/full/
     *
     * Response schema: PlanDetail. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function getFull(string $uuid): array
    {
        return $this->object('GET', $this->path('plans-list', $uuid, 'full'), [], null);
    }
}
