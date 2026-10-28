<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class RevenueGroups extends Resource
{
    /**
     * GET /api/v1/revenuegroup/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('revenuegroup'), $query);
    }

    /**
     * POST /api/v1/revenuegroup/
     *
     * Response schema: RevenueGroup. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('revenuegroup'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/revenuegroup/{id}/
     *
     * Documented success status: HTTP 204.
     */
    public function delete(string $id): void
    {
        $this->nothing('DELETE', $this->path('revenuegroup', $id), []);
    }

    /**
     * GET /api/v1/revenuegroup/{id}/
     *
     * Response schema: RevenueGroup. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('revenuegroup', $id), [], null);
    }

    /**
     * PATCH /api/v1/revenuegroup/{id}/
     *
     * Response schema: RevenueGroup. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $id, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('revenuegroup', $id), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/revenuegroup/{id}/
     *
     * Response schema: RevenueGroup. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $id, array $payload = []): array
    {
        return $this->object('PUT', $this->path('revenuegroup', $id), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
