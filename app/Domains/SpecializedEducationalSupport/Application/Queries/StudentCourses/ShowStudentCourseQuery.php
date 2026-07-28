<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentCourses;

use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;

final class ShowStudentCourseQuery
{
    public function execute(StudentCourse $studentCourse): StudentCourse
    {
        return $studentCourse->load([
            'student.person',
            'course.disciplines',
        ]);
    }
}
