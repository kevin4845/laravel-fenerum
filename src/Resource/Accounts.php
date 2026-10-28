<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use InvalidArgumentException;
use Kevin4845\LaravelFenerum\Enum\CollectionMethod;
use Kevin4845\LaravelFenerum\Exception\InvalidResponseException;
use Kevin4845\LaravelFenerum\Support\Page;

final class Accounts extends Resource
{
    /**
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = [], ?string $fields = null): Page
    {
        return $this->page($this->path('accounts'), $query, $this->fields($fields));
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $code, ?string $fields = null): array
    {
        return $this->object('GET', $this->path('accounts', $code), [], null, $this->fields($fields));
    }

    /**
     * Required by the Account schema: code, legal_address, legal_zipcode, legal_city, legal_country.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->object('POST', $this->path('accounts'), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $code, array $payload): array
    {
        return $this->object('PUT', $this->path('accounts', $code), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $code, array $payload): array
    {
        return $this->object('PATCH', $this->path('accounts', $code), [], $payload);
    }

    public function delete(string $code): void
    {
        $this->nothing('DELETE', $this->path('accounts', $code));
    }

    /**
     * POST /api/v1/accounts/{code}/archive/
     *
     * The OpenAPI operation attaches the Account serializer as a required body, but its
     * description does not document any request fields and the success response is empty.
     * This method sends no JSON body.
     */
    public function archive(string $code): void
    {
        $this->nothing('POST', $this->path('accounts', $code, 'archive'));
    }

    /**
     * POST /api/v1/accounts/{code}/restore/
     *
     * See archive() for the request-body uncertainty.
     */
    public function restore(string $code): void
    {
        $this->nothing('POST', $this->path('accounts', $code, 'restore'));
    }

    /**
     * Create a subscription and cancel every other subscription on the account.
     *
     * Fenerum issues a pro-rated invoice immediately when a subscription is replaced.
     * Do not combine this with Subscriptions::create().
     *
     * @return array<string, mixed>
     */
    public function subscribe(
        string $code,
        string $terms,
        int|float $quantity,
        CollectionMethod $collectionMethod,
        ?bool $groupOnInvoice = null,
    ): array {
        $this->assertFiniteQuantity($quantity);

        $payload = [
            'terms' => $terms,
            'quantity' => $quantity,
            'collection_method' => $collectionMethod->value,
        ];

        if ($groupOnInvoice !== null) {
            $payload['group_on_invoice'] = $groupOnInvoice;
        }

        return $this->object('POST', $this->path('accounts', $code, 'subscribe'), [], $payload);
    }

    /**
     * Ask Fenerum to invoice draft lines that are not flagged for the next renewal.
     *
     * The current path is /generate-invoice/. The underscore path is deprecated and is not called here.
     * One invoice is returned per currency. This method does not build invoice lines.
     *
     * @return list<array<string, mixed>>
     */
    public function generateInvoice(
        string $code,
        ?CollectionMethod $collectionMethod = null,
        ?int $paymentTerms = null,
    ): array {
        $payload = [];
        if ($collectionMethod !== null) {
            $payload['collection_method'] = $collectionMethod->value;
        }

        if ($paymentTerms !== null) {
            $payload['payment_terms'] = $paymentTerms;
        }

        $body = $payload === [] ? $this->emptyObject() : $payload;
        $response = $this->call('POST', $this->path('accounts', $code, 'generate-invoice'), [], $body);
        if ($response === null || !array_is_list($response)) {
            throw new InvalidResponseException('Expected Fenerum to return the invoices it generated.');
        }

        $invoices = [];
        foreach ($response as $invoice) {
            if (!is_array($invoice) || array_is_list($invoice)) {
                throw new InvalidResponseException('Expected each generated invoice to be a JSON object.');
            }

            $invoices[] = $invoice;
        }

        return $invoices;
    }

    /**
     * Trigger renew() on the account's pending_for_grouped subscriptions.
     *
     * @return array<string, mixed>
     */
    public function startPendingSubscriptions(string $code): array
    {
        return $this->object(
            'POST',
            $this->path('accounts', $code, 'start-pending-subscriptions'),
            [],
            $this->emptyObject(),
        );
    }


