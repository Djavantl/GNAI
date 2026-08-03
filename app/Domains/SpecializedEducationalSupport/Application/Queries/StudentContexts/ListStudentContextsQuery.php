<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\StudentContexts;

use App\Domains\SpecializedEducationalSupport\Application\Data\StudentContexts\ListStudentContextsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentContextsQuery
{
    /**
     * @return LengthAwarePaginator<int, StudentContext>
     */
    public function execute(Student $student, ListStudentContextsData $filters): LengthAwarePaginator
    {
        $query = StudentContext::query()
            ->where('student_id', $student->getKey())
            ->with(['semester', 'evaluator.person']);

        if ($filters->semesterId !== null) {
            $query->where('semester_id', $filters->semesterId);
        }

        if ($filters->evaluationType !== null) {
            $query->where('evaluation_type', $filters->evaluationType->value);
        }

        if ($filters->isCurrent !== null) {
            $query->where('is_current', $filters->isCurrent);
        }

        return $query
            ->orderByDesc('version')
            ->paginate($filters->perPage)
            ->withQueryString();
    }
}
