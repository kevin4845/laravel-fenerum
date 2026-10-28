<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Enum;

/**
 * Values of UpdateSubscription.change_type and SubscriptionChange.change_type.
 */
enum SubscriptionChangeType: string
{
    case Immediate = 'immediate';
    case AtSpecifiedTime = 'at_specified_time';
    case AtRenewal = 'at_renewal';
    case Retroactive = 'retroactive';
}
