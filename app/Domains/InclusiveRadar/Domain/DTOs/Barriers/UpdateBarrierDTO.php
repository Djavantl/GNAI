<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\Barriers;

use App\Domains\InclusiveRadar\Domain\Enums\BarrierStatus;
use App\Enums\Priority;

final readonly class UpdateBarrierDTO
{
    public function __construct(
        public string $name,
        public int $institutionId,
        public int $barrierCategoryId,
        public Priority $priority,
        public string $identifiedAt,
        public ?string $description = null,
        public ?int $locationId = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $locationSpecificDetails = null,
        public ?int $affectedStudentId = null,
        public ?int $affectedProfessionalId = null,
        public ?string $affectedPersonName = null,
        public ?string $affectedPersonRole = null,
        public bool $isAnonymous = false,
        public bool $notApplicable = false,
        public bool $isActive = true,
        public ?BarrierStatus $status = null,
    ) {}
}
