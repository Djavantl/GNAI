<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Disciplines;

use App\Domains\Inclusion\Domain\Models\Discipline;

final class DisciplineHasTeachersQuery
{
    public function execute(Discipline $discipline): bool
    {
        return $discipline->teachers()->exists();
    }
}
