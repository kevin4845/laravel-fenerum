<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Enum\LookupField;

final class PlanTerms extends Resource
{
    /**
     * Plan term collections are a JSON array, not a paginated envelope.
     * The documented query key is code. Lookup-Field selects how the plan identifier is read.
     *
     * @param array<string, scalar|null> $query
     * @return list<array<string, mixed>>
     */
    public function list(string $planIdentifier, array $query = [], ?LookupField $lookupField = null): array
    {
        return $this->collection(
            $this->path('plans', $planIdentifier, 'terms'),
            $query,
            $this->lookup($lookupField),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function get(
        string $planIdentifier,
        string $identifier,
        ?LookupField $lookupField = null,
    ): array {
        return $this->object(
            'GET',
            $this->path('plans', $planIdentifier, 'terms', $identifier),
            [],
            null,
            $this->lookup($lookupField),
        );
    }

    /**
     * Required by the PlanTerms schema: interval_type, interval_count, price, currency.
     * Lookup-Field applies to the parent plan identifier.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(string $planIdentifier, array $payload, ?LookupField $lookupField = null): array
    {
        return $this->object(
            'POST',
            $this->path('plans', $planIdentifier, 'terms'),
            [],
            $payload,
            $this->lookup($lookupField),
        );
    }

    /**
     * PATCH uses PlanTermsUpdate. backwards_charging is read-only on that schema.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(
        string $planIdentifier,
        string $identifier,
        array $payload,
        ?LookupField $lookupField = null,
    ): array {
        return $this->object(
            'PATCH',
            $this->path('plans', $planIdentifier, 'terms', $identifier),
            [],
            $payload,
            $this->lookup($lookupField),
        );
    }

    /**
     * Lookup-Field is not listed on this operation.
     */
    public function delete(string $planIdentifier, string $identifier): void
    {
        $this->nothing('DELETE', $this->path('plans', $planIdentifier, 'terms', $identifier));
    }

    /**
     * Lookup-Field is not listed on this operation. See Plans::activate() for the body uncertainty.
     */
    public function activate(string $planIdentifier, string $identifier): void
    {
        $this->nothing('POST', $this->path('plans', $planIdentifier, 'terms', $identifier, 'activate'));
    }

    public function deactivate(string $planIdentifier, string $identifier): void
    {
        $this->nothing('POST', $this->path('plans', $planIdentifier, 'terms', $identifier, 'deactivate'));
    }
}
