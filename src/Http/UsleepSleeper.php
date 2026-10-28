<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Http;

final class UsleepSleeper implements Sleeper
{
    public function sleep(float $seconds): void
    {
        if ($seconds <= 0) {
            return;
        }

        usleep((int) round($seconds * 1000000));
    }
}
