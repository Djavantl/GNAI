<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Policies\Inspections;

use App\Domains\Inclusion\Application\Data\Inspections\CreateInspectionData;

final readonly class InspectionRegistrationPolicy
{
    public function shouldRegister(
        bool $stateChanged,
        CreateInspectionData $inspection,
    ): bool {
        return $stateChanged
            || filled($inspection->description)
            || $inspection->evidences !== [];
    }
}
