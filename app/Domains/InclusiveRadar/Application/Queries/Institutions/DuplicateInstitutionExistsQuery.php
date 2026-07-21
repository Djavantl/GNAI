<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Queries\Institutions;

use App\Domains\InclusiveRadar\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\InclusiveRadar\Domain\Models\Institution;

final class DuplicateInstitutionExistsQuery
{
    public function execute(CreateInstitutionData $data): bool
    {
        return Institution::query()
            ->where('name', trim($data->name))
            ->where('city', trim($data->city))
            ->where('state', trim($data->state))
            ->where('latitude', $data->latitude)
            ->where('longitude', $data->longitude)
            ->exists();
    }
}
