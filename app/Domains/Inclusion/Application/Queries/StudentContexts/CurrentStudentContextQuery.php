<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentContexts;

use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;

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
