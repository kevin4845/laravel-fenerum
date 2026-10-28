<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class OrganizationMembers extends Resource
{
    /**
     * GET /api/v1/organization-members/
     *
     * Unlike OrganizationMemberContextViewSet this is NOT exempt from ActiveOrganizationMiddleware, so
     * suspended organizations are blocked and a trial-expired organization keeps read-only access via
     * the whitelist in TRIAL_EXPIRED_ALLOWED_API_URL_NAMES (matching the old server-rendered list).
     * Future write actions (create/update) that require an active organization belong here too.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * is_active, is_admin, ordering, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('organization-members'), $query);
    }

    /**
     * GET /api/v1/organization-members/available/
     *
     * List available Organization Members
     * Response schema: OrganizationMember. Optional query parameters: page, page_size. Documented
     * success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return list<array<string, mixed>>
     */
    public function available(array $query = []): array
    {
        return $this->objectList('GET', $this->path('organization-members', 'available'), $query, null);
    }

    /**
     * GET /api/v1/organization-members/current/
     *
     * Retrieve details of the currently authenticated Organization Member
     * Response schema: OrganizationMember. Optional query parameters: page, page_size. Documented
     * success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function current(array $query = []): array
    {
        return $this->object('GET', $this->path('organization-members', 'current'), $query, null);
    }
}
