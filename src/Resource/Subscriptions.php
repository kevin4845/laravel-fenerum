<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use InvalidArgumentException;
use Kevin4845\LaravelFenerum\Enum\CancellationType;
use Kevin4845\LaravelFenerum\Enum\SubscriptionChangeType;
use Kevin4845\LaravelFenerum\Support\Page;

final class Subscriptions extends Resource
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('subscriptions'), $query);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('subscriptions', $uuid));
    }

    /**
     * Create one subscription without cancelling other subscriptions on the account.
     *
     * Required by the CreateSubscription schema: account, terms, collection_method.
     * Do not combine this with Accounts::subscribe().
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->object('POST', $this->path('subscriptions'), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $uuid, array $payload): array
    {
        return $this->object('PUT', $this->path('subscriptions', $uuid), [], $payload);
    }

    /**
     * Partial update. Fenerum applies billing changes; this client does not price them.
     *
     * The published UpdateSubscription schema has no required fields. change_type selects
     * immediate, at_specified_time, at_renewal, or retroactive application. The boolean
     * prorate field is documented as deprecated in favour of change_type.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $uuid, array $payload): array
    {
        return $this->object('PATCH', $this->path('subscriptions', $uuid), [], $payload);
    }

    /**
     * Schedule a quantity change through Fenerum.
     *
     * Pass other documented UpdateSubscription fields in $payload, for example terms,
     * prorate_date (date-time, required by the field description when change type is
     * at_specified_time or retroactive), change_renewal, or early_renewal.
     * The write schema types quantity as an integer. Webhook examples also show fractional
     * quantities, so a finite float is forwarded unchanged.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function changeQuantity(
        string $uuid,
        int|float $quantity,
        SubscriptionChangeType $changeType,
        array $payload = [],
    ): array {
        if (is_float($quantity) && !is_finite($quantity)) {
            throw new InvalidArgumentException('Quantity must be a finite number.');
        }

        $payload['quantity'] = $quantity;
        $payload['change_type'] = $changeType->value;

        return $this->patch($uuid, $payload);
    }

    /**
     * Change plan terms through the subscription update.
     *
     * Fenerum applies the change. Pass prorate_date when the change type requires it.
     * This method does not price the change.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function changeTerms(
        string $uuid,
        string $terms,
        SubscriptionChangeType $changeType,
        array $payload = [],
    ): array {
        $payload['terms'] = $terms;
        $payload['change_type'] = $changeType->value;

        return $this->patch($uuid, $payload);
    }

    /**
     * Cancel a subscription. Fenerum decides the invoice or credit note.
     *
     * A subscription cannot be cancelled when its status is expired or not_renewing, or when
     * an active contract still covers the plan terms. cancel_renewal is an integer from 0
     * through 23 and is documented for cancellation_type at_renewal. cancel_date is a date-time.
     *
     * The subscription article names full_refund, at_renewal, and today. The OpenAPI schema
     * also lists at_specified_time and cancel_invalid. The charging-options article describes
     * financial outcomes without mapping those names onto the enum.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function cancel(string $uuid, CancellationType $cancellationType, array $payload = []): array
    {
        if (array_key_exists('cancel_renewal', $payload)) {
            $renewal = $payload['cancel_renewal'];
            if (!is_int($renewal) || $renewal < 0 || $renewal > 23) {
                throw new InvalidArgumentException('cancel_renewal must be an integer from 0 through 23.');
            }
        }

        $payload['cancellation_type'] = $cancellationType->value;

        return $this->object('POST', $this->path('subscriptions', $uuid, 'cancel'), [], $payload);
    }

    /**
     * Reactivate a subscription whose status is not_renewing or not_starting.
     *
     * @return array<string, mixed>
     */
    public function reactivate(string $uuid): array
    {
        return $this->object('POST', $this->path('subscriptions', $uuid, 'reactivate'));
    }

    /**
     * @return array<string, mixed>
     */
    public function renewNow(string $uuid): array
    {
        return $this->object('POST', $this->path('subscriptions', $uuid, 'renew-now'));
    }


    /**
     * GET /api/v1/subscriptions/activity-months/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function activityMonths(array $query = []): Page
    {
        return $this->page($this->path('subscriptions', 'activity-months'), $query);
    }

    /**
     * POST /api/v1/subscriptions/bulk-action/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * The published body schema is Subscription, the parent serializer, while the response is
     * BulkActionJob. No action-specific fields are documented separately. The default body is an empty
     * object; a payload is forwarded when you pass one. Response schema: BulkActionJob. Documented
     * success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function bulkAction(array $payload = []): array
    {
        return $this->object('POST', $this->path('subscriptions', 'bulk-action'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/subscriptions/export/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * SubscriptionExport has no writable fields. The default body is an empty object. Response schema:
     * ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('subscriptions', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/subscriptions/mrr-per-month/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * Response schema: MRRPerMonth. Required query parameters: year, month. Optional query parameters:
     * subscription. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return list<array<string, mixed>>
     */
    public function mrrPerMonth(int $year, int $month, array $query = []): array
    {
        $query = ['year' => $year, 'month' => $month] + $query;
        return $this->objectList('GET', $this->path('subscriptions', 'mrr-per-month'), $query, null);
    }

    /**
     * GET /api/v1/subscriptions/retention-cohort/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function retentionCohort(array $query = []): Page
    {
        return $this->page($this->path('subscriptions', 'retention-cohort'), $query);
    }

    /**
     * GET /api/v1/subscriptions/total-mrr/
     *
     * Returns current and future MRRs.
     * Response schema: TotalMRR. Optional query parameters: account, plan, terms, status,
     * collection_method, account_active_payment_method, set_to_expire,
     * has_recipient_receiving_invoices, has_custom_price, start_date__lte, start_date__org_tz_lte,
     * start_date__gte, start_date__org_tz_gte, next_renewal_date__lte, next_renewal_date__org_tz_lte,
     * next_renewal_date__gte, next_renewal_date__org_tz_gte, end_date__lte, end_date__org_tz_lte,
     * end_date__gte, end_date__org_tz_gte, last_modified_date__lte, last_modified_date__org_tz_lte,
     * last_modified_date__gte, last_modified_date__org_tz_gte, ordering, page, page_size. Documented
     * success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function totalMrr(array $query = []): array
    {
        return $this->object('GET', $this->path('subscriptions', 'total-mrr'), $query, null);
    }

    /**
     * POST /api/v1/subscriptions/{uuid}/add-following/
     *
     * Create a subscription that follows the invoicing period of this one. Both subscriptions renew on
     * the same date and are charged on one invoice, so the first period of the new subscription is
     * invoiced prorated up to the next renewal. The collection method, payment terms, pre-renewal days
     * offset and PO number are taken from this subscription. Only plan terms with the same billing
     * frequency and currency as this subscription can be chosen, and the start date must fall within
     * its current billing period. Only a forward-charged subscription that still renews can be
     * followed.
     * Response schema: Subscription. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function addFollowing(string $uuid, array $payload = []): array
    {
        return $this->object('POST', $this->path('subscriptions', $uuid, 'add-following'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/subscriptions/{uuid}/preview-following/
     *
     * Preview adding a following subscription without creating it. The `initial_period_start`,
     * `initial_period_end` and `initial_period_price` fields describe the prorated first period and
     * are null when the new subscription would be charged a full period.
     * Response schema: PreviewSubscription. Documented success status: HTTP 200.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function previewFollowing(string $uuid, array $payload = []): array
    {
        return $this->object('POST', $this->path('subscriptions', $uuid, 'preview-following'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/subscriptions/activity_months/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @deprecated OpenAPI marks this operation as deprecated.
     *
     * @param array<string, scalar|null> $query
     */
    public function activityMonthsLegacy(array $query = []): Page
    {
        return $this->page($this->path('subscriptions', 'activity_months'), $query);
    }

    /**
     * GET /api/v1/subscriptions/mrr_per_month/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * Response schema: MRRPerMonth. Required query parameters: year, month. Optional query parameters:
     * subscription. Documented success status: HTTP 200.
     *
     * @deprecated OpenAPI marks this operation as deprecated.
     *
     * @param array<string, scalar|null> $query
     * @return list<array<string, mixed>>
     */
    public function mrrPerMonthLegacy(int $year, int $month, array $query = []): array
    {
        $query = ['year' => $year, 'month' => $month] + $query;
        return $this->objectList('GET', $this->path('subscriptions', 'mrr_per_month'), $query, null);
    }

    /**
     * GET /api/v1/subscriptions/retention_cohort/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @deprecated OpenAPI marks this operation as deprecated.
     *
     * @param array<string, scalar|null> $query
     */
    public function retentionCohortLegacy(array $query = []): Page
    {
        return $this->page($this->path('subscriptions', 'retention_cohort'), $query);
    }

    /**
     * GET /api/v1/subscriptions/total_mrr/
     *
     * Subscriptions are instances of plan terms on a specific account. You can modify subscriptions or
     * cancel them.
     * Response schema: TotalMRR. Optional query parameters: account, plan, terms, status,
     * collection_method, account_active_payment_method, set_to_expire,
     * has_recipient_receiving_invoices, has_custom_price, start_date__lte, start_date__org_tz_lte,
     * start_date__gte, start_date__org_tz_gte, next_renewal_date__lte, next_renewal_date__org_tz_lte,
     * next_renewal_date__gte, next_renewal_date__org_tz_gte, end_date__lte, end_date__org_tz_lte,
     * end_date__gte, end_date__org_tz_gte, last_modified_date__lte, last_modified_date__org_tz_lte,
     * last_modified_date__gte, last_modified_date__org_tz_gte, ordering, page, page_size. Documented
     * success status: HTTP 200.
     *
     * @deprecated OpenAPI marks this operation as deprecated.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function totalMrrLegacy(array $query = []): array
    {
        return $this->object('GET', $this->path('subscriptions', 'total_mrr'), $query, null);
    }
}
