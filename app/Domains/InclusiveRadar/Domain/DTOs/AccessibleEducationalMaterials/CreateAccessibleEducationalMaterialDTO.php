<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials;

use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;

final readonly class CreateAccessibleEducationalMaterialDTO
{
    public function __construct(
        public string $name,
        public bool $isDigital,
        public bool $isLoanable,
        public ?int $quantity,
        public ?AssetCode $assetCode,
        public ConservationState $conservationState,
        public ResourceStatus $status = ResourceStatus::AVAILABLE,
        public ?string $notes = null,
        public bool $isActive = true,
    ) {}
}
