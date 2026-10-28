<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class BusinessEntities extends Resource
{
    /**
     * GET /api/v1/business-entities/
     *
     * A business entity is a legal entity that can issue invoices and handle transactions for specific
     * countries. Each organization must have exactly one default business entity.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * is_default, company_name, short_name, registration_number, vat_number, country, page, page_size.
     * Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('business-entities'), $query);
    }

    /**
     * POST /api/v1/business-entities/
     *
     * If the BUSINESS_ENTITIES feature flag is not enabled, only allow creating one business entity
     * per organization.
     * Response schema: BusinessEntity. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('business-entities'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/business-entities/{uuid}/
     *
     * If the BUSINESS_ENTITIES feature flag is not enabled, do not allow deleting any business
     * entities.
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('business-entities', $uuid), []);
    }

    /**
     * GET /api/v1/business-entities/{uuid}/
     *
     * A business entity is a legal entity that can issue invoices and handle transactions for specific
     * countries. Each organization must have exactly one default business entity.
     * Response schema: BusinessEntity. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('business-entities', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/business-entities/{uuid}/
     *
     * A business entity is a legal entity that can issue invoices and handle transactions for specific
     * countries. Each organization must have exactly one default business entity.
     * Response schema: BusinessEntity. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('business-entities', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/business-entities/{uuid}/
     *
     * If the BUSINESS_ENTITIES feature flag is not enabled, only allow updating the default business
     * entity.
     * Response schema: BusinessEntity. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('business-entities', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
