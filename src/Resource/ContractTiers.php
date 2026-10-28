<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class ContractTiers extends Resource
{
    /**
     * GET /api/v1/accounts/{code}/contracts/{contract_pk}/tiers/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters: page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(string $code, string $contractPk, array $query = []): Page
    {
        return $this->page($this->path('accounts', $code, 'contracts', $contractPk, 'tiers'), $query);
    }

    /**
     * POST /api/v1/accounts/{code}/contracts/{contract_pk}/tiers/
     *
     * Response schema: ContractTier. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(string $code, string $contractPk, array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', $code, 'contracts', $contractPk, 'tiers'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/
     *
     * Documented success status: HTTP 204.
     */
    public function delete(string $code, string $contractPk, string $id): void
    {
        $this->nothing('DELETE', $this->path('accounts', $code, 'contracts', $contractPk, 'tiers', $id), []);
    }

    /**
     * GET /api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/
     *
     * Response schema: ContractTier. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $code, string $contractPk, string $id): array
    {
        return $this->object('GET', $this->path('accounts', $code, 'contracts', $contractPk, 'tiers', $id), [], null);
    }

    /**
     * PATCH /api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/
     *
     * Response schema: ContractTier. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $code, string $contractPk, string $id, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('accounts', $code, 'contracts', $contractPk, 'tiers', $id), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/
     *
     * Response schema: ContractTier. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $code, string $contractPk, string $id, array $payload = []): array
    {
        return $this->object('PUT', $this->path('accounts', $code, 'contracts', $contractPk, 'tiers', $id), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
