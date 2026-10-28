<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests;

use Kevin4845\LaravelFenerum\Exception\InvalidWebhookPayload;
use Kevin4845\LaravelFenerum\Webhook\KnownEvents;
use Kevin4845\LaravelFenerum\Webhook\WebhookParser;
use PHPUnit\Framework\TestCase;

final class WebhookParserTest extends TestCase
{
    private WebhookParser $parser;

    protected function setUp(): void
    {
        $this->parser = new WebhookParser();
    }

    public function testAccountCreatedMatchesTheDocumentedPayload(): void
    {
        $event = $this->parser->parse($this->json([
            'event' => 'account.created',
            'data' => [
                'uuid' => '5c838347-d3ce-40fb-951c-b3190ccf2cba',
                'code' => 'important_client',
                'company_name' => 'Important client',
                'legal_address' => 'Important street 12',
                'legal_zipcode' => '1234',
                'legal_city' => 'Aarhus',
                'legal_country' => 'DK',
                'legal_vat_number' => 'DK12345678',
                'billing_same_as_legal' => true,
                'billing_address' => null,
                'billing_zipcode' => null,
                'billing_city' => null,
                'billing_country' => null,
                'created_date' => '2018-11-20T11:25:23.889915+00:00',
            ],
        ]));

        $account = $event->asAccount();

        self::assertTrue($event->isKnown());
        self::assertSame(KnownEvents::ACCOUNT_CREATED, $event->type);
        self::assertSame('important_client', $account->code);
        self::assertNull($account->billingAddress);
        self::assertTrue($account->billingSameAsLegal);
    }

    public function testInvoiceAndSubscriptionEventsUseTheSharedShapes(): void
    {
        $invoice = $this->parser->parse($this->json([
            'event' => 'invoice.overdue',
            'data' => $this->invoiceData('due'),
        ]))->asInvoice();
        $subscription = $this->parser->parse($this->json([
            'event' => 'cancel_subscription',
            'data' => $this->subscriptionData(),
        ]))->asSubscription();

        self::assertSame(2193, $invoice->invoiceNumber);
        self::assertSame('due', $invoice->status);
        self::assertSame(1.5, $subscription->quantity);
        self::assertNull($subscription->endDate);
    }

    public function testActivityPreservesDecimalStrings(): void
    {
        $activity = $this->parser->parse($this->json([
            'event' => 'new_activity',
            'data' => [
                'uuid' => 'b84948d2-30b8-4060-a5b4-6aa92db59b50',
                'account_uuid' => '5c838347-d3ce-40fb-951c-b3190ccf2cba',
                'account_code' => 'important_client',
                'subscription' => '320ac230-a004-4cb3-9caa-fca0bee3e053',
                'invoice' => 'e947c739-0e32-4954-aa78-9ec8605d58d5',
                'date' => '2018-01-21T11:25:23.889915+00:00',
                'type' => 'upgrade_qty',
                'old_mrr' => '150.30',
                'new_mrr' => '200.40',
                'old_quantity' => 3,
                'new_quantity' => 4,
                'old_terms' => '580eb744-8746-4e78-85a3-b927605db7ad',
                'new_terms' => '580eb744-8746-4e78-85a3-b927605db7ad',
                'mrr_change' => '50.10',
                'quantity_change' => 1,
            ],
        ]))->asActivity();

        self::assertSame('upgrade_qty', $activity->type);
        self::assertSame('50.10', $activity->mrrChange);
        self::assertSame(1, $activity->quantityChange);
    }

    public function testPaymentEventsKeepGatewayData(): void
    {
        $authentication = $this->parser->parse($this->json([
            'event' => 'payment.authentication_required',
            'data' => [
                'id' => '1421',
                'payment_card' => [
                    'card_uuid' => 'f4293c5e-b592-43a1-9c68-eb76f62f6250',
                    'card_number' => '**** **** **** 4242',
                    'payment_gateway_id' => 'card_1Nw3eQ2eZvKYlo2C7Lz5xxxx',
                ],
                'amount' => '199.00',
                'currency' => 'EUR',
                'payment_gateway' => 'stripe',
                'payment_gateway_id' => 'pi_3Nw3fQ2eZvKYlo2C0xx5xxxx',
                'invoice' => 'b84948d2-30b8-4060-a5b4-6aa92db59b50',
            ],
        ]))->asPaymentAuthenticationRequired();
        $declined = $this->parser->parse($this->json([
            'event' => 'payment.declined',
            'data' => [
                'type' => 'payment',
                'reason' => 'Your card was declined.',
                'reason_code' => 'declined',
                'payment_status' => 'declined',
                'amount' => '199.00',
                'amount_in_default_currency' => '1485.00',
                'invoice' => 'b84948d2-30b8-4060-a5b4-6aa92db59b50',
                'date' => '2026-05-18',
                'currency' => 'EUR',
                'payment_card_id' => 1421,
                'data' => ['gateway' => 'raw'],
            ],
        ]))->asPaymentDeclined();
        $card = $this->parser->parse($this->json([
            'event' => 'payment_card.deactivated',
            'data' => [
                'uuid' => 'f4293c5e-b592-43a1-9c68-eb76f62f6250',
                'active' => false,
                'brand' => 'Visa',
                'card_number' => '**** **** **** 4242',
                'month' => 5,
                'year' => 2026,
                'name' => 'Jane Doe',
                'payment_gateway' => 'stripe',
                'payment_gateway_id' => 'card_1Nw3eQ2eZvKYlo2C7Lz5xxxx',
                'account' => '5c838347-d3ce-40fb-951c-b3190ccf2cba',
            ],
        ]))->asPaymentCard();

        self::assertSame('1421', $authentication->id);
        self::assertSame('**** **** **** 4242', $authentication->paymentCard->cardNumber);
        self::assertSame(['gateway' => 'raw'], $declined->gatewayData);
        self::assertFalse($card->active);
        self::assertContains(KnownEvents::PAYMENT_DECLINED, KnownEvents::OPT_IN);
    }

