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
            'guardians' => fn ($query) => $query->latest('id'),
            'guardians.person',
            'documents' => fn ($query) => $query->latest('id'),
            'documents.semester',
            'contexts' => fn ($query) => $query->orderByDesc('version'),
            'contexts.semester',
            'contexts.evaluator.person',
            'currentContext.semester',
            'currentContext.evaluator.person',
            'deficiencies',
            'peis' => fn ($query) => $query->latest('id'),
            'peis.semester',
            'peis.course',
            'peis.peiDisciplines.teacher.person',
            'studentCourses' => fn ($query) => $query
                ->orderByDesc('is_current')
                ->orderByDesc('academic_year')
                ->orderByDesc('id'),
            'studentCourses.course',
            'courses',
            'currentCourse.course',
        ]);
    }
}
