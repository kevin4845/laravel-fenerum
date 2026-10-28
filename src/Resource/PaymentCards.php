<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class PaymentCards extends Resource
{
    /**
     * GET /api/v1/paymentcards/
     *
     * Payment Cards cover both credit and debit cards attached to an account.
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * account, status, page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('paymentcards'), $query);
    }

    /**
     * POST /api/v1/paymentcards/
     *
     * Create a payment card from a gateway token. The response may include
     * `setup_intent_client_secret` if 3DS authentication is required.
     * Response schema: PaymentCard. Documented success status: HTTP 201.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload = []): array
    {
        return $this->object('POST', $this->path('paymentcards'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/paymentcards/initiate-setup/
     *
     * Payment Cards cover both credit and debit cards attached to an account.
     * Required fields: payment_gateway. Response schema: PaymentCardInitiateSetup. Documented success
     * status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function initiateSetup(string $paymentGateway): array
    {
        $payload = [];
        $payload['payment_gateway'] = $paymentGateway;
        return $this->object('POST', $this->path('paymentcards', 'initiate-setup'), [], $payload);
    }

    /**
     * GET /api/v1/paymentcards/{uuid}/
     *
     * Payment Cards cover both credit and debit cards attached to an account.
     * Response schema: PaymentCard. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('paymentcards', $uuid), [], null);
    }

    /**
     * POST /api/v1/paymentcards/{uuid}/disable/
     *
     * Payment Cards cover both credit and debit cards attached to an account.
     * DisablePaymentCard has no writable fields. The default body is an empty object. Documented
     * success status: HTTP 204.
     *
     * @param array<string, mixed> $payload
     */
    public function disable(string $uuid, array $payload = []): void
    {
        $this->nothing('POST', $this->path('paymentcards', $uuid, 'disable'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
