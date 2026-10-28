<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

use Kevin4845\LaravelFenerum\Support\Page;

final class ErpLines extends Resource
{
    /**
     * GET /api/v1/erp-lines/
     *
     * The response uses the count, next, previous, and results page. Optional query parameters:
     * sync_status, type, currency, invoice__invoice_number, invoice__invoice_number__gte,
     * invoice__invoice_number__lte, voucher_id, voucher_id__gte, voucher_id__lte, date__lte,
     * date__gte, invoice__date__lte, invoice__date__gte, created_date__lte, created_date__gte,
     * updated_date__lte, updated_date__gte, ordering, search, page, page_size. Documented success
     * status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     */
    public function list(array $query = []): Page
    {
        return $this->page($this->path('erp-lines'), $query);
    }

    /**
     * POST /api/v1/erp-lines/bulk-action/
     *
     * The published body schema is ERPAccountLine, the parent serializer, while the response is
     * BulkActionJob. No action-specific fields are documented separately. The default body is an empty
     * object; a payload is forwarded when you pass one. Response schema: BulkActionJob. Documented
     * success status: HTTP 202.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function bulkAction(array $payload = []): array
    {
        return $this->object('POST', $this->path('erp-lines', 'bulk-action'), [], $payload === [] ? $this->emptyObject() : $payload);
    }

    /**
     * POST /api/v1/erp-lines/export/
     *
     * Response schema: ExportJob. Documented success status: HTTP 202.
     *
     * @return array<string, mixed>
     */
    public function export(): array
    {
        return $this->object('POST', $this->path('erp-lines', 'export'), [], null);
    }

    /**
     * GET /api/v1/erp-lines/{id}/
     *
     * Response schema: ERPAccountLine. Documented success status: HTTP 200.
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->object('GET', $this->path('erp-lines', $id), [], null);
    }

    /**
     * PATCH /api/v1/erp-lines/{id}/
     *
     * Required fields: date. Response schema: ERPAccountLineUpdate. Documented success status: HTTP
     * 200.
     *
     * @return array<string, mixed>
     */
    public function patch(string $id, string $date, ?string $accountNumberStr = null, ?string $contraAccountNumberStr = null): array
    {
        $payload = [];
        $payload['date'] = $date;
        if ($accountNumberStr !== null) {
            $payload['account_number_str'] = $accountNumberStr;
        }
        if ($contraAccountNumberStr !== null) {
            $payload['contra_account_number_str'] = $contraAccountNumberStr;
        }
        return $this->object('PATCH', $this->path('erp-lines', $id), [], $payload);
    }

    /**
     * POST /api/v1/erp-lines/{id}/cancel/
     *
     * The published body schema is ERPAccountLine, but the success response is empty and no action
     * fields are documented. No JSON body is sent. Documented success status: HTTP 204.
     */
    public function cancel(string $id): void
    {
        $this->nothing('POST', $this->path('erp-lines', $id, 'cancel'), []);
    }

    /**
     * POST /api/v1/erp-lines/{id}/resend/
     *
     * ResendERPAccountLine has no writable fields. The default body is an empty object. Documented
     * success status: HTTP 204.
     *
     * @param array<string, mixed> $payload
     */
    public function resend(string $id, array $payload = []): void
    {
        $this->nothing('POST', $this->path('erp-lines', $id, 'resend'), [], $payload === [] ? $this->emptyObject() : $payload);
    }
}
