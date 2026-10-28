<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Reports extends Resource
{
    /**
     * GET /api/v1/reports/
     *
     * ViewSet for managing Reports and their Widgets. Reports are collections of chart widgets that
     * display KPIs. Widgets are managed as nested objects within the report. To get actual KPI data
     * for a widget, use the /api/v1/kpis/{kpi_type}/ endpoint.
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('reports'), $query);
    }

    /**
     * POST /api/v1/reports/
     *
     * ViewSet for managing Reports and their Widgets. Reports are collections of chart widgets that
     * display KPIs. Widgets are managed as nested objects within the report. To get actual KPI data
     * for a widget, use the /api/v1/kpis/{kpi_type}/ endpoint.
     * Response schema: Report. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('reports'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/reports/{uuid}/
     *
     * ViewSet for managing Reports and their Widgets. Reports are collections of chart widgets that
     * display KPIs. Widgets are managed as nested objects within the report. To get actual KPI data
     * for a widget, use the /api/v1/kpis/{kpi_type}/ endpoint.
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('reports', $uuid), []);
    }

    /**
     * GET /api/v1/reports/{uuid}/
     *
     * ViewSet for managing Reports and their Widgets. Reports are collections of chart widgets that
     * display KPIs. Widgets are managed as nested objects within the report. To get actual KPI data
     * for a widget, use the /api/v1/kpis/{kpi_type}/ endpoint.
     * Response schema: Report. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('reports', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/reports/{uuid}/
     *
     * ViewSet for managing Reports and their Widgets. Reports are collections of chart widgets that
     * display KPIs. Widgets are managed as nested objects within the report. To get actual KPI data
     * for a widget, use the /api/v1/kpis/{kpi_type}/ endpoint.
     * Response schema: Report. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('reports', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/reports/{uuid}/
     *
     * ViewSet for managing Reports and their Widgets. Reports are collections of chart widgets that
     * display KPIs. Widgets are managed as nested objects within the report. To get actual KPI data
     * for a widget, use the /api/v1/kpis/{kpi_type}/ endpoint.
     * Response schema: Report. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('reports', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
