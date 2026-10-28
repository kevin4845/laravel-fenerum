<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class VatPayments extends Resource
{
    /**
     * GET /api/v1/vat-payments/upcoming-vat-payment/
     *
     * The response schema is not published. Decoded JSON is returned as an array, or null when the
     * body is empty. Optional query parameters: page, page_size. Documented success status: HTTP 200.
     *
     * @param array<string, scalar|null> $query
     * @return array<mixed>|null
     */
    public function upcomingVatPayment(array $query = []): ?array
    {
        return $this->call('GET', $this->path('vat-payments', 'upcoming-vat-payment'), $query, null);
    }
}
