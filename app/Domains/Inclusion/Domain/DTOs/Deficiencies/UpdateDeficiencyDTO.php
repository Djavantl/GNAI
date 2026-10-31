<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Deficiencies;

final readonly class UpdateDeficiencyDTO
{
    public function __construct(
        public string $name,
        public ?string $cidCode = null,
        public ?string $description = null,
    ) {}
}
