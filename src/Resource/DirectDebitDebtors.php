<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class DirectDebitDebtors extends Resource
{
    /**
     * GET /api/v1/directdebitdebtors/
     *
     * Direct debit solutions for your accounts.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * account, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('directdebitdebtors'), $query);
    }

    /**
     * POST /api/v1/directdebitdebtors/
     *
     * Direct debit solutions for your accounts.
     * Response schema: DirectDebitDebtor. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('directdebitdebtors'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/directdebitdebtors/generate/
     *
     * Returns a possible customer number for Betalingsservice debtor.
     * Required fields: gateway. Response schema: DirectDebitIDGeneration. Documented success status:
     * HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function generate(string $gateway): array
    {
        $payload = [];
        $payload['gateway'] = $gateway;
        return $this->object('POST', $this->path('directdebitdebtors', 'generate'), [], $payload);
    }

    /**
     * GET /api/v1/directdebitdebtors/{uuid}/
     *
     * Direct debit solutions for your accounts.
     * Response schema: DirectDebitDebtor. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('directdebitdebtors', $uuid), [], null);
    }

    /**
     * POST /api/v1/directdebitdebtors/{uuid}/disable/
     *
     * Direct debit solutions for your accounts.
     * DisableDirectDebitDebtor has no writable fields. The default body is an empty object. Documented
     * success status: HTTP 204.
     *
     * @param array<string, mixed> $payload
     */
    public function disable(string $uuid, array $payload = []): void
    {
        $this->nothing('POST', $this->path('directdebitdebtors', $uuid, 'disable'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
