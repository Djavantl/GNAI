<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\PedagogicalRecords;

use App\Domains\SpecializedEducationalSupport\Application\Data\PedagogicalRecords\CreatePedagogicalRecordData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\PedagogicalRecords\PedagogicalRecordDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreatePedagogicalRecordAction
{
    /** @throws Throwable */
    public function execute(CreatePedagogicalRecordData $data, int $professionalId): PedagogicalRecord
    {
        return DB::transaction(function () use ($data, $professionalId): PedagogicalRecord {
            $session = Session::query()->with([
                'students.currentCourse.course.disciplines',
                'aeeRecord',
                'pedagogicalRecord',
            ])->lockForUpdate()->findOrFail($data->attendanceSessionId);
            if ((int) $session->professional_id !== $professionalId) {
                throw new InvalidPedagogicalRecord('Apenas o profissional vinculado ao agendamento pode criar o registro pedagógico.');
            }
            if (! SessionStatus::isScheduledValue($session->status) || ! AttendanceType::isPedagogical($session->attendance_type)) {
                throw new InvalidPedagogicalRecord('O agendamento deve estar agendado e classificado como Atendimento Pedagógico.');
            }
            if ($session->students->count() !== 1 || $session->aeeRecord !== null || $session->pedagogicalRecord !== null) {
                throw new InvalidPedagogicalRecord('O agendamento não está disponível para receber um registro pedagógico.');
            }
            $this->ensureDisciplinesBelongToCurrentCourse(
                $session,
                $data->isPresent ? $data->failedDisciplineIds : [],
                $data->isPresent ? $data->atRiskDisciplineIds : [],
            );
            $record = PedagogicalRecord::register($session, $this->dto($data));
            $record->save();
            $record->syncFailedDisciplines($data->isPresent ? $data->failedDisciplineIds : []);
            $record->syncAtRiskDisciplines($data->isPresent ? $data->atRiskDisciplineIds : []);
            $session->update(['status' => SessionStatus::COMPLETED_DATABASE_VALUE]);

            return $record->load([
                'attendanceSession.students.person',
                'attendanceSession.professional.person',
                'failedDisciplines',
                'atRiskDisciplines',
            ]);
        });
    }

    private function dto(CreatePedagogicalRecordData $data): PedagogicalRecordDTO
    {
        return new PedagogicalRecordDTO(
            followUpReason: $data->followUpReason,
            followUpStatus: $data->followUpStatus,
            duration: $data->duration,
            isPresent: $data->isPresent,
            absenceReason: $data->absenceReason,
            systematicPedagogicalFollowUpRecord: $data->systematicPedagogicalFollowUpRecord,
            strategiesAndResourcesAdopted: $data->strategiesAndResourcesAdopted,
            schoolAttendanceStatus: $data->schoolAttendanceStatus,
            referralsMade: $data->referralsMade,
            complementaryObservations: $data->complementaryObservations,
        );
    }

    /**
     * @param  list<int>  $failedDisciplineIds
     * @param  list<int>  $atRiskDisciplineIds
     */
    private function ensureDisciplinesBelongToCurrentCourse(Session $session, array $failedDisciplineIds, array $atRiskDisciplineIds): void
    {
        $allowedIds = $session->students->first()?->currentCourse?->course?->disciplines
            ?->pluck('id')
            ->map(static fn (mixed $id): int => (int) $id)
            ->all() ?? [];
        $selectedIds = array_map('intval', [...$failedDisciplineIds, ...$atRiskDisciplineIds]);

        if (array_diff($selectedIds, $allowedIds) !== []) {
            throw new InvalidPedagogicalRecord('As disciplinas selecionadas devem pertencer ao curso atual do estudante.');
        }
    }
}
