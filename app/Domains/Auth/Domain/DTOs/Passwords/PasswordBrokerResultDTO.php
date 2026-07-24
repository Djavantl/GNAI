<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\DTOs\Passwords;

use Illuminate\Support\Facades\Password;

final readonly class PasswordBrokerResultDTO
{
    public function __construct(
        public string $status,
    ) {}

    public function successfulLinkSent(): bool
    {
        return $this->status === Password::RESET_LINK_SENT;
    }
}
