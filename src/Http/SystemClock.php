<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

final class SystemClock implements Clock
{
    public function now(): float
    {
        return microtime(true);
    }
}
