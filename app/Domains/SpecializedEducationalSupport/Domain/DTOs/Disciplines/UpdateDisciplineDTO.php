<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Disciplines;

final readonly class UpdateDisciplineDTO
{
    public function __construct(
        public string $name,
        public ?string $description,
        public bool $isActive,
    ) {}
}
