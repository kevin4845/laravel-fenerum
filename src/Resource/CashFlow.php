<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class CashFlow extends Resource
{
    /**
     * GET /api/v1/cash-flow/{month}/
     *
     * Response schema: CashFlowEntry. Optional query parameters: revenue_base. Documented success
     * status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function get(string $month, array $query = []): array
    {
        return $this->object('GET', $this->path('cash-flow', $month), $query, null);
    }
}
