<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\AeeRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\AeeRecords\CreateAeeRecordData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords\AeeStudentEvaluationDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\AeeRecords\CreateAeeRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidAeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateAeeRecordAction
{
    /** @throws Throwable */
    public function execute(CreateAeeRecordData $data, int $professionalId): AeeRecord
    {
        return DB::transaction(function () use ($data, $professionalId): AeeRecord {
            $session = Session::query()->with(['students', 'aeeRecord', 'pedagogicalRecord'])->lockForUpdate()->findOrFail($data->attendanceSessionId);
            $this->ensureCanCreate($session, $professionalId, $data->evaluations);
            $dto = $this->dto($data);
            $record = AeeRecord::register($session, $dto);
            $record->save();

            foreach ($dto->evaluations as $evaluationDTO) {
                $student = $session->students->firstWhere('id', $evaluationDTO->studentId);
                AeeStudentEvaluation::register($record, $student, $evaluationDTO)->save();
            }

            $session->update(['status' => SessionStatus::COMPLETED_DATABASE_VALUE]);

            return $record->load([
                'attendanceSession.professional.person',
                'studentEvaluations.student.person',
            ]);
        });
    }

    private function ensureCanCreate(Session $session, int $professionalId, array $evaluations): void
    {
        if ((int) $session->professional_id !== $professionalId) {
            throw new InvalidAeeRecord('Apenas o profissional vinculado ao agendamento pode criar o registro AEE.');
        }
        if (! SessionStatus::isScheduledValue($session->status)) {
            throw new InvalidAeeRecord('O agendamento precisa estar com status Agendada para receber o registro AEE.');
        }
        if (! $session->sessionDateHasArrived()) {
            throw new InvalidAeeRecord('O registro AEE só pode ser criado quando a data do agendamento chegar.');
        }
        if (! AttendanceType::isAee($session->attendance_type)) {
            throw new InvalidAeeRecord('Este agendamento não está classificado como Atendimento AEE.');
        }
        if ($session->aeeRecord !== null || $session->pedagogicalRecord !== null) {
            throw new InvalidAeeRecord('Este agendamento já possui um registro de atendimento.');
        }

        $expected = $session->students->pluck('id')->map(fn ($id): int => (int) $id)->sort()->values()->all();
        $received = collect($evaluations)->pluck('student_id')->map(fn ($id): int => (int) $id)->unique()->sort()->values()->all();
        if ($expected !== $received) {
            throw new InvalidAeeRecord('As avaliações devem corresponder exatamente aos alunos vinculados ao agendamento.');
        }
    }

    private function dto(CreateAeeRecordData $data): CreateAeeRecordDTO
    {
        return new CreateAeeRecordDTO(
            duration: $data->duration,
            activitiesPerformed: $data->activitiesPerformed,
            strategiesUsed: $data->strategiesUsed,
            resourcesUsed: $data->resourcesUsed,
            generalObservations: $data->generalObservations,
            evaluations: collect($data->evaluations)
                ->map(fn (array $evaluation): AeeStudentEvaluationDTO => $this->evaluationDTO($evaluation))
                ->values()
                ->all(),
        );
    }

    private function evaluationDTO(array $data): AeeStudentEvaluationDTO
    {
        return new AeeStudentEvaluationDTO(
            studentId: (int) $data['student_id'],
            isPresent: (bool) $data['is_present'],
            absenceReason: $data['absence_reason'] ?? null,
            adaptationsMade: $data['adaptations_made'] ?? null,
            studentParticipation: $data['student_participation'] ?? null,
            developmentEvaluation: $data['development_evaluation'] ?? null,
            progressIndicators: $data['progress_indicators'] ?? null,
            recommendations: $data['recommendations'] ?? null,
            nextSessionAdjustments: $data['next_session_adjustments'] ?? null,
        );
    }
}
