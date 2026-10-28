<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Exception;

final class ApiException extends FenerumException
{
    /**
     * @param array<mixed>|null $errors Decoded JSON object body, when the response was a JSON object.
     *                                  Field values can repeat data the API received. Prefer statusCode over logging this.
     * @param int $retries How many times the failed request was retried before this exception.
     * @param float $retryDelaySeconds Total delay slept while retrying, in seconds.
     */
    public function __construct(
        string $message,
        public readonly int $statusCode,
        public readonly ?array $errors,
        public readonly string $responseBody,
        public readonly ?string $retryAfter,
        public readonly int $retries = 0,
        public readonly float $retryDelaySeconds = 0.0,
    ) {
        parent::__construct($message, $statusCode);
    }
}
