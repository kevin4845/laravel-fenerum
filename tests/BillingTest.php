<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Kevin4845\LaravelFenerum\Enum\CancellationType;
use Kevin4845\LaravelFenerum\Enum\CollectionMethod;
use Kevin4845\LaravelFenerum\Enum\LookupField;
use Kevin4845\LaravelFenerum\Enum\SubscriptionChangeType;
use Kevin4845\LaravelFenerum\Exception\InvalidResponseException;

final class BillingTest extends FenerumTestCase
{
    public function testSubscribeSendsTheDocumentedBody(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-1', 'quantity' => 2]),
        ]);

        $subscription = $client->accounts()->subscribe(
            'CUST001',
            'term-1',
            2,
            CollectionMethod::Invoice,
            false,
        );

        self::assertSame('sub-1', $subscription['uuid']);
        self::assertSame('POST', $this->lastRequest()->getMethod());
        self::assertSame('https://fenerum.test/api/v1/accounts/CUST001/subscribe/', (string) $this->lastRequest()->getUri());
        self::assertSame(
            [
                'terms' => 'term-1',
                'quantity' => 2,
                'collection_method' => 'invoice',
                'group_on_invoice' => false,
            ],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testGenerateInvoiceUsesTheCurrentPath(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [['uuid' => 'inv-1', 'currency' => 'DKK']]),
        ]);

        $invoices = $client->accounts()->generateInvoice('CUST001', CollectionMethod::PaymentCard, 14);

        self::assertSame('inv-1', $invoices[0]['uuid']);
        self::assertSame(
            'https://fenerum.test/api/v1/accounts/CUST001/generate-invoice/',
            (string) $this->lastRequest()->getUri(),
        );
        self::assertSame(
            ['collection_method' => 'payment_card', 'payment_terms' => 14],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testArchiveSendsNoJsonBody(): void
    {
        $client = $this->client([new Response(204)]);

        $client->accounts()->archive('CUST001');

        self::assertSame('POST', $this->lastRequest()->getMethod());
        self::assertSame('https://fenerum.test/api/v1/accounts/CUST001/archive/', (string) $this->lastRequest()->getUri());
        self::assertSame('', (string) $this->lastRequest()->getBody());
    }

    public function testQuantityChangeIsForwardedWithoutALocalPrice(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-1', 'quantity' => 5]),
        ]);

        $updated = $client->subscriptions()->changeQuantity(
            'sub-1',
            5,
            SubscriptionChangeType::AtSpecifiedTime,
            ['prorate_date' => '2026-11-01T00:00:00Z'],
        );

        self::assertSame(5, $updated['quantity']);
        self::assertSame('PATCH', $this->lastRequest()->getMethod());
        self::assertSame('https://fenerum.test/api/v1/subscriptions/sub-1/', (string) $this->lastRequest()->getUri());
        self::assertSame(
            [
                'prorate_date' => '2026-11-01T00:00:00Z',
                'quantity' => 5,
                'change_type' => 'at_specified_time',
            ],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testImmediateQuantityChangeCanForwardAFractionalQuantity(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-1']),
        ]);

        $client->subscriptions()->changeQuantity('sub-1', 1.5, SubscriptionChangeType::Immediate);

        self::assertSame(
            ['quantity' => 1.5, 'change_type' => 'immediate'],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testQuantityChangeCanBeScheduledForRenewal(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-1', 'quantity' => 4]),
        ]);

        $client->subscriptions()->changeQuantity('sub-1', 4, SubscriptionChangeType::AtRenewal);

        self::assertSame(
            ['quantity' => 4, 'change_type' => 'at_renewal'],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testPlanTermChangeIsASubscriptionUpdate(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-1']),
        ]);

        $client->subscriptions()->changeTerms(
            'sub-1',
            'terms-2',
            SubscriptionChangeType::AtSpecifiedTime,
            ['prorate_date' => '2026-11-01T00:00:00Z'],
        );

        self::assertSame('PATCH', $this->lastRequest()->getMethod());
        self::assertSame(
            [
                'prorate_date' => '2026-11-01T00:00:00Z',
                'terms' => 'terms-2',
                'change_type' => 'at_specified_time',
            ],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testCancellationSendsTheDocumentedType(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-1', 'status' => 'not_renewing']),
        ]);

        $subscription = $client->subscriptions()->cancel('sub-1', CancellationType::AtRenewal, [
            'cancel_renewal' => 1,
        ]);

        self::assertSame('not_renewing', $subscription['status']);
        self::assertSame('POST', $this->lastRequest()->getMethod());
        self::assertSame('https://fenerum.test/api/v1/subscriptions/sub-1/cancel/', (string) $this->lastRequest()->getUri());
        self::assertSame(
            ['cancel_renewal' => 1, 'cancellation_type' => 'at_renewal'],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }

    public function testCancellationRejectsARenewalIndexOutsideTheSchema(): void
    {
        $client = $this->client([]);

        $this->expectException(InvalidArgumentException::class);
        $client->subscriptions()->cancel('sub-1', CancellationType::AtRenewal, ['cancel_renewal' => 24]);
    }

    public function testScheduledChangeCanBeCancelled(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'change-1', 'state' => 'cancelled']),
        ]);

        $change = $client->subscriptionChanges()->cancel('sub-1', 'change-1');

        self::assertSame('cancelled', $change['state']);
        self::assertSame(
            'https://fenerum.test/api/v1/subscriptions/sub-1/changes/change-1/cancel/',
            (string) $this->lastRequest()->getUri(),
        );
        self::assertSame('{}', (string) $this->lastRequest()->getBody());
    }

    public function testSubscriptionChangeListIsPaginated(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [
                'count' => 1,
                'next' => 'https://app.fenerum.com/api/v1/subscriptions/sub-1/changes/?page=2',
                'previous' => null,
                'results' => [['uuid' => 'change-1', 'change_type' => 'at_renewal', 'state' => 'scheduled']],
            ]),
        ]);

        $page = $client->subscriptionChanges()->list('sub-1', ['page' => 1, 'page_size' => 20]);

        self::assertSame('scheduled', $page->results[0]['state']);
        self::assertSame('page=1&page_size=20', $this->lastRequest()->getUri()->getQuery());
    }

    public function testPlanLookupUsesTheDocumentedHeader(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'plan-1', 'code' => 'enterprise']),
        ]);

        $plan = $client->plans()->get('enterprise', LookupField::Code);

        self::assertSame('plan-1', $plan['uuid']);
        self::assertSame('code', $this->lastRequest()->getHeaderLine('Lookup-Field'));
        self::assertSame('https://fenerum.test/api/v1/plans/enterprise/', (string) $this->lastRequest()->getUri());
    }

    public function testPlanTermsListIsAnArray(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [
                ['uuid' => 'term-1', 'currency' => 'DKK', 'price' => '99.00'],
            ]),
        ]);

        $terms = $client->planTerms()->list('enterprise', ['code' => 'monthly'], LookupField::Code);

        self::assertSame('term-1', $terms[0]['uuid']);
        self::assertSame('code', $this->lastRequest()->getHeaderLine('Lookup-Field'));
        self::assertSame(
            'https://fenerum.test/api/v1/plans/enterprise/terms/?code=monthly',
            (string) $this->lastRequest()->getUri(),
        );
    }

    public function testProductPricingListIsAnArray(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [
                ['uuid' => 'price-1', 'price' => '10.00', 'currency' => 'EUR'],
            ]),
        ]);

        $prices = $client->productPricings()->list('product-1');

        self::assertSame('EUR', $prices[0]['currency']);
        self::assertSame(
            'https://fenerum.test/api/v1/products/product-1/pricings/',
            (string) $this->lastRequest()->getUri(),
        );
    }

    public function testInvoicePaymentAndRefundUseTheDocumentedBodies(): void
    {
        $client = $this->client([
            new Response(202),
            $this->jsonResponse(200, ['uuid' => 'credit-1']),
        ]);

        self::assertNull($client->invoices()->pay('inv-1', CollectionMethod::Invoice));
        self::assertSame(
            ['collection_method' => 'invoice'],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );

        $credit = $client->invoices()->refund('inv-1');

        self::assertSame('credit-1', $credit['uuid']);
        $refund = $this->requestAt(1);
        self::assertSame('{}', (string) $refund->getBody());
        self::assertSame(
            'https://fenerum.test/api/v1/invoices/inv-1/refund/',
            (string) $refund->getUri(),
        );
    }

    public function testAListWithoutThePageEnvelopeIsRejected(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [['code' => 'CUST001']]),
        ]);

        $this->expectException(InvalidResponseException::class);
        $client->accounts()->list();
    }
}
