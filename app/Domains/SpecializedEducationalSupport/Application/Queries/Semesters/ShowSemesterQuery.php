<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Semesters;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;

final class ShowSemesterQuery
{
    public function execute(Semester $semester): Semester
    {
        return $semester;
    }
}
