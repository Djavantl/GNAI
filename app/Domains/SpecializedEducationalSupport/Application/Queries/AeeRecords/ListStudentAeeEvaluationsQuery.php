<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\AeeRecords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\ListAeeRecordsData;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListStudentAeeEvaluationsQuery
{
    public function execute(Student $student, ListAeeRecordsData $filters, User $user): LengthAwarePaginator
    {
        return AeeStudentEvaluation::query()
            ->with('aeeRecord.attendanceSession.professional.person')
            ->where('student_id', $student->getKey())
            ->when(! $user->can('aee-record.view-all'), fn ($query) => $query->whereHas('aeeRecord.attendanceSession', fn ($session) => $session->where('professional_id', $user->professional_id ?? 0)))
            ->when($filters->professionalId !== null, fn ($query) => $query->whereHas('aeeRecord.attendanceSession', fn ($session) => $session->where('professional_id', $filters->professionalId)))
            ->when($filters->isPresent !== null, fn ($query) => $query->where('is_present', $filters->isPresent))
            ->orderByDesc('id')
            ->paginate($filters->perPage)
            ->withQueryString();
    }

    public function show(Student $student, AeeStudentEvaluation $evaluation, User $user): AeeStudentEvaluation
    {
        $evaluation->load(['student.person', 'aeeRecord.attendanceSession.professional.person']);
        if ((int) $evaluation->student_id !== (int) $student->getKey() || (! $user->can('aee-record.view-all') && (int) $evaluation->aeeRecord->attendanceSession->professional_id !== (int) $user->professional_id)) {
            throw new InvalidAeeRecord('A avaliação AEE informada não está disponível para este usuário.');
        }

        return $evaluation;
    }
}