    /**
     * POST /api/v1/accounts/export-balance/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * The published body schema is Account, the parent serializer, while the response is ExportJob. No
     * action-specific fields are documented separately. The default body is an empty object; a payload
     * is forwarded when you pass one. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function exportBalance(array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', 'export-balance'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/accounts/export-contract-tiers/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * The published body schema is Account, the parent serializer, while the response is ExportJob. No
     * action-specific fields are documented separately. The default body is an empty object; a payload
     * is forwarded when you pass one. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function exportContractTiers(array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', 'export-contract-tiers'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/accounts/export-contracts/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * The published body schema is Account, the parent serializer, while the response is ExportJob. No
     * action-specific fields are documented separately. The default body is an empty object; a payload
     * is forwarded when you pass one. Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function exportContracts(array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', 'export-contracts'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/accounts/export-mrr-per-month-report/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * AccountExport has no writable fields. The default body is an empty object. Response schema:
     * ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function exportMrrPerMonthReport(array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', 'export-mrr-per-month-report'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/accounts/export/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * AccountExport has no writable fields. The default body is an empty object. Response schema:
     * ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('accounts', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * GET /api/v1/accounts/{code}/card-registration-link/{gateway}/
     *
     * Here you can generate an url to send to your account in order for them to register a payment
     * card in a specified gateway.
     * Response schema: AddCardURLResponse. Optional query parameters: currency, redirect_url.
     * Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function getCardRegistrationLink(string $code, string $gateway, array $query = []): array
    {
        return $this->object('GET', $this->path('accounts', $code, 'card-registration-link', $gateway), $query, null);
    }

    /**
     * GET /api/v1/accounts/{code}/payment-gateways/
     *
     * One set of payment_gateway and payment_gateway_account_id is allowed per a customer account.
     * Meaning each of your customers needs to match 1-to-1 with customer accounts set up in each
     * payment gateway you use.
     * Response schema: MinimalAccountGateway. Documented success status: HTTP 200.
     *
     * @return list<array<string, mixed>>
     */
    public function paymentGateways(string $code): array
    {
        return $this->objectList('GET', $this->path('accounts', $code, 'payment-gateways'), [], null);
    }

    /**
     * PATCH /api/v1/accounts/{code}/payment-gateways/
     *
     * One set of payment_gateway and payment_gateway_account_id is allowed per a customer account.
     * Meaning each of your customers needs to match 1-to-1 with customer accounts set up in each
     * payment gateway you use.
     * The request body is a JSON array. Response schema: MinimalAccountGateway. Documented success
     * status: HTTP 200.
     *
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    public function updatePaymentGateways(string $code, array $items): array
    {
        return $this->objectList('PATCH', $this->path('accounts', $code, 'payment-gateways'), [], $items);
    }

    /**
     * GET /api/v1/accounts/{code}/card_registration_link/{gateway}/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * Response schema: AddCardURLResponse. Optional query parameters: currency, redirect_url.
     * Documented success status: HTTP 200.
     *
     * @deprecated OpenAPI marks this operation as deprecated.
     *
     * @param array<string, scalar|null> $query
     * @return array<string, mixed>
     */
    public function getCardRegistrationLinkLegacy(string $code, string $gateway, array $query = []): array
    {
        return $this->object('GET', $this->path('accounts', $code, 'card_registration_link', $gateway), $query, null);
    }

    /**
     * POST /api/v1/accounts/{code}/generate_invoice/
     *
     * An Account in Fenerum is a named entity who can have one or more subscriptions. Can be used for
     * both companies and people.
     * Response schema: Invoice. Documented success status: HTTP 200.
     *
     * @deprecated OpenAPI marks this operation as deprecated.
     *
     * @return list<array<string, mixed>>
     */
    public function generateInvoiceLegacy(string $code, ?CollectionMethod $collectionMethod = null, ?int $paymentTerms = null): array
    {
        $payload = [];
        if ($collectionMethod !== null) {
            $payload['collection_method'] = $collectionMethod->value;
        }
        if ($paymentTerms !== null) {
            $payload['payment_terms'] = $paymentTerms;
        }
        return $this->objectList('POST', $this->path('accounts', $code, 'generate_invoice'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    private function assertFiniteQuantity(int|float $quantity): void
    {
        if (is_float($quantity) && !is_finite($quantity)) {
            throw new InvalidArgumentException('Quantity must be a finite number.');
        }
    }
}
