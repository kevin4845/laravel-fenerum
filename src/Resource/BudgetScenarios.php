<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class BudgetScenarios extends Resource
{
    /**
     * GET /api/v1/budget-scenarios/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('budget-scenarios'), $query);
    }

    /**
     * GET /api/v1/budget-scenarios/{uuid}/
     *
     * Response schema: ScenarioList. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('budget-scenarios', $uuid), [], null);
    }
}
