<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

/**
 * Read and cancel subscription changes.
 *
 * The OpenAPI spec exposes list, retrieve, and cancel. It does not expose an endpoint that
 * creates a change. Create a change by updating the subscription with Subscriptions::patch()
 * or Subscriptions::changeQuantity().
 */
final class SubscriptionChanges extends Resource
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function list(string $subscriptionUuid, array $query = []): Page
    {
        return $this->page($this->path('subscriptions', $subscriptionUuid, 'changes'), $query);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $subscriptionUuid, string $uuid): array
    {
        return $this->object('GET', $this->path('subscriptions', $subscriptionUuid, 'changes', $uuid));
    }

    /**
     * Cancel a scheduled change before Fenerum applies it.
     *
     * The request schema is an empty object.
     *
     * @return array<string, mixed>
     */
    public function cancel(string $subscriptionUuid, string $uuid): array
    {
        return $this->object(
            'POST',
            $this->path('subscriptions', $subscriptionUuid, 'changes', $uuid, 'cancel'),
            [],
            $this->emptyObject(),
        );
    }
}
