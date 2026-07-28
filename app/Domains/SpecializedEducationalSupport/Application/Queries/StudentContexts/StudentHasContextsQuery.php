<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;

final class StudentHasContextsQuery
{
    public function execute(Student $student): bool
    {
        return StudentContext::query()
            ->where('student_id', $student->getKey())
            ->exists();
    }
}
