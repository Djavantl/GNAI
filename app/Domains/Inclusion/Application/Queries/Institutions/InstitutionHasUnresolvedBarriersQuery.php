<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Institutions;

use App\Domains\Inclusion\Domain\Models\Institution;

final class InstitutionHasUnresolvedBarriersQuery
{
    public function execute(Institution $institution): bool
    {
        return $institution
            ->barriers()
            ->whereNull('resolved_at')
            ->exists();
    }
}
