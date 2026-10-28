<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Recipients extends Resource
{
    /**
     * GET /api/v1/recipients/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * The response uses the count, next, previous, and results page. Optional query parameters: email,
     * page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('recipients'), $query);
    }

    /**
     * POST /api/v1/recipients/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * Response schema: Recipient. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('recipients'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/recipients/export/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * RecipientExport has no writable fields. The default body is an empty object. Response schema:
     * ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('recipients', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/recipients/{uuid}/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('recipients', $uuid), []);
    }

    /**
     * GET /api/v1/recipients/{uuid}/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * Response schema: Recipient. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('recipients', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/recipients/{uuid}/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * Response schema: Recipient. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('recipients', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/recipients/{uuid}/
     *
     * Recipients are destinations where we send notifications for a given account. Each recipient may
     * ask for certain notifications only.
     * Response schema: Recipient. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('recipients', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
