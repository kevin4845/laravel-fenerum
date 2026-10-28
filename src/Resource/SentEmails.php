<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class SentEmails extends Resource
{
    /**
     * GET /api/v1/sent-emails/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * status, date_created__lte, date_created__org_tz_lte, date_created__gte,
     * date_created__org_tz_gte, ordering, search, page, page_size. Documented success status: HTTP
     * 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('sent-emails'), $query);
    }

    /**
     * POST /api/v1/sent-emails/bulk-action/
     *
     * The published body schema is SentEmail, the parent serializer, while the response is
     * BulkActionJob. No action-specific fields are documented separately. The default body is an empty
     * object; a payload is forwarded when you pass one. Response schema: BulkActionJob. Documented
     * success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function bulkAction(array $payload = []): array
    {
        return $this->object('POST', $this->path('sent-emails', 'bulk-action'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/sent-emails/export/
     *
     * The published body schema is SentEmail, the parent serializer, while the response is ExportJob.
     * No action-specific fields are documented separately. The default body is an empty object; a
     * payload is forwarded when you pass one. Response schema: ExportJob. Documented success status:
     * HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('sent-emails', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/sent-emails/{id}/
     *
     * Response schema: SentEmail. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('sent-emails', $id), [], null);
    }

    /**
     * POST /api/v1/sent-emails/{id}/resend/
     *
     * The response schema is not published. Decoded JSON is returned as an array, or null when the
     * body is empty. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<mixed>|null
     */
    public function resend(string $id, array $payload = []): ?array
    {
        return $this->call('POST', $this->path('sent-emails', $id, 'resend'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
