<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentCourses;

use App\Domains\Inclusion\Domain\Models\StudentCourse;

final class ShowStudentCourseQuery
{
    public function execute(StudentCourse $studentCourse): StudentCourse
    {
        return $studentCourse->load([
            'student.person',
            'course.disciplines',
            'failedDisciplines',
            'atRiskDisciplines',
        ]);
    }
}
