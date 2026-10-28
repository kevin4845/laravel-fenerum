<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Laravel;

use Illuminate\Support\Facades\Facade;
use Kevin4845\LaravelFenerum\FenerumClient;

/**
 * @method static \Kevin4845\LaravelFenerum\FenerumConfig config()
 * @method static \Kevin4845\LaravelFenerum\Resource\Accounts accounts()
 * @method static \Kevin4845\LaravelFenerum\Resource\Subscriptions subscriptions()
 * @method static \Kevin4845\LaravelFenerum\Resource\Invoices invoices()
 * @method static \Kevin4845\LaravelFenerum\Webhook\WebhookParser webhookParser()
 */
final class Fenerum extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FenerumClient::class;
    }
}
