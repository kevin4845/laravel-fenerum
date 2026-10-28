# kevin4845/laravel-fenerum

PHP client for the [Fenerum API](https://www.fenerum.com/en-DK/docs/). The package sends HTTP requests and parses responses. It does not calculate prices, prorations, invoice amounts, or renewal dates. Fenerum remains responsible for billing.

The core client works in any PHP application. Laravel integration is included and optional: the Composer package does not require `illuminate/support`.

The operation map is [docs/api-coverage.md](docs/api-coverage.md). It was built from the OpenAPI v1 schema retrieved on 2026-10-28. Of 258 outbound operations, 256 are callable. Six of those call paths OpenAPI marks as deprecated. Two operations are intentionally unsupported. Incoming webhook callback paths are parsed, not called.

## Requirements

- PHP 8.2 or newer is declared in `composer.json`
- `ext-json`
- Guzzle 7.9 or newer, installed with this package

`illuminate/support` is not required. The service provider and facade are loaded only when a Laravel application discovers them. The checks for this package ran on PHP 8.4.14 with Laravel 13.39.0. PHP 8.2, PHP 8.3, Laravel 11, and Laravel 12 were not executed here.

## Installation

The package has not been published. Point Composer at this checkout:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-fenerum"
        }
    ],
    "require": {
        "kevin4845/laravel-fenerum": "@dev"
    }
}
```

Then run `composer update kevin4845/laravel-fenerum`.

### Laravel

Laravel discovers `Kevin4845\LaravelFenerum\Laravel\FenerumServiceProvider` and the `Fenerum` facade alias from `composer.json`. Publish the config file:

```bash
php artisan vendor:publish --tag=fenerum-config
```

`config/fenerum.php` reads these environment variables:

| Variable | Purpose | Default |
| --- | --- | --- |
| `FENERUM_TOKEN` | API token, without the `Token ` prefix | none |
| `FENERUM_CLIENT_SYSTEM` | `X-Client-System` | `Laravel` |
| `FENERUM_USER` | Optional `X-User`. An empty value sends no header | none |
| `FENERUM_BASE_URL` | API origin | `https://app.fenerum.com` |
| `FENERUM_TIMEOUT` | Timeout in seconds | `10` |
| `FENERUM_MAX_RETRIES` | Extra attempts after a retryable HTTP 429 | `2` |
| `FENERUM_MAX_RETRY_DELAY` | Maximum total time slept while retrying, in seconds | `60` |

The container binds `Kevin4845\LaravelFenerum\FenerumClient` as a lazy singleton. Package discovery does not need a token. Resolving the client throws if `token` or `client_system` is empty.

```php
use Kevin4845\LaravelFenerum\FenerumClient;
use Kevin4845\LaravelFenerum\Laravel\Fenerum;

public function __construct(private FenerumClient $fenerum)
{
}

public function index()
{
    return Fenerum::accounts()->get('CUST001');
}
```

There is no webhook controller. Parse callbacks in your own route with `webhookParser()`.

## PHP-only usage

```php
use Kevin4845\LaravelFenerum\FenerumClient;

$client = FenerumClient::create(
    token: $token,
    clientSystem: 'MyApp',
    user: 'jane', // optional X-User, or null
    baseUrl: 'https://app.fenerum.com',
    timeout: 10.0,
    maxRetries: 2,
    maxRetryDelaySeconds: 60.0,
);
```

Pass the token itself. The client adds the `Token ` prefix. `withUser()` returns a copy that overrides `X-User` for the accounts you act on behalf of. A per-request `X-User` header overrides the configured user. `Authorization` and `X-Client-System` cannot be overridden per request.

The base URL must be an origin such as `https://app.fenerum.com`, with no user info, path, or query. Paths passed to the client start with `/api/` and end with `/`.

You can inject any Guzzle `ClientInterface`, or any implementation of `Kevin4845\LaravelFenerum\Http\HttpTransport`.

## Authentication

Every request sends:

- `Authorization: Token <token>`
- `X-Client-System: <client system>`
- `Accept: application/json`
- `X-User`, when a user is configured or passed for that request

The token is omitted from debug output and from exception messages. String body fields whose names contain `password`, `token`, `secret`, or `authorization` are replaced with `[redacted]` in stored error bodies when they are at least four characters.

