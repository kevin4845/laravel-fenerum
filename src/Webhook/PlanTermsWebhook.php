<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class PlanTermsWebhook
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $planUuid,
        public readonly string $planName,
        public readonly bool $planCollectVat,
        public readonly string $planVatType,
        public readonly string $intervalType,
        public readonly int $intervalCount,
        public readonly int|float $price,
        public readonly string $currency,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'uuid'),
            PayloadReader::string($data, 'plan_uuid'),
            PayloadReader::string($data, 'plan_name'),
            PayloadReader::bool($data, 'plan_collect_vat'),
            PayloadReader::string($data, 'plan_vat_type'),
            PayloadReader::string($data, 'interval_type'),
            PayloadReader::int($data, 'interval_count'),
            PayloadReader::number($data, 'price'),
            PayloadReader::string($data, 'currency'),
        );
    }
}
