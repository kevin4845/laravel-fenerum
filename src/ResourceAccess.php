<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum;

use Kevin4845\LaravelFenerum\Resource\AccountBudgets;
use Kevin4845\LaravelFenerum\Resource\Activities;
use Kevin4845\LaravelFenerum\Resource\AuditLogs;
use Kevin4845\LaravelFenerum\Resource\BankAccounts;
use Kevin4845\LaravelFenerum\Resource\BudgetScenarios;
use Kevin4845\LaravelFenerum\Resource\Budgets;
use Kevin4845\LaravelFenerum\Resource\BusinessEntities;
use Kevin4845\LaravelFenerum\Resource\CashFlow;
use Kevin4845\LaravelFenerum\Resource\ChurnReasons;
use Kevin4845\LaravelFenerum\Resource\ContractTiers;
use Kevin4845\LaravelFenerum\Resource\Contracts;
use Kevin4845\LaravelFenerum\Resource\DirectDebitDebtors;
use Kevin4845\LaravelFenerum\Resource\DraftInvoiceLineIndex;
use Kevin4845\LaravelFenerum\Resource\DraftInvoiceLineProducts;
use Kevin4845\LaravelFenerum\Resource\DraftInvoiceLines;
use Kevin4845\LaravelFenerum\Resource\EInvoiceFieldMappings;
use Kevin4845\LaravelFenerum\Resource\ErpAccounts;
use Kevin4845\LaravelFenerum\Resource\ErpLines;
use Kevin4845\LaravelFenerum\Resource\ErpVatCodes;
use Kevin4845\LaravelFenerum\Resource\Exports;
use Kevin4845\LaravelFenerum\Resource\Features;
use Kevin4845\LaravelFenerum\Resource\InvoiceLines;
use Kevin4845\LaravelFenerum\Resource\Kpis;
use Kevin4845\LaravelFenerum\Resource\OrganizationMembers;
use Kevin4845\LaravelFenerum\Resource\PaymentCards;
use Kevin4845\LaravelFenerum\Resource\Payments;
use Kevin4845\LaravelFenerum\Resource\PlanCatalog;
use Kevin4845\LaravelFenerum\Resource\PricingSimulator;
use Kevin4845\LaravelFenerum\Resource\Receipts;
use Kevin4845\LaravelFenerum\Resource\Recipients;
use Kevin4845\LaravelFenerum\Resource\Reports;
use Kevin4845\LaravelFenerum\Resource\RevenueGroupForms;
use Kevin4845\LaravelFenerum\Resource\RevenueGroupIndex;
use Kevin4845\LaravelFenerum\Resource\RevenueGroups;
use Kevin4845\LaravelFenerum\Resource\RevenueRecognition;
use Kevin4845\LaravelFenerum\Resource\Search;
use Kevin4845\LaravelFenerum\Resource\SelfService;
use Kevin4845\LaravelFenerum\Resource\SentEmails;
use Kevin4845\LaravelFenerum\Resource\SupplierInvoices;
use Kevin4845\LaravelFenerum\Resource\Suppliers;
use Kevin4845\LaravelFenerum\Resource\Transactions;
use Kevin4845\LaravelFenerum\Resource\VatPayments;
use Kevin4845\LaravelFenerum\Resource\WebhookEventMetadata;

trait ResourceAccess
{
    private ?AccountBudgets $accountBudgets = null;

    public function accountBudgets(): AccountBudgets
    {
        return $this->accountBudgets ??= new AccountBudgets($this);
    }

    private ?Activities $activities = null;

    public function activities(): Activities
    {
        return $this->activities ??= new Activities($this);
    }

    private ?AuditLogs $auditLogs = null;

    public function auditLogs(): AuditLogs
    {
        return $this->auditLogs ??= new AuditLogs($this);
    }

    private ?BankAccounts $bankAccounts = null;

    public function bankAccounts(): BankAccounts
    {
        return $this->bankAccounts ??= new BankAccounts($this);
    }

    private ?BudgetScenarios $budgetScenarios = null;

    public function budgetScenarios(): BudgetScenarios
    {
        return $this->budgetScenarios ??= new BudgetScenarios($this);
    }

    private ?Budgets $budgets = null;

    public function budgets(): Budgets
    {
        return $this->budgets ??= new Budgets($this);
    }

    private ?BusinessEntities $businessEntities = null;

    public function businessEntities(): BusinessEntities
    {
        return $this->businessEntities ??= new BusinessEntities($this);
    }

    private ?CashFlow $cashFlow = null;

    public function cashFlow(): CashFlow
    {
        return $this->cashFlow ??= new CashFlow($this);
    }

    private ?ChurnReasons $churnReasons = null;

    public function churnReasons(): ChurnReasons
    {
        return $this->churnReasons ??= new ChurnReasons($this);
    }

    private ?ContractTiers $contractTiers = null;

    public function contractTiers(): ContractTiers
    {
        return $this->contractTiers ??= new ContractTiers($this);
    }

    private ?Contracts $contracts = null;

    public function contracts(): Contracts
    {
        return $this->contracts ??= new Contracts($this);
    }

    private ?DirectDebitDebtors $directDebitDebtors = null;

    public function directDebitDebtors(): DirectDebitDebtors
    {
        return $this->directDebitDebtors ??= new DirectDebitDebtors($this);
    }

    private ?DraftInvoiceLineIndex $draftInvoiceLineIndex = null;

