<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Positions;

final readonly class UpdatePositionDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
