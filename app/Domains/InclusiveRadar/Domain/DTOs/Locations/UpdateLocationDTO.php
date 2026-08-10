<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Locations;

final readonly class UpdateLocationDTO
{
    public function __construct(
        public int $institutionId,
        public string $name,
        public float $latitude,
        public float $longitude,
        public ?string $type = null,
        public ?string $description = null,
        public ?string $googlePlaceId = null,
        public bool $isActive = true,
    ) {}
}