    public function testPlanTermsCreatedReadsTheDocumentedPrice(): void
    {
        $terms = $this->parser->parse($this->json([
            'event' => 'plan_terms.created',
            'data' => [
                'uuid' => 'b84948d2-30b8-4060-a5b4-6aa92db59b50',
                'plan_uuid' => '5c838347-d3ce-40fb-951c-b3190ccf2cba',
                'plan_name' => 'Enterprise',
                'plan_collect_vat' => true,
                'plan_vat_type' => 'services',
                'interval_type' => 'month',
                'interval_count' => 1,
                'price' => 99.99,
                'currency' => 'DKK',
            ],
        ]))->asPlanTerms();

        self::assertSame(99.99, $terms->price);
        self::assertSame('services', $terms->planVatType);
    }

    public function testEveryDocumentedEventParses(): void
    {
        self::assertSame(
            [
                'account.created',
                'account.updated',
                'plan_terms.created',
                'plan_terms.updated',
                'new_invoice',
                'paid_invoice',
                'invoice.overdue',
                'cancel_subscription',
                'reactivate_subscription',
                'renew_subscription_soon',
                'new_activity',
                'payment.authentication_required',
                'payment.declined',
                'card_expires_this_month',
                'payment_card.activated',
                'payment_card.deactivated',
            ],
            KnownEvents::ALL,
        );

        foreach (KnownEvents::ALL as $event) {
            $parsed = $this->parser->parse($this->json([
                'event' => $event,
                'data' => [],
            ]));

            self::assertTrue($parsed->isKnown());
            self::assertSame($event, $parsed->type);
        }
    }

    public function testUnknownEventsRemainReadable(): void
    {
        $event = $this->parser->parse($this->json([
            'event' => 'future.event',
            'data' => ['id' => '1'],
        ]));

        self::assertFalse($event->isKnown());
        self::assertSame('1', $event->data['id']);
        $this->expectException(InvalidWebhookPayload::class);
        $event->asAccount();
    }

    public function testInvalidPayloadsAreRejected(): void
    {
        $rejected = 0;
        foreach (['not-json', '{"event":"account.created"}', '{"event":"account.created","data":[1]}'] as $body) {
            try {
                $this->parser->parse($body);
            } catch (InvalidWebhookPayload) {
                $rejected++;
            }
        }

        self::assertSame(3, $rejected);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function json(array $payload): string
    {
        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    private function invoiceData(string $status): array
    {
        return [
            'uuid' => 'b84948d2-30b8-4060-a5b4-6aa92db59b50',
            'invoice_number' => 2193,
            'account_uuid' => '5c838347-d3ce-40fb-951c-b3190ccf2cba',
            'account_code' => 'important_client',
            'date' => '2018-11-20T11:25:23.889915+00:00',
            'due_date' => '2018-11-30T11:25:23.889915+00:00',
            'invoice_api_url' => 'https://app.fenerum.com/api/v1/invoices/b84948d2-30b8-4060-a5b4-6aa92db59b50/',
            'currency' => 'DKK',
            'status' => $status,
            'collection_method' => 'payment_card',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function subscriptionData(): array
    {
        return [
            'uuid' => 'b84948d2-30b8-4060-a5b4-6aa92db59b50',
            'account_uuid' => '5c838347-d3ce-40fb-951c-b3190ccf2cba',
            'account_code' => 'important_client',
            'terms_uuid' => '320ac230-a004-4cb3-9caa-fca0bee3e053',
            'start_date' => '2018-01-21T11:25:23.889915+00:00',
            'end_date' => null,
            'next_renewal_date' => '2018-11-21T11:25:23.889915+00:00',
            'quantity' => 1.5,
            'collection_method' => 'payment_card',
            'payment_terms' => 10,
            'po_number' => 'A21',
        ];
    }
}
