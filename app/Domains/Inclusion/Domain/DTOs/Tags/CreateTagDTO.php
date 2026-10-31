<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Tags;

final readonly class CreateTagDTO
{
    public function __construct(
        public string $name,
        public bool $isActive = true,
    ) {}
}
