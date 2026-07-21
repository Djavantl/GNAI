<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Institutions;

use App\Domains\InclusiveRadar\Domain\Models\Institution;

final class ShowInstitutionQuery
{
    public function execute(Institution $institution): Institution
    {
        return $institution->load(['locations', 'barriers']);
    }
}
