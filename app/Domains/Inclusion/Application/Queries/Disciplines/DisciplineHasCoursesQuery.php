<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Disciplines;

use App\Domains\Inclusion\Domain\Models\Discipline;

final class DisciplineHasCoursesQuery
{
    public function execute(Discipline $discipline): bool
    {
        return $discipline->courses()->exists();
    }
}
