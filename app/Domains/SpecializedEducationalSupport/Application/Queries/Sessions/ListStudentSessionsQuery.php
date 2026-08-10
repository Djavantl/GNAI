<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\ListSessionsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentSessionsQuery
{
    public function execute(Student $student, ListSessionsData $filters): LengthAwarePaginator
    {
        return Session::query()
            ->with(['professional.person', 'students.person', 'aeeRecord', 'pedagogicalRecord'])
            ->whereHas('students', fn ($studentQuery) => $studentQuery->where('students.id', $student->id))
            ->when($filters->professional !== null, fn ($query) => $query->where('professional_id', $filters->professional))
            ->when($filters->type !== null && $filters->type !== '', fn ($query) => $query->where('type', $filters->type))
            ->when($filters->status !== null && $filters->status !== '', fn ($query) => $query->where('status', $filters->status))
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();
    }
}
