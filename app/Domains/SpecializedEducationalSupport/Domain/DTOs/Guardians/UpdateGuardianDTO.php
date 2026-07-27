<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\DTOs\Guardians;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\GuardianRelationship;

final readonly class UpdateGuardianDTO
{
    public function __construct(
        public GuardianRelationship $relationship,
    ) {}
}
