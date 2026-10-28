<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Webhook;

use Kevin4845\LaravelFenerum\Exception\InvalidWebhookPayload;

final class WebhookEvent
{
    /**
     * @param array<mixed> $data
     * @param array<mixed> $payload
     */
    public function __construct(
        public readonly string $type,
        public readonly array $data,
        public readonly array $payload,
    ) {
    }

    public function isKnown(): bool
    {
        return KnownEvents::includes($this->type);
    }

    public function asAccount(): AccountWebhook
    {
        $this->expect([KnownEvents::ACCOUNT_CREATED, KnownEvents::ACCOUNT_UPDATED]);

        return AccountWebhook::from($this->data);
    }

    public function asPlanTerms(): PlanTermsWebhook
    {
        $this->expect([KnownEvents::PLAN_TERMS_CREATED, KnownEvents::PLAN_TERMS_UPDATED]);

        return PlanTermsWebhook::from($this->data);
    }

    public function asInvoice(): InvoiceWebhook
    {
        $this->expect([
            KnownEvents::NEW_INVOICE,
            KnownEvents::PAID_INVOICE,
            KnownEvents::INVOICE_OVERDUE,
        ]);

        return InvoiceWebhook::from($this->data);
    }

    public function asSubscription(): SubscriptionWebhook
    {
        $this->expect([
            KnownEvents::CANCEL_SUBSCRIPTION,
            KnownEvents::REACTIVATE_SUBSCRIPTION,
            KnownEvents::RENEW_SUBSCRIPTION_SOON,
        ]);

        return SubscriptionWebhook::from($this->data);
    }

    public function asActivity(): ActivityWebhook
    {
        $this->expect([KnownEvents::NEW_ACTIVITY]);

        return ActivityWebhook::from($this->data);
    }

    public function asPaymentAuthenticationRequired(): PaymentAuthenticationRequiredWebhook
    {
        $this->expect([KnownEvents::PAYMENT_AUTHENTICATION_REQUIRED]);

        return PaymentAuthenticationRequiredWebhook::from($this->data);
    }

    public function asPaymentDeclined(): PaymentDeclinedWebhook
    {
        $this->expect([KnownEvents::PAYMENT_DECLINED]);

        return PaymentDeclinedWebhook::from($this->data);
    }

    public function asPaymentCard(): PaymentCardWebhook
    {
        $this->expect([
            KnownEvents::CARD_EXPIRES_THIS_MONTH,
            KnownEvents::PAYMENT_CARD_ACTIVATED,
            KnownEvents::PAYMENT_CARD_DEACTIVATED,
        ]);

        return PaymentCardWebhook::from($this->data);
    }

    /**
     * @param list<string> $types
     */
    private function expect(array $types): void
    {
        if (!in_array($this->type, $types, true)) {
            throw new InvalidWebhookPayload(sprintf(
                'Webhook event "%s" cannot be read as %s.',
                $this->type,
                implode(' or ', $types),
            ));
        }
    }
}
