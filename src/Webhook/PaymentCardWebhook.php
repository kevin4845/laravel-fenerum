<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class PaymentCardWebhook
{
    public function __construct(
        public readonly string $uuid,
        public readonly bool $active,
        public readonly string $brand,
        public readonly string $cardNumber,
        public readonly int $month,
        public readonly int $year,
        public readonly string $name,
        public readonly string $paymentGateway,
        public readonly string $paymentGatewayId,
        public readonly string $account,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'uuid'),
            PayloadReader::bool($data, 'active'),
            PayloadReader::string($data, 'brand'),
            PayloadReader::string($data, 'card_number'),
            PayloadReader::int($data, 'month'),
            PayloadReader::int($data, 'year'),
            PayloadReader::string($data, 'name'),
            PayloadReader::string($data, 'payment_gateway'),
            PayloadReader::string($data, 'payment_gateway_id'),
            PayloadReader::string($data, 'account'),
        );
    }
}
