<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Enum;

/**
 * Collection methods documented on Fenerum write operations.
 */
enum CollectionMethod: string
{
    case PaymentCard = 'payment_card';
    case Invoice = 'invoice';
    case DirectDebit = 'direct_debit';
}
