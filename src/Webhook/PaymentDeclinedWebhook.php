<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class PaymentDeclinedWebhook
{
    /**
     * @param array<mixed> $gatewayData
     */
    public function __construct(
        public readonly string $type,
        public readonly string $reason,
        public readonly string $reasonCode,
        public readonly string $paymentStatus,
        public readonly string $amount,
        public readonly string $amountInDefaultCurrency,
        public readonly string $invoice,
        public readonly string $date,
        public readonly string $currency,
        public readonly int $paymentCardId,
        public readonly array $gatewayData,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'type'),
            PayloadReader::string($data, 'reason'),
            PayloadReader::string($data, 'reason_code'),
            PayloadReader::string($data, 'payment_status'),
            PayloadReader::string($data, 'amount'),
            PayloadReader::string($data, 'amount_in_default_currency'),
            PayloadReader::string($data, 'invoice'),
            PayloadReader::string($data, 'date'),
            PayloadReader::string($data, 'currency'),
            PayloadReader::int($data, 'payment_card_id'),
            PayloadReader::object($data, 'data'),
        );
    }
}
