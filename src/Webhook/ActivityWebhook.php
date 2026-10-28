<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class ActivityWebhook
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $accountUuid,
        public readonly string $accountCode,
        public readonly string $subscription,
        public readonly string $invoice,
        public readonly string $date,
        public readonly string $type,
        public readonly string $oldMrr,
        public readonly string $newMrr,
        public readonly int|float $oldQuantity,
        public readonly int|float $newQuantity,
        public readonly string $oldTerms,
        public readonly string $newTerms,
        public readonly string $mrrChange,
        public readonly int|float $quantityChange,
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
            PayloadReader::string($data, 'subscription'),
            PayloadReader::string($data, 'invoice'),
            PayloadReader::string($data, 'date'),
            PayloadReader::string($data, 'type'),
            PayloadReader::string($data, 'old_mrr'),
            PayloadReader::string($data, 'new_mrr'),
            PayloadReader::number($data, 'old_quantity'),
            PayloadReader::number($data, 'new_quantity'),
            PayloadReader::string($data, 'old_terms'),
            PayloadReader::string($data, 'new_terms'),
            PayloadReader::string($data, 'mrr_change'),
            PayloadReader::number($data, 'quantity_change'),
        );
    }
}
