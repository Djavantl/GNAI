<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Sessions;

use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\CreateSessionData;
use App\Domains\SpecializedEducationalSupport\Application\Services\Sessions\SessionNotificationSender;
use App\Domains\SpecializedEducationalSupport\Application\Services\Sessions\SessionSchedulingValidator;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Sessions\CreateSessionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

final readonly class CreateSessionAction
{
    public function __construct(
        private SessionSchedulingValidator $validator,
        private SessionNotificationSender $notifications,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateSessionData $data, int $creatorId): Session
    {
        $payload = $this->validator->normalize([
            'professional_id' => $data->professionalId,
            'student_ids' => $data->studentIds,
            'session_date' => $data->sessionDate,
            'start_time' => $data->startTime,
            'end_time' => $data->endTime,
            'attendance_type' => $data->attendanceType->value,
            'type' => $data->type->value,
            'location' => $data->location,
            'session_objective' => $data->sessionObjective,
        ]);

        $lockKey = 'sessions:create:'.sha1(json_encode([
            'professional_id' => $payload['professional_id'],
            'student_ids' => $payload['student_ids'],
            'session_date' => $payload['session_date'],
            'start_time' => $payload['start_time'],
            'end_time' => $payload['end_time'],
        ], JSON_THROW_ON_ERROR));

        $lock = Cache::lock($lockKey, 10);

        if (! $lock->get()) {
            throw ValidationException::withMessages([
                'start_time' => 'Este agendamento já está sendo processado. Aguarde alguns segundos e tente novamente.',
            ]);
        }

        try {
            $session = DB::transaction(function () use ($payload, $creatorId): Session {
                $this->validator->validateForCreation($payload);

                $sessionDTO = new CreateSessionDTO(
                    professionalId: (int) $payload['professional_id'],
                    creatorId: $creatorId,
                    studentIds: $payload['student_ids'],
                    sessionDate: (string) $payload['session_date'],
                    startTime: (string) $payload['start_time'],
                    endTime: (string) $payload['end_time'],
                    type: SessionType::from((string) $payload['type']),
                    attendanceType: AttendanceType::from((string) $payload['attendance_type']),
                    location: (string) $payload['location'],
                    sessionObjective: (string) $payload['session_objective'],
                );

                $session = Session::schedule($sessionDTO);
                $session->save();
                $session->students()->sync($payload['student_ids']);

                return $session->fresh(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord']);
            });
        } finally {
            try {
                $lock->release();
            } catch (Throwable) {
                // Lock can expire before release. Creation already finished.
            }
        }

        if ($data->sendNotification && ! $session->sessionDateHasPassed()) {
            $this->notifications->send(
                $session,
                'Novo Agendamento Criado',
                'Um novo agendamento foi registrado.',
            );
        }

        return $session;
    }
}
