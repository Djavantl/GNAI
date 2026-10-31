<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Institutions;

use App\Domains\Inclusion\Application\Data\Institutions\CreateInstitutionData;
use App\Domains\Inclusion\Domain\Models\Institution;

final class InstitutionExistsQuery
{
    public function execute(): bool
    {
        return Institution::query()->exists();
    }
}
