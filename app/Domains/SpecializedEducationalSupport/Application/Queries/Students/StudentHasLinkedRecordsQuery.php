<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts\StudentHasContextsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Support\Facades\DB;

final readonly class StudentHasLinkedRecordsQuery
{
    public function __construct(
        private StudentHasContextsQuery $studentHasContextsQuery,
    ) {}

    public function execute(Student $student): bool
    {
        return $student->guardians()->exists()
            || $this->studentHasContextsQuery->execute($student)
            || $student->deficiencies()->exists()
            || $student->peis()->exists()
            || $student->studentCourses()->exists()
            || $student->documents()->exists()
            || $student->sessions()->exists()
            || $student->sessionEvaluations()->exists()
            || DB::table('loans')->where('student_id', $student->getKey())->exists()
            || DB::table('waitlists')->where('student_id', $student->getKey())->exists();
    }
}
