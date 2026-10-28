<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class PaymentCardSummary
{
    public function __construct(
        public readonly string $cardUuid,
        public readonly string $cardNumber,
        public readonly string $paymentGatewayId,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'card_uuid'),
            PayloadReader::string($data, 'card_number'),
            PayloadReader::string($data, 'payment_gateway_id'),
        );
    }
}
