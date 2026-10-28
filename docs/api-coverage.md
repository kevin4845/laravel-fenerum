# Fenerum API coverage

Schema: Fenerum API v1, retrieved on 2026-10-28 from `https://app.fenerum.com/api/swagger/?format=openapi`.

This report maps every HTTP operation in that schema, except the incoming `your-endpoint-for-*` callback paths, which are events this client parses rather than calls.

Operations: 258. Implemented: 250. Deprecated and still callable: 6. Unsupported: 2.
Dedicated PHPUnit coverage: 29. Operations without a dedicated test: 229.

An operation marked `deprecated` is implemented and calls the path OpenAPI still publishes. Prefer the non-deprecated path when one exists.

A missing dedicated test does not mean the shared HTTP client is untested. Authentication, errors, pagination, and retries are covered on the shared request path. Only the operations below marked `dedicated` have a test that builds that call.

| Method | Path | Client | Status | Test | Notes |
| --- | --- | --- | --- | --- | --- |
| POST | `/api/self-service/initiate-organization/` | `selfService()->initiateOrganization()` | implemented | none |  |
| GET | `/api/v1/account-budgets/` | `accountBudgets()->list()` | implemented | none |  |
| GET | `/api/v1/account-budgets/{id}/` | `accountBudgets()->get()` | implemented | none |  |
| GET | `/api/v1/accounts/` | `accounts()->list()` | implemented | dedicated |  |
| POST | `/api/v1/accounts/` | `accounts()->create()` | implemented | dedicated |  |
| POST | `/api/v1/accounts/export-balance/` | `accounts()->exportBalance()` | implemented | none | Request schema Account is ambiguous for this response. |
| POST | `/api/v1/accounts/export-contract-tiers/` | `accounts()->exportContractTiers()` | implemented | none | Request schema Account is ambiguous for this response. |
| POST | `/api/v1/accounts/export-contracts/` | `accounts()->exportContracts()` | implemented | none | Request schema Account is ambiguous for this response. |
| POST | `/api/v1/accounts/export-mrr-per-month-report/` | `accounts()->exportMrrPerMonthReport()` | implemented | none |  |
| POST | `/api/v1/accounts/export/` | `accounts()->export()` | implemented | dedicated |  |
| DELETE | `/api/v1/accounts/{code}/` | `accounts()->delete()` | implemented | none |  |
| GET | `/api/v1/accounts/{code}/` | `accounts()->get()` | implemented | dedicated |  |
| PATCH | `/api/v1/accounts/{code}/` | `accounts()->patch()` | implemented | none |  |
| PUT | `/api/v1/accounts/{code}/` | `accounts()->put()` | implemented | none |  |
| POST | `/api/v1/accounts/{code}/archive/` | `accounts()->archive()` | implemented | dedicated | No JSON body is sent. OpenAPI attaches the Account serializer, documents no archive fields, and returns 204. |
| GET | `/api/v1/accounts/{code}/card-registration-link/{gateway}/` | `accounts()->getCardRegistrationLink()` | implemented | none |  |
| GET | `/api/v1/accounts/{code}/card_registration_link/{gateway}/` | `accounts()->getCardRegistrationLinkLegacy()` | deprecated | none |  |
| GET | `/api/v1/accounts/{code}/contracts/` | `contracts()->list()` | implemented | none |  |
| POST | `/api/v1/accounts/{code}/contracts/` | `contracts()->create()` | implemented | none |  |
| GET | `/api/v1/accounts/{code}/contracts/{contract_pk}/tiers/` | `contractTiers()->list()` | implemented | none |  |
| POST | `/api/v1/accounts/{code}/contracts/{contract_pk}/tiers/` | `contractTiers()->create()` | implemented | none |  |
| DELETE | `/api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/` | `contractTiers()->delete()` | implemented | none |  |
| GET | `/api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/` | `contractTiers()->get()` | implemented | none |  |
| PATCH | `/api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/` | `contractTiers()->patch()` | implemented | none |  |
| PUT | `/api/v1/accounts/{code}/contracts/{contract_pk}/tiers/{id}/` | `contractTiers()->put()` | implemented | none |  |
| DELETE | `/api/v1/accounts/{code}/contracts/{uuid}/` | `contracts()->delete()` | implemented | none |  |
| GET | `/api/v1/accounts/{code}/contracts/{uuid}/` | `contracts()->get()` | implemented | none |  |
| PATCH | `/api/v1/accounts/{code}/contracts/{uuid}/` | `contracts()->patch()` | implemented | none |  |
| PUT | `/api/v1/accounts/{code}/contracts/{uuid}/` | `contracts()->put()` | implemented | none |  |
| POST | `/api/v1/accounts/{code}/generate-invoice/` | `accounts()->generateInvoice()` | implemented | dedicated |  |
| POST | `/api/v1/accounts/{code}/generate_invoice/` | `accounts()->generateInvoiceLegacy()` | deprecated | none |  |
| GET | `/api/v1/accounts/{code}/payment-gateways/` | `accounts()->paymentGateways()` | implemented | none |  |
| PATCH | `/api/v1/accounts/{code}/payment-gateways/` | `accounts()->updatePaymentGateways()` | implemented | dedicated |  |
| POST | `/api/v1/accounts/{code}/restore/` | `accounts()->restore()` | implemented | none | No JSON body is sent. Same request-schema gap as archive. |
| POST | `/api/v1/accounts/{code}/start-pending-subscriptions/` | `accounts()->startPendingSubscriptions()` | implemented | none |  |
| POST | `/api/v1/accounts/{code}/subscribe/` | `accounts()->subscribe()` | implemented | dedicated |  |
| GET | `/api/v1/activities/` | `activities()->list()` | implemented | none |  |
| POST | `/api/v1/activities/` | `activities()->create()` | implemented | none |  |
| GET | `/api/v1/activities/account/` | `activities()->account()` | implemented | none |  |
| POST | `/api/v1/activities/account/export/` | `activities()->accountExport()` | implemented | none |  |
| GET | `/api/v1/activities/account/{uuid}/` | `activities()->getAccount()` | implemented | none |  |
| DELETE | `/api/v1/activities/delete/` | `activities()->deleteMatching()` | implemented | none | The description says matching filters are used, but the operation lists no query parameters. Names passed in the query are unverified. |
| POST | `/api/v1/activities/export/` | `activities()->export()` | implemented | none | Request schema Activity is ambiguous for this response. |
| DELETE | `/api/v1/activities/{uuid}/` | `activities()->delete()` | implemented | none |  |
| GET | `/api/v1/activities/{uuid}/` | `activities()->get()` | implemented | none |  |
| PATCH | `/api/v1/activities/{uuid}/` | `activities()->patch()` | implemented | none |  |
| PUT | `/api/v1/activities/{uuid}/` | `activities()->put()` | implemented | none |  |
| GET | `/api/v1/audit-logs/` | `auditLogs()->list()` | implemented | none |  |
| POST | `/api/v1/audit-logs/export/` | `auditLogs()->export()` | implemented | none |  |
| GET | `/api/v1/audit-logs/{uuid}/` | `auditLogs()->get()` | implemented | none |  |
| GET | `/api/v1/bank-accounts/` | `bankAccounts()->list()` | implemented | none |  |
| GET | `/api/v1/bank-accounts/cash-balance/` | `bankAccounts()->cashBalance()` | implemented | none |  |
| GET | `/api/v1/bank-accounts/cash-flow/` | `bankAccounts()->cashFlow()` | implemented | none |  |
| GET | `/api/v1/bank-accounts/{id}/` | `bankAccounts()->get()` | implemented | none |  |
| GET | `/api/v1/budget-scenarios/` | `budgetScenarios()->list()` | implemented | none |  |
| GET | `/api/v1/budget-scenarios/{uuid}/` | `budgetScenarios()->get()` | implemented | none |  |
| GET | `/api/v1/budgets-list/` | `budgets()->list()` | implemented | none |  |
| GET | `/api/v1/business-entities/` | `businessEntities()->list()` | implemented | none |  |
| POST | `/api/v1/business-entities/` | `businessEntities()->create()` | implemented | none |  |
| DELETE | `/api/v1/business-entities/{uuid}/` | `businessEntities()->delete()` | implemented | none |  |
| GET | `/api/v1/business-entities/{uuid}/` | `businessEntities()->get()` | implemented | none |  |
| PATCH | `/api/v1/business-entities/{uuid}/` | `businessEntities()->patch()` | implemented | none |  |
| PUT | `/api/v1/business-entities/{uuid}/` | `businessEntities()->put()` | implemented | none |  |
| GET | `/api/v1/cash-flow/{month}/` | `cashFlow()->get()` | implemented | none |  |
| GET | `/api/v1/churn-reasons/` | `churnReasons()->list()` | implemented | none |  |
| POST | `/api/v1/churn-reasons/` | `churnReasons()->create()` | implemented | none |  |
| DELETE | `/api/v1/churn-reasons/{uuid}/` | `churnReasons()->delete()` | implemented | none |  |
| GET | `/api/v1/churn-reasons/{uuid}/` | `churnReasons()->get()` | implemented | none |  |
| PATCH | `/api/v1/churn-reasons/{uuid}/` | `churnReasons()->patch()` | implemented | none |  |
| PUT | `/api/v1/churn-reasons/{uuid}/` | `churnReasons()->put()` | implemented | none |  |
| GET | `/api/v1/directdebitdebtors/` | `directDebitDebtors()->list()` | implemented | none |  |
| POST | `/api/v1/directdebitdebtors/` | `directDebitDebtors()->create()` | implemented | none |  |
| POST | `/api/v1/directdebitdebtors/generate/` | `directDebitDebtors()->generate()` | implemented | none |  |
| GET | `/api/v1/directdebitdebtors/{uuid}/` | `directDebitDebtors()->get()` | implemented | none |  |
| POST | `/api/v1/directdebitdebtors/{uuid}/disable/` | `directDebitDebtors()->disable()` | implemented | none |  |
| GET | `/api/v1/draft-invoice-lines-list/` | `draftInvoiceLineIndex()->list()` | implemented | none |  |
| POST | `/api/v1/draft-invoice-lines-list/export/` | `draftInvoiceLineIndex()->export()` | implemented | none |  |
| GET | `/api/v1/draft-invoice-lines-list/{uuid}/` | `draftInvoiceLineIndex()->get()` | implemented | none |  |
| GET | `/api/v1/draft-invoice-lines/products/` | `draftInvoiceLineProducts()->list()` | implemented | none |  |
| POST | `/api/v1/draft-invoice-lines/products/` | `draftInvoiceLineProducts()->create()` | implemented | none |  |
| DELETE | `/api/v1/draft-invoice-lines/products/{uuid}/` | `draftInvoiceLineProducts()->delete()` | implemented | none |  |
| GET | `/api/v1/draft-invoice-lines/products/{uuid}/` | `draftInvoiceLineProducts()->get()` | implemented | none |  |
| PATCH | `/api/v1/draft-invoice-lines/products/{uuid}/` | `draftInvoiceLineProducts()->patch()` | implemented | none |  |
| PUT | `/api/v1/draft-invoice-lines/products/{uuid}/` | `draftInvoiceLineProducts()->put()` | implemented | none |  |
| GET | `/api/v1/draftinvoicelines/` | `draftInvoiceLines()->list()` | implemented | none |  |
| POST | `/api/v1/draftinvoicelines/` | `draftInvoiceLines()->create()` | implemented | none |  |
| DELETE | `/api/v1/draftinvoicelines/{uuid}/` | `draftInvoiceLines()->delete()` | implemented | none |  |
| GET | `/api/v1/draftinvoicelines/{uuid}/` | `draftInvoiceLines()->get()` | implemented | none |  |
| PATCH | `/api/v1/draftinvoicelines/{uuid}/` | `draftInvoiceLines()->patch()` | implemented | none |  |
| PUT | `/api/v1/draftinvoicelines/{uuid}/` | `draftInvoiceLines()->put()` | implemented | none |  |
| GET | `/api/v1/einvoice-field-mappings/` | `eInvoiceFieldMappings()->list()` | implemented | none |  |
| GET | `/api/v1/einvoice-field-mappings/available-fields/` | `eInvoiceFieldMappings()->availableFields()` | implemented | none |  |
| PUT | `/api/v1/einvoice-field-mappings/save-mappings/` | `eInvoiceFieldMappings()->saveMappings()` | implemented | none |  |
| GET | `/api/v1/erp-accounts/` | `erpAccounts()->list()` | implemented | none |  |
| POST | `/api/v1/erp-accounts/import/` | `erpAccounts()->import()` | implemented | none |  |
| GET | `/api/v1/erp-accounts/{uuid}/` | `erpAccounts()->get()` | implemented | none |  |
| GET | `/api/v1/erp-lines/` | `erpLines()->list()` | implemented | none |  |
| POST | `/api/v1/erp-lines/bulk-action/` | `erpLines()->bulkAction()` | implemented | none | Request schema ERPAccountLine is ambiguous for this response. |
| POST | `/api/v1/erp-lines/export/` | `erpLines()->export()` | implemented | none |  |
| GET | `/api/v1/erp-lines/{id}/` | `erpLines()->get()` | implemented | none |  |
| PATCH | `/api/v1/erp-lines/{id}/` | `erpLines()->patch()` | implemented | none |  |
| POST | `/api/v1/erp-lines/{id}/cancel/` | `erpLines()->cancel()` | implemented | none | Request schema ERPAccountLine is ambiguous for this response. |
| POST | `/api/v1/erp-lines/{id}/resend/` | `erpLines()->resend()` | implemented | none |  |
| GET | `/api/v1/erp-vat-codes/` | `erpVatCodes()->list()` | implemented | none |  |
| POST | `/api/v1/erp-vat-codes/import/` | `erpVatCodes()->import()` | implemented | none |  |
| GET | `/api/v1/erp-vat-codes/{uuid}/` | `erpVatCodes()->get()` | implemented | none |  |
| POST | `/api/v1/expense-documents/upload/` | `` | unsupported | none | ExpenseDocumentUpload requires files, but each item is a read-only URI and the operation does not document a multipart body. Left unsupported so the client does not invent an upload format. |
| GET | `/api/v1/exports/{id}/download/` | `exports()->download()` | implemented | dedicated | Returns ExportDownload.url. The schema does not say whether {id} is export_id or job_uuid. |
| GET | `/api/v1/features-list/` | `features()->list()` | implemented | none |  |
| GET | `/api/v1/invoicelines/` | `invoiceLines()->list()` | implemented | none |  |
| GET | `/api/v1/invoicelines/{id}/` | `invoiceLines()->get()` | implemented | none |  |
| GET | `/api/v1/invoices/` | `invoices()->list()` | implemented | none |  |
| GET | `/api/v1/invoices/available-vat-billing-periods/` | `invoices()->availableVatBillingPeriods()` | implemented | none |  |
| POST | `/api/v1/invoices/export-vat-report/` | `invoices()->exportVatReport()` | implemented | none |  |
| POST | `/api/v1/invoices/export/` | `invoices()->export()` | implemented | dedicated |  |
| GET | `/api/v1/invoices/{uuid}/` | `invoices()->get()` | implemented | none |  |
| PATCH | `/api/v1/invoices/{uuid}/` | `` | unsupported | none | The operation publishes no request parameters and no response schema. No payload is sent until Fenerum documents one. |
| POST | `/api/v1/invoices/{uuid}/accept-loss/` | `invoices()->acceptLoss()` | implemented | dedicated | Response schema is not published. |
| POST | `/api/v1/invoices/{uuid}/apply-credit-note/` | `invoices()->applyCreditNote()` | implemented | dedicated | Response schema is not published. |
| POST | `/api/v1/invoices/{uuid}/apply_credit/` | `invoices()->applyCredit()` | implemented | dedicated | Response schema is not published. |
| POST | `/api/v1/invoices/{uuid}/change-collection-method/` | `invoices()->changeCollectionMethod()` | implemented | dedicated | Response schema is not published. |
| POST | `/api/v1/invoices/{uuid}/mark-as-due/` | `invoices()->markAsDue()` | implemented | dedicated | Request schema has no writable fields. The success body is not published. Documented for chargeback invoices. |
| POST | `/api/v1/invoices/{uuid}/pay/` | `invoices()->pay()` | implemented | dedicated | Required collection_method. Success is HTTP 202. HTTP 423 is an API error. The success body is not published. |
| POST | `/api/v1/invoices/{uuid}/refund/` | `invoices()->refund()` | implemented | dedicated | Request schema has no writable fields. The response is the credit note. |
| POST | `/api/v1/invoices/{uuid}/resend/` | `invoices()->resend()` | implemented | dedicated | Response schema is not published. |
| POST | `/api/v1/invoices/{uuid}/send-to-debt-collection/` | `invoices()->sendToDebtCollection()` | implemented | dedicated |  |
| GET | `/api/v1/kpis/` | `kpis()->list()` | implemented | none | Response schema is not published. |
| GET | `/api/v1/kpis/{id}/` | `kpis()->get()` | implemented | none |  |
| POST | `/api/v1/kpis/{id}/` | `kpis()->query()` | implemented | none |  |
| GET | `/api/v1/kpis/{id}/export/` | `kpis()->export()` | implemented | dedicated | Response is raw, not JSON. |
| GET | `/api/v1/organization-members/` | `organizationMembers()->list()` | implemented | none |  |
| GET | `/api/v1/organization-members/available/` | `organizationMembers()->available()` | implemented | none |  |
| GET | `/api/v1/organization-members/current/` | `organizationMembers()->current()` | implemented | none |  |
| GET | `/api/v1/paymentcards/` | `paymentCards()->list()` | implemented | none |  |
| POST | `/api/v1/paymentcards/` | `paymentCards()->create()` | implemented | none |  |
| POST | `/api/v1/paymentcards/initiate-setup/` | `paymentCards()->initiateSetup()` | implemented | none |  |
| GET | `/api/v1/paymentcards/{uuid}/` | `paymentCards()->get()` | implemented | none |  |
| POST | `/api/v1/paymentcards/{uuid}/disable/` | `paymentCards()->disable()` | implemented | none |  |
| GET | `/api/v1/payments-list/` | `payments()->list()` | implemented | none |  |
| POST | `/api/v1/payments-list/export/` | `payments()->export()` | implemented | none |  |
| GET | `/api/v1/payments-list/{id}/` | `payments()->get()` | implemented | none |  |
| GET | `/api/v1/payments-list/{id}/full/` | `payments()->getFull()` | implemented | none |  |
| GET | `/api/v1/plans-list/` | `planCatalog()->list()` | implemented | none |  |
| POST | `/api/v1/plans-list/export/` | `planCatalog()->export()` | implemented | none |  |
| GET | `/api/v1/plans-list/{uuid}/` | `planCatalog()->get()` | implemented | none |  |
| GET | `/api/v1/plans-list/{uuid}/full/` | `planCatalog()->getFull()` | implemented | none |  |
| GET | `/api/v1/plans/` | `plans()->list()` | implemented | dedicated |  |
| POST | `/api/v1/plans/` | `plans()->create()` | implemented | none |  |
| POST | `/api/v1/plans/calculate/` | `plans()->calculate()` | implemented | none |  |
| DELETE | `/api/v1/plans/{identifier}/` | `plans()->delete()` | implemented | none |  |
| GET | `/api/v1/plans/{identifier}/` | `plans()->get()` | implemented | none |  |
| PATCH | `/api/v1/plans/{identifier}/` | `plans()->patch()` | implemented | none |  |
| POST | `/api/v1/plans/{identifier}/activate/` | `plans()->activate()` | implemented | none | No JSON body is sent. The operation returns 204 and documents no fields. |
| POST | `/api/v1/plans/{identifier}/deactivate/` | `plans()->deactivate()` | implemented | none | No JSON body is sent. The operation returns 204 and documents no fields. |
| GET | `/api/v1/plans/{plan_identifier}/terms/` | `planTerms()->list()` | implemented | dedicated |  |
| POST | `/api/v1/plans/{plan_identifier}/terms/` | `planTerms()->create()` | implemented | none |  |
| DELETE | `/api/v1/plans/{plan_identifier}/terms/{identifier}/` | `planTerms()->delete()` | implemented | none |  |
| GET | `/api/v1/plans/{plan_identifier}/terms/{identifier}/` | `planTerms()->get()` | implemented | none |  |
| PATCH | `/api/v1/plans/{plan_identifier}/terms/{identifier}/` | `planTerms()->patch()` | implemented | none |  |
| POST | `/api/v1/plans/{plan_identifier}/terms/{identifier}/activate/` | `planTerms()->activate()` | implemented | none | No JSON body is sent. The operation returns 204 and documents no fields. |
| POST | `/api/v1/plans/{plan_identifier}/terms/{identifier}/deactivate/` | `planTerms()->deactivate()` | implemented | none | No JSON body is sent. The operation returns 204 and documents no fields. |
| POST | `/api/v1/pricing-simulator/simulate/` | `pricingSimulator()->simulate()` | implemented | none |  |
| GET | `/api/v1/products/` | `products()->list()` | implemented | none |  |
| POST | `/api/v1/products/` | `products()->create()` | implemented | none |  |
| POST | `/api/v1/products/export-pricing/` | `products()->exportPricing()` | implemented | none | Request schema Product is ambiguous for this response. |
| POST | `/api/v1/products/export/` | `products()->export()` | implemented | none | Request schema Product is ambiguous for this response. |
| GET | `/api/v1/products/{product_uuid}/pricings/` | `productPricings()->list()` | implemented | dedicated |  |
| POST | `/api/v1/products/{product_uuid}/pricings/` | `productPricings()->create()` | implemented | none |  |
| DELETE | `/api/v1/products/{product_uuid}/pricings/{uuid}/` | `productPricings()->delete()` | implemented | none |  |
| GET | `/api/v1/products/{product_uuid}/pricings/{uuid}/` | `productPricings()->get()` | implemented | none |  |
| PATCH | `/api/v1/products/{product_uuid}/pricings/{uuid}/` | `productPricings()->patch()` | implemented | none |  |
| PUT | `/api/v1/products/{product_uuid}/pricings/{uuid}/` | `productPricings()->put()` | implemented | none |  |
| DELETE | `/api/v1/products/{uuid}/` | `products()->delete()` | implemented | none |  |
| GET | `/api/v1/products/{uuid}/` | `products()->get()` | implemented | none |  |
| PATCH | `/api/v1/products/{uuid}/` | `products()->patch()` | implemented | none |  |
| PUT | `/api/v1/products/{uuid}/` | `products()->put()` | implemented | none |  |
| GET | `/api/v1/receipts/` | `receipts()->list()` | implemented | none |  |
| POST | `/api/v1/receipts/bulk-action/` | `receipts()->bulkAction()` | implemented | none |  |
| GET | `/api/v1/receipts/{uuid}/` | `receipts()->get()` | implemented | none |  |
| GET | `/api/v1/recipients/` | `recipients()->list()` | implemented | none |  |
| POST | `/api/v1/recipients/` | `recipients()->create()` | implemented | none |  |
| POST | `/api/v1/recipients/export/` | `recipients()->export()` | implemented | none |  |
| DELETE | `/api/v1/recipients/{uuid}/` | `recipients()->delete()` | implemented | none |  |
| GET | `/api/v1/recipients/{uuid}/` | `recipients()->get()` | implemented | none |  |
| PATCH | `/api/v1/recipients/{uuid}/` | `recipients()->patch()` | implemented | none |  |
| PUT | `/api/v1/recipients/{uuid}/` | `recipients()->put()` | implemented | none |  |
| GET | `/api/v1/reports/` | `reports()->list()` | implemented | none |  |
| POST | `/api/v1/reports/` | `reports()->create()` | implemented | none |  |
| DELETE | `/api/v1/reports/{uuid}/` | `reports()->delete()` | implemented | none |  |
| GET | `/api/v1/reports/{uuid}/` | `reports()->get()` | implemented | none |  |
| PATCH | `/api/v1/reports/{uuid}/` | `reports()->patch()` | implemented | none |  |
| PUT | `/api/v1/reports/{uuid}/` | `reports()->put()` | implemented | none |  |
| POST | `/api/v1/revenue-groups-form/` | `revenueGroupForms()->create()` | implemented | none |  |
| DELETE | `/api/v1/revenue-groups-form/{id}/` | `revenueGroupForms()->delete()` | implemented | none |  |
| GET | `/api/v1/revenue-groups-form/{id}/` | `revenueGroupForms()->get()` | implemented | none |  |
| PATCH | `/api/v1/revenue-groups-form/{id}/` | `revenueGroupForms()->patch()` | implemented | none |  |
| POST | `/api/v1/revenue-groups-form/{revenue_group_id}/countries/` | `revenueGroupForms()->createCountries()` | implemented | none |  |
| DELETE | `/api/v1/revenue-groups-form/{revenue_group_id}/countries/{id}/` | `revenueGroupForms()->deleteCountries()` | implemented | none |  |
| GET | `/api/v1/revenue-groups-form/{revenue_group_id}/countries/{id}/` | `revenueGroupForms()->getCountries()` | implemented | none |  |
| PATCH | `/api/v1/revenue-groups-form/{revenue_group_id}/countries/{id}/` | `revenueGroupForms()->updateCountries()` | implemented | none |  |
| GET | `/api/v1/revenue-groups-list/` | `revenueGroupIndex()->list()` | implemented | none |  |
| POST | `/api/v1/revenue-groups-list/export/` | `revenueGroupIndex()->export()` | implemented | none |  |
| GET | `/api/v1/revenue-groups-list/{id}/` | `revenueGroupIndex()->get()` | implemented | none |  |
| GET | `/api/v1/revenue-groups-list/{id}/full/` | `revenueGroupIndex()->getFull()` | implemented | none |  |
| POST | `/api/v1/revenue-recognition-algorithms/examples/` | `revenueRecognition()->examples()` | implemented | none | Response schema is not published. |
| GET | `/api/v1/revenue-recognition-algorithms/settings/` | `revenueRecognition()->settings()` | implemented | none | Response schema is not published. |
| GET | `/api/v1/revenuegroup/` | `revenueGroups()->list()` | implemented | none |  |
| POST | `/api/v1/revenuegroup/` | `revenueGroups()->create()` | implemented | none |  |
| DELETE | `/api/v1/revenuegroup/{id}/` | `revenueGroups()->delete()` | implemented | none |  |
| GET | `/api/v1/revenuegroup/{id}/` | `revenueGroups()->get()` | implemented | none |  |
| PATCH | `/api/v1/revenuegroup/{id}/` | `revenueGroups()->patch()` | implemented | none |  |
| PUT | `/api/v1/revenuegroup/{id}/` | `revenueGroups()->put()` | implemented | none |  |
| GET | `/api/v1/search/` | `search()->list()` | implemented | none |  |
| GET | `/api/v1/sent-emails/` | `sentEmails()->list()` | implemented | none |  |
| POST | `/api/v1/sent-emails/bulk-action/` | `sentEmails()->bulkAction()` | implemented | none | Request schema SentEmail is ambiguous for this response. |
| POST | `/api/v1/sent-emails/export/` | `sentEmails()->export()` | implemented | none | Request schema SentEmail is ambiguous for this response. |
| GET | `/api/v1/sent-emails/{id}/` | `sentEmails()->get()` | implemented | none |  |
| POST | `/api/v1/sent-emails/{id}/resend/` | `sentEmails()->resend()` | implemented | none | Response schema is not published. |
| GET | `/api/v1/subscriptions/` | `subscriptions()->list()` | implemented | none |  |
| POST | `/api/v1/subscriptions/` | `subscriptions()->create()` | implemented | none |  |
| GET | `/api/v1/subscriptions/activity-months/` | `subscriptions()->activityMonths()` | implemented | none |  |
| GET | `/api/v1/subscriptions/activity_months/` | `subscriptions()->activityMonthsLegacy()` | deprecated | none |  |
| POST | `/api/v1/subscriptions/bulk-action/` | `subscriptions()->bulkAction()` | implemented | none | Request schema Subscription is ambiguous for this response. |
| POST | `/api/v1/subscriptions/export/` | `subscriptions()->export()` | implemented | none |  |
| GET | `/api/v1/subscriptions/mrr-per-month/` | `subscriptions()->mrrPerMonth()` | implemented | none |  |
| GET | `/api/v1/subscriptions/mrr_per_month/` | `subscriptions()->mrrPerMonthLegacy()` | deprecated | none |  |
| GET | `/api/v1/subscriptions/retention-cohort/` | `subscriptions()->retentionCohort()` | implemented | none |  |
| GET | `/api/v1/subscriptions/retention_cohort/` | `subscriptions()->retentionCohortLegacy()` | deprecated | none |  |
| GET | `/api/v1/subscriptions/total-mrr/` | `subscriptions()->totalMrr()` | implemented | none |  |
| GET | `/api/v1/subscriptions/total_mrr/` | `subscriptions()->totalMrrLegacy()` | deprecated | none |  |
| GET | `/api/v1/subscriptions/{subscription_uuid}/changes/` | `subscriptionChanges()->list()` | implemented | dedicated |  |
| GET | `/api/v1/subscriptions/{subscription_uuid}/changes/{uuid}/` | `subscriptionChanges()->get()` | implemented | none |  |
| POST | `/api/v1/subscriptions/{subscription_uuid}/changes/{uuid}/cancel/` | `subscriptionChanges()->cancel()` | implemented | dedicated |  |
| GET | `/api/v1/subscriptions/{uuid}/` | `subscriptions()->get()` | implemented | none |  |
| PATCH | `/api/v1/subscriptions/{uuid}/` | `subscriptions()->patch(), changeQuantity(), changeTerms()` | implemented | dedicated | Quantity, terms, and schedule are sent on this update. change_type is immediate, at_specified_time, at_renewal, or retroactive. There is no separate create-change endpoint. |
| PUT | `/api/v1/subscriptions/{uuid}/` | `subscriptions()->put()` | implemented | none |  |
| POST | `/api/v1/subscriptions/{uuid}/add-following/` | `subscriptions()->addFollowing()` | implemented | dedicated |  |
| POST | `/api/v1/subscriptions/{uuid}/cancel/` | `subscriptions()->cancel()` | implemented | dedicated |  |
| POST | `/api/v1/subscriptions/{uuid}/preview-following/` | `subscriptions()->previewFollowing()` | implemented | none |  |
| POST | `/api/v1/subscriptions/{uuid}/reactivate/` | `subscriptions()->reactivate()` | implemented | none |  |
| POST | `/api/v1/subscriptions/{uuid}/renew-now/` | `subscriptions()->renewNow()` | implemented | none |  |
| GET | `/api/v1/supplier-invoices/` | `supplierInvoices()->list()` | implemented | none |  |
| POST | `/api/v1/supplier-invoices/bulk-action/` | `supplierInvoices()->bulkAction()` | implemented | none |  |
| GET | `/api/v1/supplier-invoices/{uuid}/` | `supplierInvoices()->get()` | implemented | none |  |
| POST | `/api/v1/suppliers/export/` | `suppliers()->export()` | implemented | none |  |
| GET | `/api/v1/transactions/` | `transactions()->list()` | implemented | none |  |
| POST | `/api/v1/transactions/` | `transactions()->create()` | implemented | none |  |
| POST | `/api/v1/transactions/export/` | `transactions()->export()` | implemented | none |  |
| GET | `/api/v1/transactions/{uuid}/` | `transactions()->get()` | implemented | none |  |
| GET | `/api/v1/transactions/{uuid}/full/` | `transactions()->getFull()` | implemented | none |  |
| GET | `/api/v1/vat-payments/upcoming-vat-payment/` | `vatPayments()->upcomingVatPayment()` | implemented | none | Response schema is not published. |
| GET | `/api/v1/webhook-events-metadata/` | `webhookEventMetadata()->list()` | implemented | none |  |
| GET | `/api/v1/webhook-events-metadata/{uuid}/` | `webhookEventMetadata()->get()` | implemented | none |  |
| GET | `/api/v1/webhooks/` | `webhooks()->list()` | implemented | none |  |
| POST | `/api/v1/webhooks/` | `webhooks()->create()` | implemented | dedicated |  |
| GET | `/api/v1/webhooks/{uuid}/` | `webhooks()->get()` | implemented | none |  |
| PATCH | `/api/v1/webhooks/{uuid}/` | `webhooks()->patch()` | implemented | none |  |
| PUT | `/api/v1/webhooks/{uuid}/` | `webhooks()->put()` | implemented | none |  |

