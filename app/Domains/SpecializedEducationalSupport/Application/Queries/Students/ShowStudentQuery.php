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
            'documents' => fn ($query) => $query->latest('id')->limit(5),
            'currentContext.semester',
            'currentContext.evaluator.person',
            'deficiencies',
            'peis' => fn ($query) => $query->latest('id')->limit(5),
            'peis.semester',
            'peis.course',
            'peis.peiDisciplines.teacher.person',
            'studentCourses.course',
            'courses',
            'currentCourse.course',
        ]);
    }
}
