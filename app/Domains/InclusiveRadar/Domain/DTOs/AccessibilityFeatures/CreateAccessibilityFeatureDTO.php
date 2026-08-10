<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\AccessibilityFeatures;

final readonly class CreateAccessibilityFeatureDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $isActive = true,
    ) {}
}
