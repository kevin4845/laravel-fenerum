# kevin4845/laravel-fenerum

[![Packagist version](https://img.shields.io/packagist/v/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)
[![PHP version](https://img.shields.io/packagist/php-v/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)
[![License](https://img.shields.io/packagist/l/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)

PHP SDK for the [Fenerum API](https://www.fenerum.com/en-DK/docs/). Use `FenerumClient` in any PHP application. Laravel integration is optional and is loaded only when a Laravel application discovers it.

Source: [github.com/kevin4845/laravel-fenerum](https://github.com/kevin4845/laravel-fenerum)

## Requirements and installation

- PHP 8.2 or newer
- `ext-json`
- Guzzle 7.9 or newer, installed with this package

Checks in this repository ran on PHP 8.4.14. The Laravel example was exercised on Laravel 13.39.0. PHP 8.2, PHP 8.3, Laravel 11, and Laravel 12 were not executed here.

Packagist currently publishes `dev-main` only, so install that version:

```bash
composer require kevin4845/laravel-fenerum:dev-main
```

After a stable tag is published:

```bash
composer require kevin4845/laravel-fenerum
```

`illuminate/support` is not required for the plain PHP client.

## Plain PHP

Pass the API token itself. The client sends `Authorization: Token <token>` and `X-Client-System`.

```php
use Kevin4845\LaravelFenerum\FenerumClient;

$token = getenv('FENERUM_TOKEN');
if (!is_string($token) || $token === '') {
    throw new RuntimeException('Set FENERUM_TOKEN.');
}

$client = FenerumClient::create(
    token: $token,
    clientSystem: 'MyApp',
);

$account = $client->accounts()->get('CUST001');
```

`get()` takes the account code. `clientSystem` is required. The default API origin is `https://app.fenerum.com`.

## Laravel

Package discovery registers `Kevin4845\LaravelFenerum\Laravel\FenerumServiceProvider` and the `Fenerum` facade. Set `FENERUM_TOKEN`. `FENERUM_CLIENT_SYSTEM` defaults to `Laravel`. The client is resolved when you first use it, not during discovery.

```php
use Kevin4845\LaravelFenerum\Laravel\Fenerum;

$account = Fenerum::accounts()->get('CUST001');
```

The same call works through the `Fenerum` alias, or by injecting `Kevin4845\LaravelFenerum\FenerumClient`.

Optional settings live in `config/fenerum.php`: `FENERUM_USER`, `FENERUM_BASE_URL`, `FENERUM_TIMEOUT`, `FENERUM_MAX_RETRIES`, and `FENERUM_MAX_RETRY_DELAY`. Publish that file with:

```bash
php artisan vendor:publish --tag=fenerum-config
```

## Features

- Resource clients for accounts, subscriptions, invoices, plans, products, exports, webhooks, and the other operations in [docs/api-coverage.md](docs/api-coverage.md).
- Paginated lists return `Kevin4845\LaravelFenerum\Support\Page` (`count`, `next`, `previous`, `results`).
- HTTP 429 on `GET`, `PUT`, and `DELETE` is retried when `Retry-After` is usable, up to 2 extra attempts and 60 seconds of sleep by default. `POST`, `PATCH`, network failures, and other statuses are not retried.
- `webhookParser()->parse($rawBody)` reads documented webhook events. On the facade that is `Fenerum::webhookParser()`.

## Limitations

- `PATCH /api/v1/invoices/{uuid}/` and `POST /api/v1/expense-documents/upload/` are not implemented. Their request contracts are not documented.
- Webhook bodies are parsed. Signature verification is not implemented.
- The coverage report was built from the OpenAPI schema retrieved on 2026-10-28. It is not a live check of every operation.

## Development and license

```bash
composer check
```

That runs Composer validation, PHPCS, PHPStan level 8, and PHPUnit. Tests mock HTTP and do not call Fenerum.

MIT, as declared in `composer.json`.