`ApiException::$responseBody` and `$errors` can still contain other data the API echoed, including customer fields. Do not log those properties by default.

## Resources

List endpoints that return `count`, `next`, `previous`, and `results` come back as `Kevin4845\LaravelFenerum\Support\Page`. Some lists are JSON arrays rather than pages. Endpoints whose response schema is unpublished return the decoded JSON array, or `null` when the body is empty. Pass query parameters through as an associative array. `null` values are omitted. Booleans are sent as `true` and `false`.

```php
use Kevin4845\LaravelFenerum\Enum\CollectionMethod;

$page = $client->accounts()->list(['page' => 1, 'search' => 'acme']);

$account = $client->accounts()->create([
    'code' => 'CUST001',
    'company_name' => 'Acme ApS',
    'legal_address' => 'Vestergade 1',
    'legal_zipcode' => '1456',
    'legal_city' => 'København K',
    'legal_country' => 'DK',
]);

$client->accounts()->subscribe('CUST001', 'terms-uuid', 3, CollectionMethod::Invoice);
$client->recipients()->create([
    'account' => $account['uuid'],
    'name' => 'Finance',
    'email' => 'billing@example.com',
    'receive_invoice' => true,
    'receive_payment_confirmation' => true,
]);
$client->contracts()->create('CUST001', [
    'plan_terms' => 'terms-uuid',
    'start_date' => '2026-11-01',
]);
$client->paymentCards()->initiateSetup('stripe');
$client->draftInvoiceLines()->list();
```

`accounts()->subscribe()` creates a subscription and cancels the account's other subscriptions. Fenerum can issue a prorated invoice immediately. Do not combine it with `subscriptions()->create()`, which creates one subscription and leaves the others in place.

Other resources follow the same shape: `plans()`, `planTerms()`, `products()`, `productPricings()`, `subscriptionChanges()`, `activities()`, `invoices()`, `webhooks()`, plus the clients listed in the coverage report (budgets, ERP, revenue groups, direct debit, search, and the rest).

`plans()->get($identifier, LookupField::Code)` sends the documented `Lookup-Field` header. `accounts()->list()` and `accounts()->get()` accept an optional comma-separated `Fields` projection.

## Subscription changes and cancellation

Fenerum applies subscription changes by updating the subscription. There is no endpoint that creates a change resource. `PATCH /api/v1/subscriptions/{uuid}/` takes `UpdateSubscription`. `change_type` is `immediate`, `at_specified_time`, `at_renewal`, or `retroactive`. The boolean `prorate` field is deprecated in the schema. `prorate_date` is a date-time and is documented for `at_specified_time` and `retroactive`.

```php
use Kevin4845\LaravelFenerum\Enum\CancellationType;
use Kevin4845\LaravelFenerum\Enum\SubscriptionChangeType;

$client->subscriptions()->changeQuantity('sub-uuid', 4, SubscriptionChangeType::Immediate);
$client->subscriptions()->changeQuantity('sub-uuid', 4, SubscriptionChangeType::AtRenewal);
$client->subscriptions()->changeQuantity(
    'sub-uuid',
    4,
    SubscriptionChangeType::AtSpecifiedTime,
    ['prorate_date' => '2026-11-01T00:00:00Z'],
);
$client->subscriptions()->changeTerms('sub-uuid', 'terms-uuid', SubscriptionChangeType::AtRenewal);

$client->subscriptions()->cancel('sub-uuid', CancellationType::AtRenewal, [
    'cancel_renewal' => 0,
]);
$client->subscriptions()->reactivate('sub-uuid');
$client->subscriptions()->renewNow('sub-uuid');
$client->subscriptions()->addFollowing('sub-uuid', ['terms' => 'terms-uuid', 'quantity' => 1]);
$client->subscriptions()->previewFollowing('sub-uuid', ['terms' => 'terms-uuid']);
```

`changeQuantity()` forwards an integer or a finite float. The write schema types quantity as an integer; webhook examples also show fractional quantities, so a float is not coerced. `cancel_renewal` must be an integer from 0 through 23 when you send it.

The subscription article names cancellation types `full_refund`, `at_renewal`, and `today`. The schema also lists `at_specified_time` and `cancel_invalid`. This client does not map those names onto financial outcomes. A subscription cannot be cancelled when its status is `expired` or `not_renewing`, or while an active contract covers the terms. Reactivation applies to `not_renewing` and `not_starting`.

