<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Support;

use Kevin4845\LaravelFenerum\Exception\InvalidResponseException;

/**
 * A page from a Fenerum list endpoint.
 *
 * @phpstan-type Result array<string, mixed>
 */
final class Page
{
    /**
     * @param list<array<string, mixed>> $results
     */
    public function __construct(
        public readonly int $count,
        public readonly ?string $next,
        public readonly ?string $previous,
        public readonly array $results,
    ) {
    }

    /**
     * @param array<mixed> $payload
     */
    public static function fromPayload(array $payload): self
    {
        if (!array_key_exists('count', $payload) || !is_int($payload['count'])) {
            throw new InvalidResponseException('Fenerum list response is missing an integer count.');
        }

        if (!isset($payload['results']) || !is_array($payload['results']) || !array_is_list($payload['results'])) {
            throw new InvalidResponseException('Fenerum list response is missing a results array.');
        }

        $results = [];
        foreach ($payload['results'] as $result) {
            if (!is_array($result) || array_is_list($result)) {
                throw new InvalidResponseException('Fenerum list response contains a result that is not an object.');
            }

            $results[] = $result;
        }

        return new self(
            $payload['count'],
            self::nullableString($payload, 'next'),
            self::nullableString($payload, 'previous'),
            $results,
        );
    }

    /**
     * @param array<mixed> $payload
     */
    private static function nullableString(array $payload, string $key): ?string
    {
        if (!array_key_exists($key, $payload) || $payload[$key] === null) {
            return null;
        }

        if (!is_string($payload[$key])) {
            throw new InvalidResponseException(sprintf('Fenerum list field "%s" must be a string or null.', $key));
        }

        return $payload[$key];
    }
}
