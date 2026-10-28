<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class RevenueGroupForms extends Resource
{
    /**
     * POST /api/v1/revenue-groups-form/
     *
     * Response schema: RevenueGroupForm. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('revenue-groups-form'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/revenue-groups-form/{id}/
     *
     * Documented success status: HTTP 204.
     */
    public function delete(string $id): void
    {
        $this->nothing('DELETE', $this->path('revenue-groups-form', $id), []);
    }

    /**
     * GET /api/v1/revenue-groups-form/{id}/
     *
     * Response schema: RevenueGroupForm. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('revenue-groups-form', $id), [], null);
    }

    /**
     * PATCH /api/v1/revenue-groups-form/{id}/
     *
     * Response schema: RevenueGroupForm. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $id, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('revenue-groups-form', $id), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/revenue-groups-form/{revenue_group_id}/countries/
     *
     * Required fields: country, revenue_account. Response schema: RevenueGroupCountryForm. Documented
     * success status: HTTP 201.
     *
     * @return array<string, mixed>
     */
    public function createCountries(string $revenueGroupId, string $country, string $revenueAccount): array
    {
        $payload = [];
        $payload['country'] = $country;
        $payload['revenue_account'] = $revenueAccount;
        return $this->object('POST', $this->path('revenue-groups-form', $revenueGroupId, 'countries'), [], $payload);
    }

    /**
     * DELETE /api/v1/revenue-groups-form/{revenue_group_id}/countries/{id}/
     *
     * Documented success status: HTTP 204.
     */
    public function deleteCountries(string $revenueGroupId, string $id): void
    {
        $this->nothing('DELETE', $this->path('revenue-groups-form', $revenueGroupId, 'countries', $id), []);
    }

    /**
     * GET /api/v1/revenue-groups-form/{revenue_group_id}/countries/{id}/
     *
     * Response schema: RevenueGroupCountryForm. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function getCountries(string $revenueGroupId, string $id): array
    {
        return $this->object('GET', $this->path('revenue-groups-form', $revenueGroupId, 'countries', $id), [], null);
    }

    /**
     * PATCH /api/v1/revenue-groups-form/{revenue_group_id}/countries/{id}/
     *
     * Required fields: country, revenue_account. Response schema: RevenueGroupCountryForm. Documented
     * success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function updateCountries(string $revenueGroupId, string $id, string $country, string $revenueAccount): array
    {
        $payload = [];
        $payload['country'] = $country;
        $payload['revenue_account'] = $revenueAccount;
        return $this->object('PATCH', $this->path('revenue-groups-form', $revenueGroupId, 'countries', $id), [], $payload);
    }
}
