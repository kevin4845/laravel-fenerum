<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

interface Sleeper
{
    public function sleep(float $seconds): void;
}
