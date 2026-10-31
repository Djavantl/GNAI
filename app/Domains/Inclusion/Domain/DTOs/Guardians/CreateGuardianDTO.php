<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\DTOs\Guardians;

use App\Domains\Inclusion\Domain\Enums\GuardianRelationship;

final readonly class CreateGuardianDTO
{
    public function __construct(
        public GuardianRelationship $relationship,
    ) {}
}
