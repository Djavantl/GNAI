<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Peis;

final readonly class CreatePeiDTO
{
    public function __construct(
        public int $creatorId,
        public int $version,
        public bool $isCurrent = true,
        public bool $isFinished = false,
    ) {}
}
