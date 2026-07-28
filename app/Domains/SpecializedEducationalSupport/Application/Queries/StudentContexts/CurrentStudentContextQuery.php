<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;

final class CurrentStudentContextQuery
{
    public function execute(Student $student): StudentContext
    {
        return StudentContext::query()
            ->where('student_id', $student->getKey())
            ->where('is_current', true)
            ->with([
                'student.person',
                'student.deficiencies',
                'semester',
                'evaluator.person',
            ])
            ->firstOrFail();
    }
}
