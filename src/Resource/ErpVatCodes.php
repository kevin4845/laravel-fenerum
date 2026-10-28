<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class ErpVatCodes extends Resource
{
    /**
     * GET /api/v1/erp-vat-codes/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('erp-vat-codes'), $query);
    }

    /**
     * POST /api/v1/erp-vat-codes/import/
     *
     * Import VAT codes from ERP system.
     * Required fields: vat_code, name. Response schema: ERPVATCode. Documented success status: HTTP
     * 201.
     *
     * @return array<string, mixed>
     */
    public function import(string $vatCode, string $name, ?string $accountNumber = null, ?string $contraAccountNumber = null): array
    {
        $payload = [];
        $payload['vat_code'] = $vatCode;
        $payload['name'] = $name;
        if ($accountNumber !== null) {
            $payload['account_number'] = $accountNumber;
        }
        if ($contraAccountNumber !== null) {
            $payload['contra_account_number'] = $contraAccountNumber;
        }
        return $this->object('POST', $this->path('erp-vat-codes', 'import'), [], $payload);
    }

    /**
     * GET /api/v1/erp-vat-codes/{uuid}/
     *
     * Response schema: ERPVATCode. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('erp-vat-codes', $uuid), [], null);
    }
}
