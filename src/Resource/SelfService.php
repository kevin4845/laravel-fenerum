<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class SelfService extends Resource
{
    /**
     * POST /api/self-service/initiate-organization/
     *
     * This endpoint can be used to initiate self-service on behalf of a recipient/new user. It
     * requires the self-service feature and the permission "initiate_self_service". The endpoint
     * returns a direct link to self-service for the provided email.
     * Required fields: email. Response schema: SelfServiceInitiateOrganizationResponse. Documented
     * success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function initiateOrganization(string $email): array
    {
        $payload = [];
        $payload['email'] = $email;
        return $this->object('POST', '/api/self-service/initiate-organization/', [], $payload);
    }
}
