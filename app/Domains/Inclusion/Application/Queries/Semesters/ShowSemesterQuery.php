<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Semesters;

use App\Domains\Inclusion\Domain\Models\Semester;

final class ShowSemesterQuery
{
    public function execute(Semester $semester): Semester
    {
        return $semester;
    }
}