## Unsupported

- `PATCH /api/v1/invoices/{uuid}/`: The operation publishes no request parameters and no response schema. No payload is sent until Fenerum documents one.
- `POST /api/v1/expense-documents/upload/`: ExpenseDocumentUpload requires files, but each item is a read-only URI and the operation does not document a multipart body. Left unsupported so the client does not invent an upload format.

## Incoming webhook callbacks

These paths document payloads Fenerum can POST to your endpoint. `WebhookParser` reads them. The client does not call them, and it does not verify a signature because the callback documentation does not define one.

The callbacks article also names `payment.authentication_required`. This OpenAPI file does not publish a `your-endpoint-for-*` path for it. The parser still accepts that event name.

- `POST /your-endpoint-for-account-created` account.created
- `POST /your-endpoint-for-account-updated` account.updated
- `POST /your-endpoint-for-plan_terms-created` plan_terms.created
- `POST /your-endpoint-for-plan_terms-updated` plan_terms.updated
- `POST /your-endpoint-for-new_invoice` new_invoice
- `POST /your-endpoint-for-paid_invoice` paid_invoice
- `POST /your-endpoint-for-invoice-overdue` invoice.overdue
- `POST /your-endpoint-for-cancel_subscription` cancel_subscription
- `POST /your-endpoint-for-reactivate_subscription` reactivate_subscription
- `POST /your-endpoint-for-renew_subscription_soon` renew_subscription_soon
- `POST /your-endpoint-for-new_activity` new_activity
- `POST /your-endpoint-for-payment-declined` payment.declined
- `POST /your-endpoint-for-card_expires_this_month` card_expires_this_month
- `POST /your-endpoint-for-payment_card-activated` payment_card.activated
- `POST /your-endpoint-for-payment_card-deactivated` payment_card.deactivated
