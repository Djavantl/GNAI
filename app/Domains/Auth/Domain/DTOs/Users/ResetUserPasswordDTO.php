<?php

declare(strict_types=1);

namespace App\Domains\Auth\Domain\DTOs\Users;

final readonly class ResetUserPasswordDTO
{
    public function __construct(
        public string $password,
        public string $rememberToken,
    ) {}
}
