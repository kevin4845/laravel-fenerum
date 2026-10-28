<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

final class AccountWebhook
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $code,
        public readonly string $companyName,
        public readonly string $legalAddress,
        public readonly string $legalZipcode,
        public readonly string $legalCity,
        public readonly string $legalCountry,
        public readonly string $legalVatNumber,
        public readonly bool $billingSameAsLegal,
        public readonly ?string $billingAddress,
        public readonly ?string $billingZipcode,
        public readonly ?string $billingCity,
        public readonly ?string $billingCountry,
        public readonly string $createdDate,
    ) {
    }

    /**
     * @param array<mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            PayloadReader::string($data, 'uuid'),
            PayloadReader::string($data, 'code'),
            PayloadReader::string($data, 'company_name'),
            PayloadReader::string($data, 'legal_address'),
            PayloadReader::string($data, 'legal_zipcode'),
            PayloadReader::string($data, 'legal_city'),
            PayloadReader::string($data, 'legal_country'),
            PayloadReader::string($data, 'legal_vat_number'),
            PayloadReader::bool($data, 'billing_same_as_legal'),
            PayloadReader::nullableString($data, 'billing_address'),
            PayloadReader::nullableString($data, 'billing_zipcode'),
            PayloadReader::nullableString($data, 'billing_city'),
            PayloadReader::nullableString($data, 'billing_country'),
            PayloadReader::string($data, 'created_date'),
        );
    }
}
