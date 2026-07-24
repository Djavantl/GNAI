<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Domain\DTOs\Sessions;

use App\Domains\Auth\Domain\DTOs\Sessions\LoginCredentialsDTO;
use Tests\TestCase;

final class LoginCredentialsDTOTest extends TestCase
{
    public function test_it_exposes_laravel_auth_credentials(): void
    {
        $dto = new LoginCredentialsDTO(
            email: 'user@example.com',
            password: 'secret',
        );

        self::assertSame([
            'email' => 'user@example.com',
            'password' => 'secret',
        ], $dto->toAuthCredentials());
    }
}
