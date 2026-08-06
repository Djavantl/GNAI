<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Students;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class StudentPedagogicalRecordsQuery
{
    /**
     * @return Collection<int, PedagogicalRecord>
     */
    public function execute(Student $student, ?User $user): Collection
    {
        $query = PedagogicalRecord::query()
            ->with([
                'attendanceSession.professional.person',
                'attendanceSession.students.person',
                'guardians.person',
            ])
            ->whereHas(
                'attendanceSession.students',
                fn (Builder $studentQuery): Builder => $studentQuery->where('students.id', $student->getKey()),
            );

        if (! ($user?->can('pedagogical-record.view-all') ?? false)) {
            $canViewOwn = $user?->can('pedagogical-record.view-own') ?? false;
            $professionalId = $user?->professional_id;

            if ($canViewOwn && $professionalId !== null) {
                $query->whereHas(
                    'attendanceSession',
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
