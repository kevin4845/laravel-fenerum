<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class InvoiceWebhook
{
    public function __construct(
        public readonly string $uuid,
        public readonly int $invoiceNumber,
        public readonly string $accountUuid,
        public readonly string $accountCode,
        public readonly string $date,
        public readonly string $dueDate,
        public readonly string $invoiceApiUrl,
        public readonly string $currency,
        public readonly string $status,
        public readonly string $collectionMethod,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'uuid'),
            PayloadReader::int($data, 'invoice_number'),
            PayloadReader::string($data, 'account_uuid'),
            PayloadReader::string($data, 'account_code'),
            PayloadReader::string($data, 'date'),
            PayloadReader::string($data, 'due_date'),
            PayloadReader::string($data, 'invoice_api_url'),
            PayloadReader::string($data, 'currency'),
            PayloadReader::string($data, 'status'),
            PayloadReader::string($data, 'collection_method'),
        );
    }
}
