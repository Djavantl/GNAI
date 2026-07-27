<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Support\Facades\DB;

final class StudentHasLinkedRecordsQuery
{
    public function execute(Student $student): bool
    {
        return $student->guardians()->exists()
            || $student->contexts()->exists()
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
