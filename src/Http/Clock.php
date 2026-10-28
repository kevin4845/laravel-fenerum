<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

interface Clock
{
    /**
     * Current Unix timestamp in seconds.
     */
    public function now(): float;
}
