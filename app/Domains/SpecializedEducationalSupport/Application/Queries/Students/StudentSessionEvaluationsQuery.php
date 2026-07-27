<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Models\SpecializedEducationalSupport\StudentSessionEvaluation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class StudentSessionEvaluationsQuery
{
    /**
     * @return LengthAwarePaginator<int, StudentSessionEvaluation>
     */
    public function execute(Student $student, ?User $user, int $perPage = 5): LengthAwarePaginator
    {
        $query = StudentSessionEvaluation::query()
            ->with('sessionRecord.attendanceSession.professional.person')
            ->where('student_id', $student->getKey());

        if (! ($user?->can('session-record.view-all') ?? false)) {
            $canViewOwn = $user?->can('session-record.view-own') ?? false;
            $professionalId = $user?->professional_id;

            if ($canViewOwn && $professionalId !== null) {
                $query->whereHas(
                    'sessionRecord.attendanceSession',
                    fn (Builder $sessionQuery): Builder => $sessionQuery->where('professional_id', $professionalId),
                );
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
