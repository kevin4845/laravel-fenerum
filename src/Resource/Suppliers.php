<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class Suppliers extends Resource
{
    /**
     * POST /api/v1/suppliers/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('suppliers', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
