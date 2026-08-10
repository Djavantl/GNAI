<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
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
        return $this->query($student, $user)
            ->orderByDesc('id')
            ->limit(5)
            ->get();
    }

    /**
     * @return Collection<int, AeeStudentEvaluation>
     */
    public function history(Student $student, User $user): Collection
    {
        return $this->query($student, $user)
            ->orderBy(
                Session::query()
                    ->select('session_date')
                    ->join('aee_records', 'aee_records.attendance_session_id', '=', 'attendance_sessions.id')
                    ->whereColumn('aee_records.id', 'aee_student_evaluations.aee_record_id')
                    ->limit(1),
            )
            ->orderBy(
                Session::query()
                    ->select('start_time')
                    ->join('aee_records', 'aee_records.attendance_session_id', '=', 'attendance_sessions.id')
                    ->whereColumn('aee_records.id', 'aee_student_evaluations.aee_record_id')
                    ->limit(1),
            )
            ->orderBy('id')
            ->get();
    }

    /** @return Builder<AeeStudentEvaluation> */
    private function query(Student $student, ?User $user): Builder
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

        return $query;
    }
}
