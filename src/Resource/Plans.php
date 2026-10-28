<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Enum\LookupField;
use Kevin4845\LaravelFenerum\Support\Page;

final class Plans extends Resource
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('plans'), $query);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $identifier, ?LookupField $lookupField = null): array
    {
        return $this->object(
            'GET',
            $this->path('plans', $identifier),
            [],
            null,
            $this->lookup($lookupField),
        );
    }

    /**
     * Required by the Plan schema: name, vat_type.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->object('POST', $this->path('plans'), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $identifier, array $payload, ?LookupField $lookupField = null): array
    {
        return $this->object(
            'PATCH',
            $this->path('plans', $identifier),
            [],
            $payload,
            $this->lookup($lookupField),
        );
    }

    public function delete(string $identifier): void
    {
        $this->nothing('DELETE', $this->path('plans', $identifier));
    }

    /**
     * POST /api/v1/plans/{identifier}/activate/
     *
     * The OpenAPI operation marks a Plan body as required, but the description does not
     * document request fields and the success response is empty. No JSON body is sent.
     * Lookup-Field is not listed on this operation.
     */
    public function activate(string $identifier): void
    {
        $this->nothing('POST', $this->path('plans', $identifier, 'activate'));
    }

    /**
     * See activate() for the request-body uncertainty.
     */
    public function deactivate(string $identifier): void
    {
        $this->nothing('POST', $this->path('plans', $identifier, 'deactivate'));
    }


    /**
     * POST /api/v1/plans/calculate/
     *
     * Required fields: account_country_code, terms, quantity. Response schema: CalculatePlanTerms.
     * Documented success status: HTTP 201.
     *
     * @return array<string, mixed>
     */
    public function calculate(string $accountCountryCode, string $terms, int $quantity, ?string $accountCode = null): array
    {
        $payload = [];
        if ($accountCode !== null) {
            $payload['account_code'] = $accountCode;
        }
        $payload['account_country_code'] = $accountCountryCode;
        $payload['terms'] = $terms;
        $payload['quantity'] = $quantity;
        return $this->object('POST', $this->path('plans', 'calculate'), [], $payload);
    }
}
