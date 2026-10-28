<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class DraftInvoiceLines extends Resource
{
    /**
     * GET /api/v1/draftinvoicelines/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for custom (without
     * product feature) Invoice lines interaction.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * account, date_from__lte, date_from__gte, date_to__lte, date_to__gte, last_modified_date__lte,
     * last_modified_date__org_tz_lte, last_modified_date__gte, last_modified_date__org_tz_gte, page,
     * page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('draftinvoicelines'), $query);
    }

    /**
     * POST /api/v1/draftinvoicelines/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for custom (without
     * product feature) Invoice lines interaction.
     * Response schema: DraftInvoiceLine. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('draftinvoicelines'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/draftinvoicelines/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for custom (without
     * product feature) Invoice lines interaction.
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('draftinvoicelines', $uuid), []);
    }

    /**
     * GET /api/v1/draftinvoicelines/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for custom (without
     * product feature) Invoice lines interaction.
     * Response schema: DraftInvoiceLine. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('draftinvoicelines', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/draftinvoicelines/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for custom (without
     * product feature) Invoice lines interaction.
     * Response schema: DraftInvoiceLine. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('draftinvoicelines', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/draftinvoicelines/{uuid}/
     *
     * Invoice lines that can be used to create a standalone invoice or added to an
     * automatically-generated subscription renewal invoice. This endpoint allows for custom (without
     * product feature) Invoice lines interaction.
     * Response schema: DraftInvoiceLine. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('draftinvoicelines', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
