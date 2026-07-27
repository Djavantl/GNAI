<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class ShowStudentQuery
{
    public function execute(Student $student): Student
    {
        return $student->load([
            'person',
            'guardians.person',
            'documents',
            'currentContext.semester',
            'currentContext.evaluator.person',
            'deficiencies',
            'peis.course',
            'studentCourses.course',
            'courses',
            'currentCourse.course',
        ]);
    }
}
