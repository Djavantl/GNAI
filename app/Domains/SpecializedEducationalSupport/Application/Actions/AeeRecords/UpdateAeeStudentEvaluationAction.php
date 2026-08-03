<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\UpdateAeeStudentEvaluationData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords\AeeStudentEvaluationDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Support\Facades\DB;
use Throwable;

final class UpdateAeeStudentEvaluationAction
{
    /** @throws Throwable */
    public function execute(Student $student, AeeStudentEvaluation $evaluation, UpdateAeeStudentEvaluationData $data, int $professionalId): AeeStudentEvaluation
    {
        return DB::transaction(function () use ($student, $evaluation, $data, $professionalId): AeeStudentEvaluation {
            $locked = AeeStudentEvaluation::query()->with('aeeRecord.attendanceSession')->lockForUpdate()->findOrFail($evaluation->getKey());
            if ((int) $locked->student_id !== (int) $student->getKey() || $data->studentId !== (int) $student->getKey()) {
                throw new InvalidAeeRecord('A avaliação informada não pertence a este aluno.');
            }
            if ((int) $locked->aeeRecord->attendanceSession->professional_id !== $professionalId) {
                throw new InvalidAeeRecord('Apenas o profissional vinculado ao agendamento pode editar esta avaliação.');
            }
            $locked->revise(new AeeStudentEvaluationDTO(
                studentId: $data->studentId,
                isPresent: $data->isPresent,
                absenceReason: $data->absenceReason,
                adaptationsMade: $data->adaptationsMade,
                studentParticipation: $data->studentParticipation,
                developmentEvaluation: $data->developmentEvaluation,
                progressIndicators: $data->progressIndicators,
                recommendations: $data->recommendations,
                nextSessionAdjustments: $data->nextSessionAdjustments,
            ));
            $locked->save();

            return $locked->load([
                'student.person',
                'aeeRecord.attendanceSession.professional.person',
            ]);
        });
    }
}
