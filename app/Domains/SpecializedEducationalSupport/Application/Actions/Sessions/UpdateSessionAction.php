<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\UpdateSessionData;
use App\Domains\SpecializedEducationalSupport\Application\Services\Sessions\SessionNotificationSender;
use App\Domains\SpecializedEducationalSupport\Application\Services\Sessions\SessionSchedulingValidator;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Sessions\UpdateSessionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateSessionAction
{
    public function __construct(
        private SessionSchedulingValidator $validator,
        private SessionNotificationSender $notifications,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Session $session, UpdateSessionData $data, int $userId): Session
    {
        $session->ensureCreatedBy($userId, 'editá-la');
        $session->ensureIsScheduled();

        $updatedSession = DB::transaction(function () use ($session, $data): Session {
            $lockedSession = Session::query()
                ->with(['students', 'sessionRecord', 'pedagogicalRecord'])
                ->lockForUpdate()
                ->findOrFail($session->getKey());

            $payload = $this->validator->normalize([
                'professional_id' => $data->professionalId,
                'student_ids' => $data->studentIds,
                'session_date' => $data->sessionDate,
                'start_time' => $data->startTime,
                'end_time' => $data->endTime,
                'attendance_type' => $data->attendanceType,
                'type' => $data->type,
                'location' => $data->location,
                'session_objective' => $data->sessionObjective,
                'status' => $data->status ?? $lockedSession->status,
            ]);

            $this->validator->validateForUpdate($lockedSession, $payload);

            $sessionDTO = new UpdateSessionDTO(
                professionalId: (int) $payload['professional_id'],
                studentIds: $payload['student_ids'],
                sessionDate: (string) $payload['session_date'],
                startTime: (string) $payload['start_time'],
                endTime: (string) $payload['end_time'],
                type: (string) $payload['type'],
                attendanceType: (string) $payload['attendance_type'],
                location: (string) $payload['location'],
                sessionObjective: (string) $payload['session_objective'],
                status: (string) $payload['status'],
            );

            $lockedSession->revise($sessionDTO);
            $lockedSession->save();
            $lockedSession->students()->sync($payload['student_ids']);

            return $lockedSession->fresh(['students.person', 'professional.person', 'sessionRecord', 'pedagogicalRecord']);
        });

        $this->notifications->send(
            $updatedSession,
            'Agendamento de Atendimento Atualizado',
            'Houve uma alteração nos detalhes do seu agendamento.',
        );

        return $updatedSession;
    }
}
