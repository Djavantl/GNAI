<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Institutions;

use App\Domains\Inclusion\Domain\Models\Institution;

final class ShowInstitutionQuery
{
    public function execute(Institution $institution): Institution
    {
        return $institution->load(['locations', 'barriers']);
    }
}
