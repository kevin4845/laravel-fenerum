<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

/**
 * Successful response body that the schema does not describe as JSON.
 */
final class RawResponse
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $body,
        public readonly ?string $contentType,
    ) {
    }
}
