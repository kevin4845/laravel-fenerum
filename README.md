# kevin4845/laravel-fenerum

[![Packagist version](https://img.shields.io/packagist/v/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)
[![PHP version](https://img.shields.io/packagist/php-v/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)
[![License](https://img.shields.io/packagist/l/kevin4845/laravel-fenerum)](https://packagist.org/packages/kevin4845/laravel-fenerum)

A general-purpose PHP SDK for the [Fenerum API](https://www.fenerum.com/en-DK/docs/). Use `FenerumClient` in any PHP application. Laravel integration is optional.

Source: [github.com/kevin4845/laravel-fenerum](https://github.com/kevin4845/laravel-fenerum)

## Requirements and installation

- PHP 8.2 or newer
- `ext-json`
- Guzzle 7.9 or newer (installed with this package)

The test suite has been run on PHP 8.4.14. The Laravel integration has been tested on Laravel 13.39.0. PHP 8.2, PHP 8.3, Laravel 11, and Laravel 12 have not been tested in this environment.

Install the latest stable release:

```bash
composer require kevin4845/laravel-fenerum:^0.9
```

For the latest development branch instead:

```bash
composer require kevin4845/laravel-fenerum:dev-main
```

`illuminate/support` is not required for the plain PHP client.

## Plain PHP

Pass your Fenerum API token. The client sends `Authorization: Token <token>` and `X-Client-System`.

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

Package discovery registers `Kevin4845\LaravelFenerum\Laravel\FenerumServiceProvider` and the `Fenerum` facade. Set `FENERUM_TOKEN`; `FENERUM_CLIENT_SYSTEM` defaults to `Laravel`. The client is resolved on first use, not during package discovery.

```php
use Kevin4845\LaravelFenerum\Laravel\Fenerum;

$account = Fenerum::accounts()->get('CUST001');
```

The same call works through the `Fenerum` alias. You can also inject `Kevin4845\LaravelFenerum\FenerumClient`.

Optional settings are available in `config/fenerum.php`: `FENERUM_USER`, `FENERUM_BASE_URL`, `FENERUM_TIMEOUT`, `FENERUM_MAX_RETRIES`, and `FENERUM_MAX_RETRY_DELAY`. Publish the configuration with:

```bash
php artisan vendor:publish --tag=fenerum-config
```

## Features

- Resource clients for accounts, subscriptions, invoices, plans, products, exports, webhooks, and other operations documented in [docs/api-coverage.md](docs/api-coverage.md).
- Paginated lists return `Kevin4845\LaravelFenerum\Support\Page` with `count`, `next`, `previous`, and `results`.
- HTTP 429 responses for `GET`, `PUT`, and `DELETE` are retried when `Retry-After` is usable, with up to 2 additional attempts and 60 seconds of total sleep by default. `POST`, `PATCH`, network failures, and other status codes are not retried.
- Parse webhook events with `webhookParser()->parse($rawBody)`, or `Fenerum::webhookParser()` when using Laravel.

## Limitations

- `PATCH /api/v1/invoices/{uuid}/` and `POST /api/v1/expense-documents/upload/` are not implemented because their request contracts are undocumented or unclear.
- Webhook bodies can be parsed, but signature verification is not implemented.
- The API coverage report is based on the OpenAPI schema retrieved on 2026-10-28; it does not mean every operation has been verified against a live Fenerum account.

## Development and license

Run the checks with:

```bash
composer check
```

This runs Composer validation, PHPCS, PHPStan level 8, and PHPUnit. Tests mock HTTP requests and do not call Fenerum.

The package declares the MIT license in `composer.json`.
