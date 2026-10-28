<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class Activities extends Resource
{
    /**
     * GET /api/v1/activities/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * account, uuid, subscription_uuid, type, mrr_change_type, is_manual, deleted, future, since,
     * use_activity_date, activity_type, created_date__lte, created_date__org_tz_lte,
     * created_date__gte, created_date__org_tz_gte, updated_date__lte, updated_date__gte,
     * activity_date__lte, activity_date__org_tz_lte, activity_date__gte, activity_date__org_tz_gte,
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('activities'), $query);
    }

    /**
     * POST /api/v1/activities/
     *
     * Create a Manual Activity
     * Response schema: ManualActivityWrite. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('activities'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/activities/account/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * account, uuid, subscription_uuid, is_manual, future, type, churn_reason, was_customer_on,
     * created_date__lte, created_date__org_tz_lte, created_date__gte, created_date__org_tz_gte,
     * date__lte, date__org_tz_lte, date__gte, date__org_tz_gte,
     * account_first_subscription_start_date__lte, account_first_subscription_start_date__org_tz_lte,
     * account_first_subscription_start_date__gte, account_first_subscription_start_date__org_tz_gte,
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function account(array $query = []): Page
    {
        return $this->page($this->path('activities', 'account'), $query);
    }

    /**
     * POST /api/v1/activities/account/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function accountExport(array $payload = []): array
    {
        return $this->object('POST', $this->path('activities', 'account', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/activities/account/{uuid}/
     *
     * Response schema: AccountActivity. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function getAccount(string $uuid): array
    {
        return $this->object('GET', $this->path('activities', 'account', $uuid), [], null);
    }

    /**
     * DELETE /api/v1/activities/delete/
     *
     * Delete multiple Manual Activities matching filtering params
     * Filter names are not published. Query values are forwarded unchanged. Documented success status:
     * HTTP 204.
     *
     * @param array<string, scalar|null> $query
     */
    public function deleteMatching(array $query = []): void
    {
        $this->nothing('DELETE', $this->path('activities', 'delete'), $query);
    }

    /**
     * POST /api/v1/activities/export/
     *
     * The published body schema is Activity, the parent serializer, while the response is ExportJob.
     * No action-specific fields are documented separately. The default body is an empty object; a
     * payload is forwarded when you pass one. Response schema: ExportJob. Documented success status:
     * HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('activities', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * DELETE /api/v1/activities/{uuid}/
     *
     * Documented success status: HTTP 204.
     */
    public function delete(string $uuid): void
    {
        $this->nothing('DELETE', $this->path('activities', $uuid), []);
    }

    /**
     * GET /api/v1/activities/{uuid}/
     *
     * Response schema: Activity. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('activities', $uuid), [], null);
    }

    /**
     * PATCH /api/v1/activities/{uuid}/
     *
     * Update a Manual Activity
     * Response schema: ManualActivityWrite. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload = []): array
    {
        return $this->object('PATCH', $this->path('activities', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * PUT /api/v1/activities/{uuid}/
     *
     * Update a Manual Activity
     * Response schema: ManualActivityWrite. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload = []): array
    {
        return $this->object('PUT', $this->path('activities', $uuid), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
