<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

/**
 * Event names published on the webhook resource and the webhook callbacks page.
 */
final class KnownEvents
{
    public const ACCOUNT_CREATED = 'account.created';

    public const ACCOUNT_UPDATED = 'account.updated';

    public const PLAN_TERMS_CREATED = 'plan_terms.created';

    public const PLAN_TERMS_UPDATED = 'plan_terms.updated';

    public const NEW_INVOICE = 'new_invoice';

    public const PAID_INVOICE = 'paid_invoice';

    public const INVOICE_OVERDUE = 'invoice.overdue';

    public const CANCEL_SUBSCRIPTION = 'cancel_subscription';

    public const REACTIVATE_SUBSCRIPTION = 'reactivate_subscription';

    public const RENEW_SUBSCRIPTION_SOON = 'renew_subscription_soon';

    public const NEW_ACTIVITY = 'new_activity';

    public const PAYMENT_AUTHENTICATION_REQUIRED = 'payment.authentication_required';

    public const PAYMENT_DECLINED = 'payment.declined';

    public const CARD_EXPIRES_THIS_MONTH = 'card_expires_this_month';

    public const PAYMENT_CARD_ACTIVATED = 'payment_card.activated';

    public const PAYMENT_CARD_DEACTIVATED = 'payment_card.deactivated';

    /**
     * Payment events that are not enabled until the webhook events list opts in.
     *
     * @var list<string>
     */
    public const OPT_IN = [
        self::PAYMENT_AUTHENTICATION_REQUIRED,
        self::PAYMENT_DECLINED,
        self::CARD_EXPIRES_THIS_MONTH,
        self::PAYMENT_CARD_ACTIVATED,
        self::PAYMENT_CARD_DEACTIVATED,
    ];

    /**
     * @var list<string>
     */
    public const ALL = [
        self::ACCOUNT_CREATED,
        self::ACCOUNT_UPDATED,
        self::PLAN_TERMS_CREATED,
        self::PLAN_TERMS_UPDATED,
        self::NEW_INVOICE,
        self::PAID_INVOICE,
        self::INVOICE_OVERDUE,
        self::CANCEL_SUBSCRIPTION,
        self::REACTIVATE_SUBSCRIPTION,
        self::RENEW_SUBSCRIPTION_SOON,
        self::NEW_ACTIVITY,
        self::PAYMENT_AUTHENTICATION_REQUIRED,
        self::PAYMENT_DECLINED,
        self::CARD_EXPIRES_THIS_MONTH,
        self::PAYMENT_CARD_ACTIVATED,
        self::PAYMENT_CARD_DEACTIVATED,
    ];

    public static function includes(string $event): bool
    {
        return in_array($event, self::ALL, true);
    }
}
