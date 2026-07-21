<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Institutions;

use App\Domains\InclusiveRadar\Domain\Models\Institution;

final class InstitutionHasActiveBarriersQuery
{
    public function execute(Institution $institution): bool
    {
        return $institution
            ->barriers()
            ->get()
            ->contains(function ($barrier): bool {
                $status = $barrier->latestStatus();

                if (! $status) {
                    return true;
                }

                return ! $status->allowsDeletion();
            });
    }
}
