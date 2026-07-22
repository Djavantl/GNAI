<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Institutions;

final readonly class CreateInstitutionDTO
{
    public function __construct(
        public string $name,
        public string $city,
        public string $state,
        public float $latitude,
        public float $longitude,
        public ?string $shortName = null,
        public ?string $district = null,
        public ?string $address = null,
        public ?int $defaultZoom = 16,
        public bool $isActive = true,
    ) {}
}
