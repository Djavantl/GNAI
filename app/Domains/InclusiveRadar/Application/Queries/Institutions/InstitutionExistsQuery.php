<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\InclusiveRadar\Domain\Models\Institution;

final class InstitutionExistsQuery
{
    public function execute(): bool
    {
        return Institution::query()->exists();
    }
}