    public function draftInvoiceLineIndex(): DraftInvoiceLineIndex
    {
        return $this->draftInvoiceLineIndex ??= new DraftInvoiceLineIndex($this);
    }

    private ?DraftInvoiceLineProducts $draftInvoiceLineProducts = null;

    public function draftInvoiceLineProducts(): DraftInvoiceLineProducts
    {
        return $this->draftInvoiceLineProducts ??= new DraftInvoiceLineProducts($this);
    }

    private ?DraftInvoiceLines $draftInvoiceLines = null;

    public function draftInvoiceLines(): DraftInvoiceLines
    {
        return $this->draftInvoiceLines ??= new DraftInvoiceLines($this);
    }

    private ?EInvoiceFieldMappings $eInvoiceFieldMappings = null;

    public function eInvoiceFieldMappings(): EInvoiceFieldMappings
    {
        return $this->eInvoiceFieldMappings ??= new EInvoiceFieldMappings($this);
    }

    private ?ErpAccounts $erpAccounts = null;

    public function erpAccounts(): ErpAccounts
    {
        return $this->erpAccounts ??= new ErpAccounts($this);
    }

    private ?ErpLines $erpLines = null;

    public function erpLines(): ErpLines
    {
        return $this->erpLines ??= new ErpLines($this);
    }

    private ?ErpVatCodes $erpVatCodes = null;

    public function erpVatCodes(): ErpVatCodes
    {
        return $this->erpVatCodes ??= new ErpVatCodes($this);
    }

    private ?Exports $exports = null;

    public function exports(): Exports
    {
        return $this->exports ??= new Exports($this);
    }

    private ?Features $features = null;

    public function features(): Features
    {
        return $this->features ??= new Features($this);
    }

    private ?InvoiceLines $invoiceLines = null;

    public function invoiceLines(): InvoiceLines
    {
        return $this->invoiceLines ??= new InvoiceLines($this);
    }

    private ?Kpis $kpis = null;

    public function kpis(): Kpis
    {
        return $this->kpis ??= new Kpis($this);
    }

    private ?OrganizationMembers $organizationMembers = null;

    public function organizationMembers(): OrganizationMembers
    {
        return $this->organizationMembers ??= new OrganizationMembers($this);
    }

    private ?PaymentCards $paymentCards = null;

    public function paymentCards(): PaymentCards
    {
        return $this->paymentCards ??= new PaymentCards($this);
    }

    private ?Payments $payments = null;

    public function payments(): Payments
    {
        return $this->payments ??= new Payments($this);
    }

    private ?PlanCatalog $planCatalog = null;

    public function planCatalog(): PlanCatalog
    {
        return $this->planCatalog ??= new PlanCatalog($this);
    }

    private ?PricingSimulator $pricingSimulator = null;

    public function pricingSimulator(): PricingSimulator
    {
        return $this->pricingSimulator ??= new PricingSimulator($this);
    }

    private ?Receipts $receipts = null;

    public function receipts(): Receipts
    {
        return $this->receipts ??= new Receipts($this);
    }

    private ?Recipients $recipients = null;

    public function recipients(): Recipients
    {
        return $this->recipients ??= new Recipients($this);
    }

    private ?Reports $reports = null;

    public function reports(): Reports
    {
        return $this->reports ??= new Reports($this);
    }

    private ?RevenueGroupForms $revenueGroupForms = null;

    public function revenueGroupForms(): RevenueGroupForms
    {
        return $this->revenueGroupForms ??= new RevenueGroupForms($this);
    }

    private ?RevenueGroupIndex $revenueGroupIndex = null;

    public function revenueGroupIndex(): RevenueGroupIndex
    {
        return $this->revenueGroupIndex ??= new RevenueGroupIndex($this);
    }

    private ?RevenueGroups $revenueGroups = null;

    public function revenueGroups(): RevenueGroups
    {
        return $this->revenueGroups ??= new RevenueGroups($this);
    }

    private ?RevenueRecognition $revenueRecognition = null;

    public function revenueRecognition(): RevenueRecognition
    {
        return $this->revenueRecognition ??= new RevenueRecognition($this);
    }

    private ?Search $search = null;

    public function search(): Search
    {
        return $this->search ??= new Search($this);
    }

    private ?SelfService $selfService = null;

    public function selfService(): SelfService
    {
        return $this->selfService ??= new SelfService($this);
    }

    private ?SentEmails $sentEmails = null;

    public function sentEmails(): SentEmails
    {
        return $this->sentEmails ??= new SentEmails($this);
    }

    private ?SupplierInvoices $supplierInvoices = null;

    public function supplierInvoices(): SupplierInvoices
    {
        return $this->supplierInvoices ??= new SupplierInvoices($this);
    }

    private ?Suppliers $suppliers = null;

    public function suppliers(): Suppliers
    {
        return $this->suppliers ??= new Suppliers($this);
    }

    private ?Transactions $transactions = null;

    public function transactions(): Transactions
    {
        return $this->transactions ??= new Transactions($this);
    }

    private ?VatPayments $vatPayments = null;

    public function vatPayments(): VatPayments
    {
        return $this->vatPayments ??= new VatPayments($this);
    }

    private ?WebhookEventMetadata $webhookEventMetadata = null;

    public function webhookEventMetadata(): WebhookEventMetadata
    {
        return $this->webhookEventMetadata ??= new WebhookEventMetadata($this);
    }
}
