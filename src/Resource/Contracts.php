<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Contracts extends Resource
{
    /**
     * GET /api/v1/accounts/{code}/contracts/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(string $code, array $query = []): Page
    {
        return $this->page($this->path('accounts', $code, 'contracts'), $query);
    }

    /**
     * POST /api/v1/accounts/{code}/contracts/
     *
     * Response schema: Contract. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(string $code, array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', $code, 'contracts'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/accounts/{code}/contracts/{uuid}/
     *
     * Documented success status: HTTP 204.
     */
    public function delete(string $code, string $uuid): void
    {
        $this->nothing('DELETE', $this->path('accounts', $code, 'contracts', $uuid), []);
    }

    /**
     * GET /api/v1/accounts/{code}/contracts/{uuid}/
     *
     * Response schema: Contract. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $code, string $uuid): array
    {
        return $this->object('GET', $this->path('accounts', $code, 'contracts', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/accounts/{code}/contracts/{uuid}/
     *
     * Response schema: Contract. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $code, string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('accounts', $code, 'contracts', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/accounts/{code}/contracts/{uuid}/
     *
     * Response schema: Contract. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $code, string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('accounts', $code, 'contracts', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
