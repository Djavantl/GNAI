<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Support\Facades\DB;
use Throwable;

final class DeleteAeeStudentEvaluationAction
{
    /** @throws Throwable */
    public function execute(Student $student, AeeStudentEvaluation $evaluation, int $professionalId): void
    {
        DB::transaction(function () use ($student, $evaluation, $professionalId): void {
            $locked = AeeStudentEvaluation::query()->with('aeeRecord.attendanceSession')->lockForUpdate()->findOrFail($evaluation->getKey());
            if ((int) $locked->student_id !== (int) $student->getKey()) {
                throw new InvalidAeeRecord('A avaliação informada não pertence a este aluno.');
            }
            if ((int) $locked->aeeRecord->attendanceSession->professional_id !== $professionalId) {
                throw new InvalidAeeRecord('Apenas o profissional vinculado ao agendamento pode excluir esta avaliação.');
            }
            $locked->forceDelete();
        });
    }
}
