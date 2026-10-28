<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Resource;

final class ProductPricings extends Resource
{
    /**
     * Pricing collections are a JSON array, not a paginated envelope.
     *
     * @return list<array<string, mixed>>
     */
    public function list(string $productUuid): array
    {
        return $this->collection($this->path('products', $productUuid, 'pricings'));
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $productUuid, string $uuid): array
    {
        return $this->object('GET', $this->path('products', $productUuid, 'pricings', $uuid));
    }

    /**
     * Required by the ProductPricing schema: price, currency.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function create(string $productUuid, array $payload): array
    {
        return $this->object('POST', $this->path('products', $productUuid, 'pricings'), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function put(string $productUuid, string $uuid, array $payload): array
    {
        return $this->object('PUT', $this->path('products', $productUuid, 'pricings', $uuid), [], $payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function patch(string $productUuid, string $uuid, array $payload): array
    {
        return $this->object('PATCH', $this->path('products', $productUuid, 'pricings', $uuid), [], $payload);
    }

    public function delete(string $productUuid, string $uuid): void
    {
        $this->nothing('DELETE', $this->path('products', $productUuid, 'pricings', $uuid));
    }
}
