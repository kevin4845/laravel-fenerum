<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Webhooks extends Resource
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('webhooks'), $query);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('webhooks', $uuid));
    }

    /**
     * Required by the Webhook schema: endpoint.
     * Optional documented fields: basic_auth_username, basic_auth_password, enabled, events.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->object('POST', $this->path('webhooks'), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload): array
    {
        return $this->object('PUT', $this->path('webhooks', $uuid), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload): array
    {
        return $this->object('PATCH', $this->path('webhooks', $uuid), [], $payload);
    }
}
