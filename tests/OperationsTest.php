<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests;

use Kevin4845\LaravelFenerum\Enum\CollectionMethod;

final class OperationsTest extends FenerumTestCase
{
    public function testInvoiceActionsUseThePublishedBodies(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['detail' => 'accepted']),
            new \GuzzleHttp\Psr7\Response(200, [], ''),
            $this->jsonResponse(200, ['detail' => 'applied']),
            $this->jsonResponse(200, ['detail' => 'changed']),
            new \GuzzleHttp\Psr7\Response(202, [], ''),
            $this->jsonResponse(201, ['uuid' => 'debt-1']),
        ]);

        $client->invoices()->acceptLoss('inv-1', '2026-01-02');
        $client->invoices()->applyCreditNote('inv-1');
        $client->invoices()->applyCredit('inv-1', 'inv-2', '10.00');
        $client->invoices()->changeCollectionMethod('inv-1', CollectionMethod::Invoice);
        $client->invoices()->resend('inv-1');
        $sent = $client->invoices()->sendToDebtCollection('inv-1', true, 'ops@example.test');

        self::assertSame('debt-1', $sent['uuid']);
        self::assertSame(
            'https://fenerum.test/api/v1/invoices/inv-1/accept-loss/',
            (string) $this->requestAt(0)->getUri(),
        );
        self::assertSame('{"registration_date":"2026-01-02"}', (string) $this->requestAt(0)->getBody());
        self::assertSame('POST', $this->requestAt(1)->getMethod());
        self::assertSame('', (string) $this->requestAt(1)->getBody());
        self::assertSame(
            '{"target_invoice":"inv-2","amount":"10.00"}',
            (string) $this->requestAt(2)->getBody(),
        );
        self::assertSame(
            '{"collection_method":"invoice"}',
            (string) $this->requestAt(3)->getBody(),
        );
        self::assertSame('{}', (string) $this->requestAt(4)->getBody());
        self::assertSame(
            '{"email":"ops@example.test","accept_terms":true}',
            (string) $this->requestAt(5)->getBody(),
        );
    }

    public function testExportJobAndDownloadUseThePublishedPaths(): void
    {
        $client = $this->client([
            $this->jsonResponse(202, ['export_id' => 'exp-1', 'job_uuid' => 'job-1']),
            $this->jsonResponse(202, ['export_id' => 'exp-2', 'job_uuid' => 'job-2']),
            $this->jsonResponse(200, ['url' => 'https://files.example.test/exp-1']),
        ]);

        $job = $client->accounts()->export();
        $invoiceJob = $client->invoices()->export();
        $download = $client->exports()->download('exp-1');

        self::assertSame('exp-1', $job['export_id']);
        self::assertSame('exp-2', $invoiceJob['export_id']);
        self::assertSame('https://files.example.test/exp-1', $download['url']);
        self::assertSame('POST', $this->requestAt(0)->getMethod());
        self::assertSame('https://fenerum.test/api/v1/accounts/export/', (string) $this->requestAt(0)->getUri());
        self::assertSame('{}', (string) $this->requestAt(0)->getBody());
        self::assertSame('https://fenerum.test/api/v1/invoices/export/', (string) $this->requestAt(1)->getUri());
        self::assertSame('{}', (string) $this->requestAt(1)->getBody());
        self::assertSame(
            'https://fenerum.test/api/v1/exports/exp-1/download/',
            (string) $this->requestAt(2)->getUri(),
        );
    }

    public function testKpiExportReturnsTheRawSpreadsheet(): void
    {
        $client = $this->client([
            new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/vnd.ms-excel'], 'spreadsheet-bytes'),
        ]);

        $export = $client->kpis()->export('mrr', ['breakdown.0.dimension' => 'country']);

        self::assertSame(200, $export->statusCode);
        self::assertSame('spreadsheet-bytes', $export->body);
        self::assertSame('application/vnd.ms-excel', $export->contentType);
        self::assertSame('GET', $this->lastRequest()->getMethod());
        self::assertSame('/api/v1/kpis/mrr/export/', $this->lastRequest()->getUri()->getPath());
        self::assertSame('breakdown.0.dimension=country', $this->lastRequest()->getUri()->getQuery());
    }

    public function testPaymentGatewaysAreSentAsAJsonArray(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, [[
                'payment_gateway' => 'stripe',
                'payment_gateway_account_id' => 'cus_1',
            ]]),
        ]);

        $client->accounts()->updatePaymentGateways('CUST001', [[
            'payment_gateway' => 'stripe',
            'payment_gateway_account_id' => 'cus_1',
        ]]);

        self::assertSame('PATCH', $this->lastRequest()->getMethod());
        self::assertSame(
            '[{"payment_gateway":"stripe","payment_gateway_account_id":"cus_1"}]',
            (string) $this->lastRequest()->getBody(),
        );
    }

    public function testFollowingSubscriptionIsCreatedOnTheDocumentedPath(): void
    {
        $client = $this->client([
            $this->jsonResponse(200, ['uuid' => 'sub-2']),
        ]);

        $client->subscriptions()->addFollowing('sub-1', [
            'terms' => 'terms-9',
            'quantity' => 2,
        ]);

        self::assertSame('POST', $this->lastRequest()->getMethod());
        self::assertSame(
            'https://fenerum.test/api/v1/subscriptions/sub-1/add-following/',
            (string) $this->lastRequest()->getUri(),
        );
        self::assertSame(
            ['terms' => 'terms-9', 'quantity' => 2],
            json_decode((string) $this->lastRequest()->getBody(), true),
        );
    }
}
