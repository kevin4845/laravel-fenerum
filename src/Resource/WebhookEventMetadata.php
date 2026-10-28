<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class WebhookEventMetadata extends Resource
{
    /**
     * GET /api/v1/webhook-events-metadata/
     *
     * A webhook event metadata entry represents a webhook-event that was created after a webhook was
     * triggered.
     * The response uses the count, next, previous, and results page. Optional query parameters: uuid,
     * event, success, retry_enabled, webhook_uuid, created_date__lte, created_date__org_tz_lte,
     * created_date__gte, created_date__org_tz_gte, last_modified_date__lte,
     * last_modified_date__org_tz_lte, last_modified_date__gte, last_modified_date__org_tz_gte,
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('webhook-events-metadata'), $query);
    }

    /**
     * GET /api/v1/webhook-events-metadata/{uuid}/
     *
     * A webhook event metadata entry represents a webhook-event that was created after a webhook was
     * triggered.
     * Response schema: WebhookEventMetadata. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('webhook-events-metadata', $uuid), [], null);
    }
}
