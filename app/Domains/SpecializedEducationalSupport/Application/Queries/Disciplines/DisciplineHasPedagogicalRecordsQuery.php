<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;

final class DisciplineHasPedagogicalRecordsQuery
{
    public function execute(Discipline $discipline): bool
    {
        return $discipline->pedagogicalRecords()->exists();
    }
}
