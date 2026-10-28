<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Enum;

/**
 * Values of CancelSubscription.cancellation_type.
 */
enum CancellationType: string
{
    case FullRefund = 'full_refund';
    case AtRenewal = 'at_renewal';
    case Today = 'today';
    case AtSpecifiedTime = 'at_specified_time';
    case CancelInvalid = 'cancel_invalid';
}
