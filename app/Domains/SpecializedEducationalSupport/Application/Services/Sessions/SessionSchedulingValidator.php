<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Services\Sessions;

use App\Domains\Auth\Application\Queries\Permissions\UserHasPermissionQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions\DetectSessionConflictQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidProfessional;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidSession;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

final readonly class SessionSchedulingValidator
{
    public function __construct(
        private DetectSessionConflictQuery $detectSessionConflict,
        private UserHasPermissionQuery $userHasPermission,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        $data['type'] = Session::typeForAttendance(
            attendanceType: (string) $data['attendance_type'],
            type: (string) $data['type'],
        );

        $data['start_time'] = Carbon::parse($data['start_time'])->format('H:i');
        $data['end_time'] = empty($data['end_time'])
            ? Carbon::parse($data['start_time'])->addHour()->format('H:i')
            : Carbon::parse($data['end_time'])->format('H:i');

        $data['student_ids'] = array_values(array_unique(array_map('intval', $data['student_ids'] ?? [])));

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function validateForCreation(array $data): void
    {
        $this->ensureAttendanceTypeAcceptsStudents($data);
        $this->ensureProfessionalIsActive((int) $data['professional_id']);
        $this->ensureProfessionalCanCreateAttendanceRecord((int) $data['professional_id'], (string) $data['attendance_type']);
        $this->ensureStudentsCanReceiveAttendance($data['student_ids'], (string) $data['attendance_type']);
        $this->ensureNoConflict($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function validateForUpdate(Session $session, array $data): void
    {
        $this->ensureCanChangeAttendanceType($session, (string) $data['attendance_type']);
        $this->ensureAttendanceTypeAcceptsStudents($data);
        $this->ensureProfessionalIsActive((int) $data['professional_id']);
        $this->ensureProfessionalCanCreateAttendanceRecord((int) $data['professional_id'], (string) $data['attendance_type']);
        $this->ensureStudentsCanReceiveAttendance($data['student_ids'], (string) $data['attendance_type']);
        $this->ensureNoConflict($data, (int) $session->getKey());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function ensureAttendanceTypeAcceptsStudents(array $data): void
    {
        try {
            Session::ensureAttendanceTypeAcceptsStudents(
                attendanceType: (string) $data['attendance_type'],
                studentIds: $data['student_ids'] ?? [],
            );
        } catch (InvalidSession $exception) {
            throw ValidationException::withMessages([
                'student_ids' => $exception->getMessage(),
            ]);
        }
    }

    private function ensureCanChangeAttendanceType(Session $session, string $attendanceType): void
    {
        try {
            $session->ensureCanChangeAttendanceTypeTo($attendanceType);
        } catch (InvalidSession $exception) {
            throw ValidationException::withMessages([
                'attendance_type' => $exception->getMessage(),
            ]);
        }
    }

    private function ensureProfessionalIsActive(int $professionalId): void
    {
        $professional = Professional::with('person')->find($professionalId);

        if ($professional === null) {
            throw ValidationException::withMessages([
                'professional_id' => 'Profissional não encontrado.',
            ]);
        }

        try {
            $professional->ensureIsActive();
        } catch (InvalidProfessional) {
            throw ValidationException::withMessages([
                'professional_id' => "Não é possível agendar ou editar agendamento para profissional inativo: {$professional->person->name}.",
            ]);
        }
    }

    private function ensureProfessionalCanCreateAttendanceRecord(int $professionalId, string $attendanceType): void
    {
        $professional = Professional::with('position.permissions', 'person', 'user')->findOrFail($professionalId);
        $permission = AttendanceType::isPedagogical($attendanceType) ? 'pedagogical-record.create' : 'aee-record.create';

        $canCreateRecords = $professional->user !== null
            ? $this->userHasPermission->execute($professional->user, $permission)
            : ($professional->position?->permissions?->contains('slug', $permission) ?? false);

        if (! $canCreateRecords) {
            throw ValidationException::withMessages([
                'professional_id' => 'Não é possível criar agendamento para um profissional que não possui permissão para criar registros de agendamento.',
            ]);
        }
    }

    /**
     * @param  list<int>  $studentIds
     */
    private function ensureStudentsCanReceiveAttendance(array $studentIds, string $attendanceType): void
    {
        $students = Student::whereIn('id', $studentIds)
            ->with('person')
            ->get();

        $inactiveStudents = $students
            ->reject(fn (Student $student): bool => $student->status->isEnabled())
            ->pluck('person.name')
            ->toArray();

        if ($inactiveStudents !== []) {
            $names = implode(', ', $inactiveStudents);

            throw ValidationException::withMessages([
                'student_ids' => "Não é possível agendar ou editar agendamento para aluno inativo: {$names}.",
            ]);
        }

        $incompatibleStudents = $students
            ->reject(fn (Student $student): bool => $student->status->allowsAttendanceType($attendanceType))
            ->pluck('person.name')
            ->toArray();

        if ($incompatibleStudents !== []) {
            $names = implode(', ', $incompatibleStudents);
            $attendanceLabel = AttendanceType::labelFor($attendanceType);

            throw ValidationException::withMessages([
                'student_ids' => "O status de atendimento de {$names} não permite agendamentos do tipo {$attendanceLabel}.",
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function ensureNoConflict(array $data, ?int $ignoreId = null): void
    {
        $conflict = $this->detectSessionConflict->execute($data, $ignoreId);

        if (! $conflict['hasConflict']) {
            return;
        }

        $errors = [];

        if ($conflict['students'] !== []) {
            $names = implode(', ', $conflict['students']);
            $errors['student_ids'] = "Conflito de agenda para: {$names}.";
        }

        if ($conflict['professional']) {
            $errors['professional_id'] = 'O profissional já possui um agendamento neste horário.';
        }

        throw ValidationException::withMessages($errors);
    }
}
