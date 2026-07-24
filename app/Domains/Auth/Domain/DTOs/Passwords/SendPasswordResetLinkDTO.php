<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\DTOs\Passwords;

final readonly class SendPasswordResetLinkDTO
{
    public function __construct(
        public string $email,
    ) {}

    /**
     * @return array{email: string}
     */
    public function toBrokerCredentials(): array
    {
        return [
            'email' => $this->email,
        ];
    }
}
