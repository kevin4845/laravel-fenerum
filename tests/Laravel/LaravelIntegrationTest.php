<?php

declare(strict_types=1);

namespace Kevin4845\LaravelFenerum\Tests\Laravel;

use Illuminate\Foundation\Application;
use Kevin4845\LaravelFenerum\FenerumClient;
use Kevin4845\LaravelFenerum\Laravel\Fenerum;
use Kevin4845\LaravelFenerum\Laravel\FenerumServiceProvider;
use Orchestra\Testbench\TestCase;
use RuntimeException;

final class LaravelIntegrationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FenerumServiceProvider::class];
    }

    protected function getPackageAliases($app): array
    {
        return ['Fenerum' => Fenerum::class];
    }

    public function testDiscoveryIsDeclaredForLaravel(): void
    {
        $composer = json_decode((string) file_get_contents(dirname(__DIR__, 2) . '/composer.json'), true);
        self::assertIsArray($composer);

        self::assertSame(
            [FenerumServiceProvider::class],
            $composer['extra']['laravel']['providers'] ?? null,
        );
        self::assertSame(
            Fenerum::class,
            $composer['extra']['laravel']['aliases']['Fenerum'] ?? null,
        );
    }

    public function testTheProviderBootsBeforeTheClientIsResolved(): void
    {
        $app = $this->application();

        self::assertTrue($app->bound(FenerumClient::class));
        self::assertFalse($app->resolved(FenerumClient::class));

        $config = $app->make('config')->get('fenerum');
        self::assertIsArray($config);
        self::assertArrayHasKey('token', $config);
        self::assertSame('Laravel', $config['client_system']);
        self::assertSame('https://app.fenerum.com', $config['base_url']);
        self::assertFalse($app->resolved(FenerumClient::class));

        $paths = FenerumServiceProvider::pathsToPublish(FenerumServiceProvider::class, 'fenerum-config');
        $source = dirname(__DIR__, 2) . '/config/fenerum.php';
        self::assertArrayHasKey($source, $paths);
        self::assertSame($app->basePath('config/fenerum.php'), $paths[$source]);
        self::assertFileExists($source);
    }

    public function testTheContainerAndFacadeResolveTheSameClient(): void
    {
        $this->application()->make('config')->set([
            'fenerum.token' => 'secret-token',
            'fenerum.client_system' => 'TestApp',
            'fenerum.user' => '',
            'fenerum.base_url' => 'https://fenerum.test',
            'fenerum.timeout' => 3.5,
            'fenerum.max_retries' => 0,
            'fenerum.max_retry_delay' => 60,
        ]);

        $client = $this->application()->make(FenerumClient::class);

        self::assertInstanceOf(FenerumClient::class, $client);
        self::assertNull($client->config()->user);
        self::assertSame('https://fenerum.test', $client->config()->baseUrl);
        self::assertSame(0, $client->config()->maxRetries);
        self::assertSame($client, Fenerum::getFacadeRoot());
    }

    public function testResolvingWithoutATokenFailsBeforeARequest(): void
    {
        $this->expectException(RuntimeException::class);

        $this->application()->make(FenerumClient::class);
    }

    private function application(): Application
    {
        $app = $this->app;
        if (!$app instanceof Application) {
            self::fail('The Laravel application was not booted.');
        }

        return $app;
    }
}