Scheduled changes are listed and cancelled through `subscriptionChanges()`, not by posting a new change:

```php
$changes = $client->subscriptionChanges()->list('sub-uuid');
$client->subscriptionChanges()->cancel('sub-uuid', $changes->results[0]['uuid']);
```

`change_renewal` is documented as a string whose enum values are the integers 0 through 11. Pass the integer through. The client does not coerce it.

## Invoice operations

```php
$client->invoices()->pay('invoice-uuid', CollectionMethod::PaymentCard);          // HTTP 202; HTTP 423 is an API error
$client->invoices()->refund('invoice-uuid');                                      // returns the credit note
$client->invoices()->markAsDue('invoice-uuid');                                   // chargeback invoices
$client->invoices()->acceptLoss('invoice-uuid', '2026-01-02');                    // required registration_date
$client->invoices()->applyCreditNote('invoice-uuid');                             // no request body
$client->invoices()->applyCredit('invoice-uuid', 'target-invoice-uuid', '10.00'); // amount is optional
$client->invoices()->changeCollectionMethod('invoice-uuid', CollectionMethod::Invoice);
$client->invoices()->resend('invoice-uuid');                                      // HTTP 202, asynchronous
$client->invoices()->sendToDebtCollection('invoice-uuid', true, 'ops@example.com');
```

`pay()` is documented as HTTP 202 and can return HTTP 423. `refund()` has no writable request fields. `markAsDue()`, `resend()`, and `applyCreditNote()` publish no response schema, so an empty body comes back as `null`. `applyCredit()` requires `target_invoice` and takes an optional decimal `amount` string. `sendToDebtCollection()` requires `accept_terms` and takes an optional email. The invoice must be in the status the schema describes (`chargeback` for loss, credit note, and collection-method changes; nonpayment with a positive balance for debt collection). The organization must have completed Likvido onboarding before debt collection.

`PATCH /api/v1/invoices/{uuid}/` is not implemented. The operation has no request parameters and no response schema.

`accounts()->generateInvoice('CUST001', CollectionMethod::Invoice, 14)` calls the current `/generate-invoice/` path and returns the invoice array Fenerum created. The underscore path is available as `generateInvoiceLegacy()` and is deprecated.

## Exports and reports

Export endpoints that return HTTP 202 produce an `ExportJob` with `export_id` and `job_uuid`. Download is a separate call. The download schema does not say whether `{id}` is `export_id` or `job_uuid`. The response is `ExportDownload`, whose `url` points at the file. This client does not download that URL.

```php
$job = $client->accounts()->export();
$download = $client->exports()->download($job['export_id']);
```

The same job shape is used by `invoices()->export()`, `invoices()->exportVatReport($query)`, `subscriptions()->export()`, `recipients()->export()`, and the other export methods in the coverage report. Where the published body is the parent account, subscription, or similar serializer, the method sends `{}` unless you pass a payload. Those schemas do not document export-specific fields. See the coverage notes before sending one.

`subscriptions()->mrrPerMonth($year, $month)` returns an array, not a page. `subscriptions()->totalMrr($query)` returns one object. `kpis()->list()` and `kpis()->query()` return KPI JSON. The spreadsheet export is not JSON:

```php
$spreadsheet = $client->kpis()->export('mrr', [
    'breakdown.0.dimension' => 'country',
]);

$spreadsheet->statusCode; // 200
$spreadsheet->body;       // raw bytes
$spreadsheet->contentType;
```

The schema describes that response as an XLS file and does not publish a content type. The `Accept` header stays `application/json`, which is the API-wide default.

`activities()->deleteMatching($query)` calls `DELETE /api/v1/activities/delete/`. The description says filters select the manual activities to delete, but the operation lists no query parameters. Names you pass are unverified.

## Retry behavior

HTTP 429 is retried only for `GET`, `PUT`, and `DELETE`, and only when `Retry-After` is usable.

- Delta-seconds: a non-negative integer, such as `42`. This is checked before date parsing, so a numeric value is never treated as a date.
- HTTP-date: IMF-fixdate (`Sun, 06 Nov 1994 08:49:37 GMT`), RFC 850 (`Sunday, 06-Nov-94 08:49:37 GMT`), or asctime (`Sun Nov  6 08:49:37 1994`). The delay is `max(0, header time - now)`. A date in the past retries immediately. This follows the HTTP `Retry-After` rules; Fenerum's rate-limit article only shows an integer number of seconds.

