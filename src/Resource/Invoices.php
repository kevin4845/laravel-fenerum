<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Enum\CollectionMethod;
use Kevin4845\LaravelFenerum\Support\Page;

final class Invoices extends Resource
{
    /**
     * pdf_base64 is omitted from list results. The invoice article marks the since filter as deprecated.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('invoices'), $query);
    }

    /**
     * Detail responses include pdf_base64.
     *
     * @return array<string, mixed>
     */
    public function get(string $uuid): array
    {
        return $this->object('GET', $this->path('invoices', $uuid));
    }

    /**
     * Request payment of an invoice.
     *
     * Documented success is HTTP 202. The schema does not define a response body, so an
     * empty body returns null and a JSON object or array is returned as an array.
     * HTTP 423 is returned as an API error.
     *
     * @return array<mixed>|null
     */
    public function pay(string $uuid, CollectionMethod $collectionMethod): ?array
    {
        return $this->call('POST', $this->path('invoices', $uuid, 'pay'), [], [
            'collection_method' => $collectionMethod->value,
        ]);
    }

    /**
     * Refund the invoice. Fenerum returns the credit note.
     *
     * The request schema has no writable fields.
     *
     * @return array<string, mixed>
     */
    public function refund(string $uuid): array
    {
        return $this->object('POST', $this->path('invoices', $uuid, 'refund'), [], $this->emptyObject());
    }

    /**
     * Mark an invoice as due. The description says this only applies to chargeback status.
     *
     * The schema does not define a response body.
     *
     * @return array<mixed>|null
     */
    public function markAsDue(string $uuid): ?array
    {
        return $this->call(
            'POST',
            $this->path('invoices', $uuid, 'mark-as-due'),
            [],
            $this->emptyObject(),
        );
    }


    /**
     * GET /api/v1/invoices/available-vat-billing-periods/
     *
     * Get available VAT billing periods for your organization. If it does not return any options it
     * means that this feature is not available for your country.
     * Response schema: SimpleVATBillingPeriod. Documented success status: HTTP 200.
     *
     * @return list<array<string, mixed>>
     */
    public function availableVatBillingPeriods(): array
    {
        return $this->objectList('GET', $this->path('invoices', 'available-vat-billing-periods'), [], null);
    }

    /**
     * POST /api/v1/invoices/export-vat-report/
     *
     * Exports Sales and VAT report for the provided VAT billing period, countries, and granularity.
     * InvoiceExport has no writable fields. The default body is an empty object. Response schema:
     * ExportJob. Optional query parameters: export_period, export_countries, export_granularity.
     * Documented success status: HTTP 202.
     *
     * @param array<string, scalar|null> $query
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function exportVatReport(array $payload = [], array $query = []): array
    {
        return $this->object('POST', $this->path('invoices', 'export-vat-report'), $query, $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/invoices/export/
     *
     * Invoices are uniquely numbered lists of sold items and services issued to a customer on a
     * particular date
     * InvoiceExport has no writable fields. The default body is an empty object. Response schema:
     * ExportJob. Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function export(array $payload = []): array
    {
        return $this->object('POST', $this->path('invoices', 'export'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/invoices/{uuid}/accept-loss/
     *
     * Accept loss for the invoice. Can only accept it if an invoice has a chargeback status and
     * organization has the debt collection feature.
     * Required fields: registration_date. The response schema is not published. Decoded JSON is
     * returned as an array, or null when the body is empty. Documented success status: HTTP 200.
     *
     * @return array<mixed>|null
     */
    public function acceptLoss(string $uuid, string $registrationDate): ?array
    {
        $payload = [];
        $payload['registration_date'] = $registrationDate;
        return $this->call('POST', $this->path('invoices', $uuid, 'accept-loss'), [], $payload);
    }

    /**
     * POST /api/v1/invoices/{uuid}/apply-credit-note/
     *
     * Creates and applies a credit note to the invoice. The status of the invoice must be `chargeback`
     * and it must not be imported.
     * The response schema is not published. Decoded JSON is returned as an array, or null when the
     * body is empty. Documented success status: HTTP 200.
     *
     * @return array<mixed>|null
     */
    public function applyCreditNote(string $uuid): ?array
    {
        return $this->call('POST', $this->path('invoices', $uuid, 'apply-credit-note'), [], null);
    }

    /**
     * POST /api/v1/invoices/{uuid}/apply_credit/
     *
     * Applies the credit balance of the invoice to another. The balance of the invoice must be less
     * than 0 and the invoice must not be imported.
     * Required fields: target_invoice. The response schema is not published. Decoded JSON is returned
     * as an array, or null when the body is empty. Documented success status: HTTP 200.
     *
     * @return array<mixed>|null
     */
    public function applyCredit(string $uuid, string $targetInvoice, ?string $amount = null): ?array
    {
        $payload = [];
        $payload['target_invoice'] = $targetInvoice;
        if ($amount !== null) {
            $payload['amount'] = $amount;
        }
        return $this->call('POST', $this->path('invoices', $uuid, 'apply_credit'), [], $payload);
    }

    /**
     * POST /api/v1/invoices/{uuid}/change-collection-method/
     *
     * Change collection method of the invoice. Can only change it if an invoice has a chargeback
     * status.
     * Required fields: collection_method. The response schema is not published. Decoded JSON is
     * returned as an array, or null when the body is empty. Documented success status: HTTP 200.
     *
     * @return array<mixed>|null
     */
    public function changeCollectionMethod(string $uuid, CollectionMethod $collectionMethod): ?array
    {
        $payload = [];
        $payload['collection_method'] = $collectionMethod->value;
        return $this->call('POST', $this->path('invoices', $uuid, 'change-collection-method'), [], $payload);
    }

    /**
     * POST /api/v1/invoices/{uuid}/resend/
     *
     * Resend the Invoice. This is asynchronous action and the email will not be sent right away.
     * ResendInvoice has no writable fields. The default body is an empty object. The response schema
     * is not published. Decoded JSON is returned as an array, or null when the body is empty.
     * Documented success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<mixed>|null
     */
    public function resend(string $uuid, array $payload = []): ?array
    {
        return $this->call('POST', $this->path('invoices', $uuid, 'resend'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/invoices/{uuid}/send-to-debt-collection/
     *
     * The organization must have completed Likvido onboarding via the web UI before this endpoint can
     * be used, and the invoice must be in NONPAYMENT status with a positive balance.
     * Required fields: accept_terms. Response schema: DebtCollectionProcessResponse. Documented
     * success status: HTTP 201.
     *
     * @return array<string, mixed>
     */
    public function sendToDebtCollection(string $uuid, bool $acceptTerms, ?string $email = null): array
    {
        $payload = [];
        if ($email !== null) {
            $payload['email'] = $email;
        }
        $payload['accept_terms'] = $acceptTerms;
        return $this->object('POST', $this->path('invoices', $uuid, 'send-to-debt-collection'), [], $payload);
    }
}
