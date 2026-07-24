<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\DTOs\Sessions;

final readonly class LoginCredentialsDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public function toAuthCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
