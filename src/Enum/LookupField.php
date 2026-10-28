<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Enum;

/**
 * Values accepted by the Lookup-Field header on plans and plan terms.
 */
enum LookupField: string
{
    case Uuid = 'uuid';
    case Code = 'code';
}
