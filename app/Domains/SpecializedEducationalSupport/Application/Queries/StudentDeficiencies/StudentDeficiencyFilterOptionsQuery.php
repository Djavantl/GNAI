<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;

final class StudentDeficiencyFilterOptionsQuery
{
    /**
     * @return array<int, string>
     */
    public function execute(Student $student): array
    {
        return Deficiency::query()
            ->whereHas('students', static function ($query) use ($student): void {
                $query->where('student_id', $student->getKey());
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
