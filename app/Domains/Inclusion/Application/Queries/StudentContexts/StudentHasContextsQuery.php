<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\StudentContexts;

use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;

final class StudentHasContextsQuery
{
    public function execute(Student $student): bool
    {
        return StudentContext::query()
            ->where('student_id', $student->getKey())
            ->exists();
    }
}
