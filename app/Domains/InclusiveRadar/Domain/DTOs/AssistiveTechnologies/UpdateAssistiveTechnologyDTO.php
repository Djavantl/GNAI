<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;

final readonly class UpdateAssistiveTechnologyDTO
{
    public function __construct(
        public string $name,
        public bool $digital,
        public bool $loanable,
        public ?int $quantity,
        public ?AssetCode $assetCode,
        public ConservationState $conservationState,
        public ResourceStatus $status,
        public int $openLoans,
        public ?string $notes = null,
        public bool $active = true,
    ) {}
}
