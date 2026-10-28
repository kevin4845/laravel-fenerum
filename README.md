# kevin4845/laravel-fenerum

[![Packagist version](https://img.shields.io/packagist/v/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)
[![PHP version](https://img.shields.io/packagist/php-v/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)
[![License](https://img.shields.io/packagist/l/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)

PHP SDK for the [Fenerum API](https://www.fenerum.com/en-DK/docs/). The client sends HTTP requests and returns the decoded response. It does not calculate prices, prorations, invoice amounts, or renewal dates.

The core client is framework-agnostic. Laravel support is optional and lives in the same package. `illuminate/support` is not a required dependency.

Source: [github.com/kevin4845/laravel-fenerum](https://github.com/kevin4845/laravel-fenerum)

## Requirements

Declared in `composer.json`:

- PHP 8.2 or newer
- `ext-json`
- Guzzle 7.9 or newer (`guzzlehttp/guzzle: ^7.9`), installed with this package

The checks in this repository ran on PHP 8.4.14. PHP 8.2 and PHP 8.3 were not executed here.

## Installation

```bash
composer require kevin4845/laravel-fenerum
```

Laravel is not required to use `FenerumClient`.

## Quick start

Pass the API token without the `Token ` prefix. `clientSystem` is required and is sent as `X-Client-System`. An optional `user` is sent as `X-User`.

```php
use Kevin4845\LaravelFenerum\Exception\ApiException;
use Kevin4845\LaravelFenerum\FenerumClient;

$token = getenv('FENERUM_TOKEN');
if (!is_string($token) || $token === '') {
    throw new RuntimeException('Set the FENERUM_TOKEN environment variable.');
}

$clientSystem = getenv('FENERUM_CLIENT_SYSTEM');
$client = FenerumClient::create(
    token: $token,
    clientSystem: is_string($clientSystem) && $clientSystem !== '' ? $clientSystem : 'MyApp',
    user: getenv('FENERUM_USER') ?: null,
);

try {
    $account = $client->accounts()->get('CUST001');
} catch (ApiException $exception) {
    // The message redacts the token. Do not log responseBody or errors.
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
}
```

`FenerumClient::create()` also accepts `baseUrl` (default `https://app.fenerum.com`), `timeout` (default `10.0`), `maxRetries` (default `2`), and `maxRetryDelaySeconds` (default `60.0`). The base URL must be an origin, with no user info, path, or query. `withUser()` returns a copy that changes `X-User`. A per-request `X-User` header overrides the configured user. `Authorization` and `X-Client-System` cannot be overridden per request.

You can inject any Guzzle `ClientInterface`, or any `Kevin4845\LaravelFenerum\Http\HttpTransport`.

## What you can call

The operation map is [docs/api-coverage.md](docs/api-coverage.md). It was built from the OpenAPI v1 schema retrieved on 2026-10-28. That schema lists 258 outbound operations: 250 implemented, 6 deprecated and still callable, and 2 unsupported. Incoming webhook paths are parsed, not called.

These checks use mocked HTTP. They do not prove that every operation succeeds against a live Fenerum account. 29 operations have a dedicated test. The other 229 rely on the shared HTTP client.

| Area | Access |
| --- | --- |
| Accounts, subscriptions, plans, products | `accounts()`, `subscriptions()`, `subscriptionChanges()`, `plans()`, `planTerms()`, `products()`, `productPricings()` |
| Invoices and payments | `invoices()`, `invoiceLines()`, `draftInvoiceLines()`, `payments()`, `receipts()`, `paymentCards()` |
| Webhooks | `webhooks()`, `webhookParser()`, `webhookEventMetadata()` |
| Exports and reports | `exports()`, `kpis()`, `reports()`, plus `export()` on the resources that publish one |
| Other published resources | `activities()`, `contracts()`, `recipients()`, budgets, ERP, revenue groups, search, and the rest listed in the coverage report |

List endpoints that return `count`, `next`, `previous`, and `results` come back as `Kevin4845\LaravelFenerum\Support\Page`. Some lists are JSON arrays. Endpoints with no published response schema return decoded JSON, or `null` when the body is empty. Pass query parameters as an associative array. `null` values are omitted. Booleans are sent as `true` and `false`.

```php
use Kevin4845\LaravelFenerum\Enum\CollectionMethod;
use Kevin4845\LaravelFenerum\Enum\LookupField;

$page = $client->accounts()->list(['page' => 1, 'search' => 'acme']);
$plan = $client->plans()->get('PLAN001', LookupField::Code);
$client->accounts()->subscribe('CUST001', 'terms-uuid', 3, CollectionMethod::Invoice);
```

`accounts()->subscribe()` creates a subscription and cancels the account's other subscriptions. Fenerum can issue a prorated invoice immediately. Do not combine it with `subscriptions()->create()`.

### Subscription changes

Fenerum applies a change by updating the subscription. There is no endpoint that creates a change resource. `changeQuantity()` and `changeTerms()` call `PATCH /api/v1/subscriptions/{uuid}/`.

```php
use Kevin4845\LaravelFenerum\Enum\CancellationType;
use Kevin4845\LaravelFenerum\Enum\SubscriptionChangeType;

$client->subscriptions()->changeQuantity('sub-uuid', 4, SubscriptionChangeType::AtRenewal);
$client->subscriptions()->changeQuantity(
    'sub-uuid',
    4,
    SubscriptionChangeType::AtSpecifiedTime,
    ['prorate_date' => '2026-11-01T00:00:00Z'],
);
$client->subscriptions()->cancel('sub-uuid', CancellationType::AtRenewal, [
    'cancel_renewal' => 0,
]);

$changes = $client->subscriptionChanges()->list('sub-uuid');
$client->subscriptionChanges()->cancel('sub-uuid', $changes->results[0]['uuid']);
```

`change_type` is `immediate`, `at_specified_time`, `at_renewal`, or `retroactive`. `changeQuantity()` forwards an integer or a finite float. `cancel_renewal`, when sent, must be an integer from 0 through 23. The schema also lists cancellation types `at_specified_time` and `cancel_invalid`. This client does not map those names onto financial outcomes.

### Invoices

```php
$client->invoices()->pay('invoice-uuid', CollectionMethod::PaymentCard);
$client->invoices()->refund('invoice-uuid');
$client->invoices()->acceptLoss('invoice-uuid', '2026-01-02');
$client->invoices()->applyCredit('invoice-uuid', 'target-invoice-uuid', '10.00');
$client->invoices()->changeCollectionMethod('invoice-uuid', CollectionMethod::Invoice);
$client->invoices()->sendToDebtCollection('invoice-uuid', true, 'ops@example.com');
$client->accounts()->generateInvoice('CUST001', CollectionMethod::Invoice, 14);
```

`pay()` is documented as HTTP 202 and can return HTTP 423. Several actions publish no response schema, so an empty body comes back as `null`. `PATCH /api/v1/invoices/{uuid}/` is not implemented.

### Webhooks

```php
$event = $client->webhookParser()->parse($rawBody);

if ($event->type === 'account.updated') {
    $account = $event->asAccount();
}
```

Known events include `account.created`, `account.updated`, `new_invoice`, `paid_invoice`, `cancel_subscription`, `payment.declined`, and the other names in `Kevin4845\LaravelFenerum\Webhook\KnownEvents`. An unknown event name is kept. There is no webhook controller. Parse the raw body in your own route.

The parser does not verify a signature. The callbacks article does not define one.

### Exports and reports

Export calls that return HTTP 202 produce an `ExportJob` with `export_id` and `job_uuid`. Download is a separate call. The schema does not say whether `{id}` is `export_id` or `job_uuid`. This client does not fetch the file URL.

```php
$job = $client->accounts()->export();
$download = $client->exports()->download($job['export_id']);

$spreadsheet = $client->kpis()->export('mrr', [
    'breakdown.0.dimension' => 'country',
]);
$spreadsheet->body; // raw bytes, not decoded JSON
```

Where the published request body is the parent serializer, the method sends `{}` unless you pass a payload. Those parent fields are not documented export filters.

## Laravel

Package discovery registers `Kevin4845\LaravelFenerum\Laravel\FenerumServiceProvider` and the `Fenerum` facade. Discovery merges configuration and binds `FenerumClient` as a lazy singleton. It does not resolve the client, so a missing token does not fail during boot. Resolving the client throws `RuntimeException` when `fenerum.token` or `fenerum.client_system` is empty.

The integration tests ran on Laravel 13.39.0. Laravel 11 and Laravel 12 were not executed.

Publish the config file when you want to override the package defaults:

```bash
php artisan vendor:publish --tag=fenerum-config
```

`config/fenerum.php` reads:

| Variable | Config key | Purpose | Default |
| --- | --- | --- | --- |
| `FENERUM_TOKEN` | `token` | API token, without the `Token ` prefix | none |
| `FENERUM_CLIENT_SYSTEM` | `client_system` | `X-Client-System` | `Laravel` |
| `FENERUM_USER` | `user` | Optional `X-User`. An empty value sends no header | none |
| `FENERUM_BASE_URL` | `base_url` | API origin | `https://app.fenerum.com` |
| `FENERUM_TIMEOUT` | `timeout` | Timeout in seconds | `10` |
| `FENERUM_MAX_RETRIES` | `max_retries` | Extra attempts after a retryable HTTP 429 | `2` |
| `FENERUM_MAX_RETRY_DELAY` | `max_retry_delay` | Maximum total time slept while retrying, in seconds | `60` |

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

## Limitations

- `PATCH /api/v1/invoices/{uuid}/` is not implemented. Its request schema is not documented.
- `POST /api/v1/expense-documents/upload/` is not implemented. The upload contract is not clear: `files` is required, each item is a read-only URI, and the operation does not document multipart.
- Webhook bodies are parsed. Webhook signature verification is not implemented.
- Export and bulk-action request bodies, the export download identifier, activity-delete filters, and other unpublished schemas may need confirmation with Fenerum. See [docs/api-coverage.md](docs/api-coverage.md).
- Deprecated paths such as `generate_invoice` and `card_registration_link` remain callable. Prefer the current paths.
- The OpenAPI comparison used the schema retrieved on 2026-10-28. It is not a live-account verification of every operation.

## Retries and credentials

HTTP 429 is retried only for `GET`, `PUT`, and `DELETE`, and only when `Retry-After` is usable.

- A non-negative integer, such as `42`, is a delay in seconds. It is recognized before date parsing.
- An HTTP-date (IMF-fixdate, RFC 850, or asctime) delays until that time. A date in the past retries immediately. That follows HTTP. Fenerum's rate-limit article only shows an integer number of seconds.

The default is 2 additional attempts and at most 60 seconds of total sleep. A delay that would exceed the remaining budget is not slept, and the 429 is thrown. `maxRetries` of `0` disables retries. A missing or unrecognized `Retry-After` also disables the retry.

`POST` and `PATCH` are not retried. Fenerum does not document an idempotency key. Network failures and responses other than HTTP 429, including HTTP 400, HTTP 403, and HTTP 500, are not retried.

The token is omitted from exception messages and from debug output. String fields whose names contain `password`, `token`, `secret`, or `authorization` are replaced with `[redacted]` in stored error bodies when the value is at least four characters. `ApiException::$responseBody` and `$errors` can still contain other data the API returned, including customer fields. Do not log those properties. `NetworkException` does not chain the Guzzle exception, so its trace cannot include the `Authorization` header.

| Exception | When |
| --- | --- |
| `ApiException` | A non-2xx response. `statusCode`, `retryAfter`, `retries`, and `retryDelaySeconds` are safe to inspect. |
| `NetworkException` | The connection failed. |
| `InvalidResponseException` | A success body was not the JSON shape that method expects. |
| `InvalidWebhookPayload` | The callback body is not a JSON object with an event name and a data object. |
| `InvalidArgumentException` | The token, base URL, timeout, retry limits, or a path is invalid. |

## Development

Tests use a mocked HTTP client. They do not call Fenerum and do not need credentials.

```bash
composer check
```

That runs `composer validate`, PHPCS (PSR-12), PHPStan level 8, and PHPUnit.

This repository has no GitHub Actions workflow. Issues and pull requests go to [github.com/kevin4845/laravel-fenerum](https://github.com/kevin4845/laravel-fenerum). Run `composer check` before opening a pull request, and add an operation only when Fenerum has published it.

## License

MIT, as declared in `composer.json`.
