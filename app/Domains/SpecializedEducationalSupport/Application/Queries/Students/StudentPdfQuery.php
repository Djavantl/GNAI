<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class StudentPdfQuery
{
    public function execute(Student $student): Student
    {
        return $student->load([
            'person',
            'deficiencies',
            'currentCourse.course',
            'guardians.person',
            'currentContext.semester',
            'currentContext.evaluator.person',
            'peis' => function ($query): void {
                $query
                    ->where('is_current', true)
                    ->with([
                        'semester',
                        'course',
                        'studentContext',
                        'creator',
                        'peiDisciplines' => function ($disciplineQuery): void {
                            $disciplineQuery
                                ->with([
                                    'discipline',
                                    'teacher.person',
                                    'creator',
                                ])
                                ->orderBy('id');
                        },
                    ])
                    ->orderBy('version');
            },
        ]);
    }
}
