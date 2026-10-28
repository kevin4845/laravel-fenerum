<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

use Kevin4845\LaravelFenerum\Exception\InvalidWebhookPayload;

/**
 * Reads fields shown in the documented webhook examples.
 *
 * @internal
 */
final class PayloadReader
{
    /**
     * @param array<mixed> $data
     */
    public static function string(array $data, string $key): string
    {
        if (!array_key_exists($key, $data) || !is_string($data[$key])) {
            throw new InvalidWebhookPayload(sprintf('Webhook field "%s" must be a string.', $key));
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     */
    public static function nullableString(array $data, string $key): ?string
    {
        if (!array_key_exists($key, $data) || $data[$key] === null) {
            return null;
        }

        if (!is_string($data[$key])) {
            throw new InvalidWebhookPayload(sprintf('Webhook field "%s" must be a string or null.', $key));
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     */
    public static function bool(array $data, string $key): bool
    {
        if (!array_key_exists($key, $data) || !is_bool($data[$key])) {
            throw new InvalidWebhookPayload(sprintf('Webhook field "%s" must be a boolean.', $key));
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     */
    public static function int(array $data, string $key): int
    {
        if (!array_key_exists($key, $data) || !is_int($data[$key])) {
            throw new InvalidWebhookPayload(sprintf('Webhook field "%s" must be an integer.', $key));
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     */
    public static function number(array $data, string $key): int|float
    {
        if (!array_key_exists($key, $data) || (!is_int($data[$key]) && !is_float($data[$key]))) {
            throw new InvalidWebhookPayload(sprintf('Webhook field "%s" must be a number.', $key));
        }

        return $data[$key];
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    public static function object(array $data, string $key): array
    {
        if (
            !array_key_exists($key, $data)
            || !is_array($data[$key])
            || (array_is_list($data[$key]) && $data[$key] !== [])
        ) {
            throw new InvalidWebhookPayload(sprintf('Webhook field "%s" must be an object.', $key));
        }

        return $data[$key];
    }
}