The default allows 2 additional attempts and at most 60 seconds of total sleep. A delay that would exceed the remaining budget is not slept. The client then throws the 429. `maxRetries` of `0` disables retries. `Retry-After` that is missing or not one of those two forms also disables the retry.

`POST` and `PATCH` are not retried. Fenerum does not document an idempotency key, and a retried create could be applied twice. `PUT` and `DELETE` are retried because HTTP defines those methods as idempotent. HTTP 400, HTTP 403, other client errors, and network failures are not retried.

The thrown `ApiException` keeps the final status, the `Retry-After` value, `retries` (how many extra attempts ran), and `retryDelaySeconds` (how long was slept). Tests cover this with a fake sleeper and a fixed clock, so they do not wait.

## Webhook parsing

```php
$event = $client->webhookParser()->parse($rawBody);

if ($event->type === 'account.updated') {
    $account = $event->asAccount();
}
```

Known events: `account.created`, `account.updated`, `plan_terms.created`, `plan_terms.updated`, `new_invoice`, `paid_invoice`, `invoice.overdue`, `cancel_subscription`, `reactivate_subscription`, `renew_subscription_soon`, `new_activity`, `payment.authentication_required`, `payment.declined`, `card_expires_this_month`, `payment_card.activated`, `payment_card.deactivated`.

An unknown `event` string is kept. Calling a typed reader on the wrong event throws `InvalidWebhookPayload`. Missing optional fields become `null`. A field that the documented example requires still has to be present and of the expected type.

The five payment events are not enabled until the webhook's `events` list opts in. The webhook resource has basic-auth fields, but the callbacks article does not define a signature or the header format. This client does not verify authenticity.

## Exceptions

| Exception | When |
| --- | --- |
| `ApiException` | The API returned a non-2xx status. `statusCode`, `errors`, `responseBody`, `retryAfter`, `retries`, and `retryDelaySeconds` are available. |
| `NetworkException` | The connection failed. The Guzzle exception is not chained, so its trace cannot leak the `Authorization` header. |
| `InvalidResponseException` | A success body was not the JSON shape that method expects. |
| `InvalidWebhookPayload` | The callback body is not a JSON object with an event name and a data object. |
| `InvalidArgumentException` | The token, base URL, timeout, retry limits, or a path is invalid. |

Validation messages include the API `detail` when it is a string, and the names of other error fields. They do not include the full JSON object. The decoded object remains on `errors`.

## Testing

Tests use Guzzle's mock handler. They do not call Fenerum and do not need credentials.

```bash
composer check
```

That runs `composer validate`, PHPCS (PSR-12), PHPStan level 8, and PHPUnit.

## Known limitations

- `PATCH /api/v1/invoices/{uuid}/` is unsupported. The schema publishes no body and no response.
- `POST /api/v1/expense-documents/upload/` is unsupported. `files` is required, each item is a read-only URI, and the operation does not document multipart upload.
- Several export and bulk actions attach the parent serializer (Account, Subscription, and similar) even though the response is `ExportJob` or `BulkActionJob`. The client sends `{}` unless you pass a payload. Do not treat those parent fields as a documented export filter.
- `GET /api/v1/exports/{id}/download/` does not say whether `{id}` is `export_id` or `job_uuid`.
- `DELETE /api/v1/activities/delete/` describes filters but publishes none.
- `GET /api/v1/kpis/`, revenue-recognition settings and examples, and the upcoming VAT payment publish no response schema. The client returns decoded JSON and does not assume a page.
- Archive, restore, and plan or plan-term activate and deactivate document no request fields and return an empty success body. The client sends no JSON body, even where OpenAPI attaches a parent serializer.
- Deprecated underscore paths such as `generate_invoice`, `card_registration_link`, `activity_months`, `mrr_per_month`, `retention_cohort`, and `total_mrr` are callable and marked `@deprecated`. Prefer the current paths.
- Most operations do not have a dedicated test. Authentication, pagination, errors, retries, subscription changes, invoice actions, export download, and the KPI spreadsheet do. The coverage report marks the rest `none`.
- Webhook basic auth is not verified. The callbacks article does not specify the header.
