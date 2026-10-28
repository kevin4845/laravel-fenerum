<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class PaymentAuthenticationRequiredWebhook
{
    public function __construct(
        public readonly string $id,
        public readonly PaymentCardSummary $paymentCard,
        public readonly string $amount,
        public readonly string $currency,
        public readonly string $paymentGateway,
        public readonly string $paymentGatewayId,
        public readonly string $invoice,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'id'),
            PaymentCardSummary::from(PayloadReader::object($data, 'payment_card')),
            PayloadReader::string($data, 'amount'),
            PayloadReader::string($data, 'currency'),
            PayloadReader::string($data, 'payment_gateway'),
            PayloadReader::string($data, 'payment_gateway_id'),
            PayloadReader::string($data, 'invoice'),
        );
    }
}
