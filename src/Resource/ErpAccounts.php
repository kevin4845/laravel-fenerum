<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class ErpAccounts extends Resource
{
    /**
     * GET /api/v1/erp-accounts/
     *
     * Response schema: ERPAccount. Documented success status: HTTP 200.
     *
     * @return list<array<string, mixed>>
     */
    public function list(): array
    {
        return $this->objectList('GET', $this->path('erp-accounts'), [], null);
    }

    /**
     * POST /api/v1/erp-accounts/import/
     *
     * Import chart of accounts from ERP system. This will import VAT codes first, then accounts.
     * Response schema: ERPAccount. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function import(array $payload = []): array
    {
        return $this->object('POST', $this->path('erp-accounts', 'import'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/erp-accounts/{uuid}/
     *
     * Response schema: ERPAccount. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('erp-accounts', $uuid), [], null);
    }
}
