<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Laravel;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Kevin4845\LaravelFenerum\FenerumClient;
use RuntimeException;

final class FenerumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom($this->configFile(), 'fenerum');

        $this->app->singleton(FenerumClient::class, function (Application $app): FenerumClient {
            $repository = $app->make('config');
            if (!$repository instanceof Repository) {
                throw new RuntimeException('Fenerum configuration is missing.');
            }

            $config = $repository->get('fenerum');
            if (!is_array($config)) {
                throw new RuntimeException('Fenerum configuration is missing.');
            }

            $token = $config['token'] ?? null;
            $clientSystem = $config['client_system'] ?? null;
            if (!is_string($token) || $token === '' || !is_string($clientSystem) || $clientSystem === '') {
                throw new RuntimeException(
                    'Set fenerum.token and fenerum.client_system before resolving the Fenerum client.',
                );
            }

            $user = $config['user'] ?? null;
            if ($user === '') {
                $user = null;
            }

            if ($user !== null && !is_string($user)) {
                throw new RuntimeException('fenerum.user must be a string or empty.');
            }

            $baseUrl = $config['base_url'] ?? 'https://app.fenerum.com';
            $timeout = $config['timeout'] ?? 10;
            $maxRetries = $config['max_retries'] ?? 2;
            $maxRetryDelay = $config['max_retry_delay'] ?? 60;
            if (!is_string($baseUrl) || !is_numeric($timeout) || !is_numeric($maxRetries) || !is_numeric($maxRetryDelay)) {
                throw new RuntimeException('Fenerum configuration has an invalid value.');
            }

            return FenerumClient::create(
                $token,
                $clientSystem,
                $user,
                $baseUrl,
                (float) $timeout,
                null,
                (int) $maxRetries,
                (float) $maxRetryDelay,
            );
        });
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            $this->configFile() => $this->app->basePath('config/fenerum.php'),
        ], 'fenerum-config');
    }

    private function configFile(): string
    {
        return dirname(__DIR__, 2) . '/config/fenerum.php';
    }
}
