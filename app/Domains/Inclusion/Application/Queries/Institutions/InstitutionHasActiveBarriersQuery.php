<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Institutions;

use App\Domains\Inclusion\Domain\Models\Institution;

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
