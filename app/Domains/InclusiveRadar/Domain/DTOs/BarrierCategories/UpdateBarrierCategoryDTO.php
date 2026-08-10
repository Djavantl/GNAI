<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\BarrierCategories;

final readonly class UpdateBarrierCategoryDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $blocksMap = true,
        public bool $isActive = true,
    ) {}
}
