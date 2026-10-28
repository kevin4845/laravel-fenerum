<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class PricingSimulator extends Resource
{
    /**
     * POST /api/v1/pricing-simulator/simulate/
     *
     * Response schema: PricingSimulationRequest. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function simulate(array $payload = []): array
    {
        return $this->object('POST', $this->path('pricing-simulator', 'simulate'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
