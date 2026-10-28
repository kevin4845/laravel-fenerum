<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests\Support;

use Kevin4845\LaravelFenerum\Http\Clock;

final class FixedClock implements Clock
{
    public function __construct(private readonly float $now)
    {
    }

    public function now(): float
    {
        return $this->now;
    }
}
