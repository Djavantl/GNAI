<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Disciplines;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;

final class DisciplineHasTeachersQuery
{
    public function execute(Discipline $discipline): bool
    {
        return $discipline->teachers()->exists();
    }
}
