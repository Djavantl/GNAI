<?php

declare(strict_types=1);

namespace App\Domains\Settings\Domain\DTOs;

final readonly class TestMailResultDTO
{
    public function __construct(
        public bool $usedStoredSettings,
        public bool $loggedOnly,
    ) {}
}
