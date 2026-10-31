<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Teachers;

use App\Domains\Inclusion\Domain\Models\Teacher;

final class ShowTeacherQuery
{
    public function execute(Teacher $teacher): Teacher
    {
        return $teacher->load([
            'person',
            'courses',
            'courseDisciplines.discipline',
            'courseDisciplines.course',
        ]);
    }
}
