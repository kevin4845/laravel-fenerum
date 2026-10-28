<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class ChurnReasons extends Resource
{
    /**
     * GET /api/v1/churn-reasons/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * is_active, ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('churn-reasons'), $query);
    }

    /**
     * POST /api/v1/churn-reasons/
     *
     * Required fields: name. Response schema: FullChurnReason. Documented success status: HTTP 201.
     *
     * @return array<string, mixed>
     */
    public function create(string $name, ?bool $isActive = null): array
    {
        $payload = [];
        $payload['name'] = $name;
        if ($isActive !== null) {
            $payload['is_active'] = $isActive;
        }
        return $this->object('POST', $this->path('churn-reasons'), [], $payload);
    }

    /**
     * DELETE /api/v1/churn-reasons/{uuid}/
     *
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('churn-reasons', $uuid), []);
    }

    /**
     * GET /api/v1/churn-reasons/{uuid}/
     *
     * Response schema: FullChurnReason. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('churn-reasons', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/churn-reasons/{uuid}/
     *
     * Required fields: name. Response schema: FullChurnReason. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function patch(string $uuid, string $name, ?bool $isActive = null): array
    {
        $payload = [];
        $payload['name'] = $name;
        if ($isActive !== null) {
            $payload['is_active'] = $isActive;
        }
        return $this->object('PATCH', $this->path('churn-reasons', $uuid), [], $payload);
    }

    /**
     * PUT /api/v1/churn-reasons/{uuid}/
     *
     * Required fields: name. Response schema: FullChurnReason. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function put(string $uuid, string $name, ?bool $isActive = null): array
    {
        $payload = [];
        $payload['name'] = $name;
        if ($isActive !== null) {
            $payload['is_active'] = $isActive;
        }
        return $this->object('PUT', $this->path('churn-reasons', $uuid), [], $payload);
    }
}
