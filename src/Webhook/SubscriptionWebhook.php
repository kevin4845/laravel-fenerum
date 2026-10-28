<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class SubscriptionWebhook
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $accountUuid,
        public readonly string $accountCode,
        public readonly string $termsUuid,
        public readonly string $startDate,
        public readonly ?string $endDate,
        public readonly string $nextRenewalDate,
        public readonly int|float $quantity,
        public readonly string $collectionMethod,
        public readonly int $paymentTerms,
        public readonly string $poNumber,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'uuid'),
            PayloadReader::string($data, 'account_uuid'),
            PayloadReader::string($data, 'account_code'),
            PayloadReader::string($data, 'terms_uuid'),
            PayloadReader::string($data, 'start_date'),
            PayloadReader::nullableString($data, 'end_date'),
            PayloadReader::string($data, 'next_renewal_date'),
            PayloadReader::number($data, 'quantity'),
            PayloadReader::string($data, 'collection_method'),
            PayloadReader::int($data, 'payment_terms'),
            PayloadReader::string($data, 'po_number'),
        );
    }
}
