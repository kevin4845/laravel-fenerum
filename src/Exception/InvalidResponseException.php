<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Exception;

final class InvalidResponseException extends FenerumException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 0,
        public readonly string $responseBody = '',
    ) {
        parent::__construct($message, $statusCode);
    }
}
