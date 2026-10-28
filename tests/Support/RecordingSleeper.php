<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests\Support;

use Kevin4845\LaravelFenerum\Http\Sleeper;

final class RecordingSleeper implements Sleeper
{
    /**
     * @var list<float>
     */
    public array $delays = [];

    public function sleep(float $seconds): void
    {
        $this->delays[] = $seconds;
    }
}
