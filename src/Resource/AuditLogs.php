<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class AuditLogs extends Resource
{
    /**
     * GET /api/v1/audit-logs/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * entry_type, user, account, action_time__lte, action_time__org_tz_lte, action_time__gte,
     * action_time__org_tz_gte, ordering, search, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('audit-logs'), $query);
    }

    /**
     * POST /api/v1/audit-logs/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @return array<string, mixed>
     */
    public function export(): array
    {
        return $this->object('POST', $this->path('audit-logs', 'export'), [], null);
    }

    /**
     * GET /api/v1/audit-logs/{uuid}/
     *
     * Response schema: AuditLogEntry. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('audit-logs', $uuid), [], null);
    }
}
