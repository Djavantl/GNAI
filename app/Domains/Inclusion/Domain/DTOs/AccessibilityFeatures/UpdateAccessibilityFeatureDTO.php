<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\AccessibilityFeatures;

final readonly class UpdateAccessibilityFeatureDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public bool $isActive = true,
    ) {}
}
