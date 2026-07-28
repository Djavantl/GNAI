<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentDeficiencies;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentDeficiencies\ListStudentDeficienciesData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentDeficienciesQuery
{
    /**
     * @return LengthAwarePaginator<int, StudentDeficiency>
     */
    public function execute(Student $student, ListStudentDeficienciesData $filters): LengthAwarePaginator
    {
        return StudentDeficiency::query()
            ->with('deficiency')
            ->where('student_id', $student->getKey())
            ->when($filters->deficiencyId !== null, static function ($query) use ($filters): void {
                $query->where('deficiency_id', $filters->deficiencyId);
            })
            ->when($filters->severity !== null, static function ($query) use ($filters): void {
                $query->where('severity', $filters->severity->value);
            })
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
