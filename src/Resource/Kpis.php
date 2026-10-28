<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Http\RawResponse;

final class Kpis extends Resource
{
    /**
     * GET /api/v1/kpis/
     *
     * List all available KPI types, and the dimensions the organization can segment and filter them
     * by.
     * The response schema is not published. Decoded JSON is returned as an array, or null when the
     * body is empty. Documented success status: HTTP 200.
     *
     * @return array<mixed>|null
     */
    public function list(): ?array
    {
        return $this->call('GET', $this->path('kpis'), [], null);
    }

    /**
     * GET /api/v1/kpis/{id}/
     *
     * Retrieve KPI data for a specific KPI type using GET with query params.
     * Response schema: KPIResponse. Optional query parameters: breakdown, filter, aggregates.
     * Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function get(string $id, array $query = []): array
    {
        return $this->object('GET', $this->path('kpis', $id), $query, null);
    }

    /**
     * POST /api/v1/kpis/{id}/
     *
     * Query KPI data using POST with JSON body.
     * Response schema: KPIResponse. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function query(string $id, array $payload = []): array
    {
        return $this->object('POST', $this->path('kpis', $id), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/kpis/{id}/export/
     *
     * ViewSet for fetching KPI data with optional breakdowns and filters.
     * The response is described as an XLS file and has no JSON schema. The body is returned raw.
     * Optional query parameters: breakdown, filter, aggregates. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function export(string $id, array $query = []): RawResponse
    {
        return $this->raw('GET', $this->path('kpis', $id, 'export'), $query);
    }
}
