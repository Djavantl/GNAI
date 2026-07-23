<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->prepareSafeTestingEnvironment();

        parent::setUp();

        $this->assertSafeTestingDatabase();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function prepareSafeTestingEnvironment(): void
    {
        $this->forceEnvironmentValue('APP_ENV', 'testing');
        $this->forceEnvironmentValue('DB_CONNECTION', 'sqlite');
        $this->forceEnvironmentValue('DB_DATABASE', ':memory:');
        $this->forceEnvironmentValue('CACHE_STORE', 'array');
        $this->forceEnvironmentValue('SESSION_DRIVER', 'array');
        $this->forceEnvironmentValue('QUEUE_CONNECTION', 'sync');
    }

    private function assertSafeTestingDatabase(): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        if (! app()->environment('testing')) {
            throw new RuntimeException(
                "Testes bloqueados: aplicação iniciou fora do ambiente testing. Ambiente atual: " . app()->environment() . '.'
            );
        }

        if ($connection === 'mysql' && $database === 'gnai_db') {
            throw new RuntimeException(
                'Testes bloqueados: conexão de teste aponta para o banco principal gnai_db.'
            );
        }
    }

    private function rawEnvironmentValue(string $key): ?string
    {
        $value = $_SERVER[$key] ?? $_ENV[$key] ?? getenv($key);

        return $value === false ? null : $value;
    }

    private function forceEnvironmentValue(string $key, string $value): void
    {
        $_SERVER[$key] = $value;
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}
