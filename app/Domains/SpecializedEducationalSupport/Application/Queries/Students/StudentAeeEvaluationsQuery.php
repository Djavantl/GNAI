<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class StudentAeeEvaluationsQuery
{
    /**
     * @return Collection<int, AeeStudentEvaluation>
     */
    public function execute(Student $student, ?User $user): Collection
    {
        $query = AeeStudentEvaluation::query()
            ->with('aeeRecord.attendanceSession.professional.person')
            ->where('student_id', $student->getKey());

        if (! ($user?->can('aee-record.view-all') ?? false)) {
            $canViewOwn = $user?->can('aee-record.view-own') ?? false;
            $professionalId = $user?->professional_id;

            if ($canViewOwn && $professionalId !== null) {
                $query->whereHas(
                    'aeeRecord.attendanceSession',
                    fn (Builder $sessionQuery): Builder => $sessionQuery->where('professional_id', $professionalId),
                );
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }
}
