<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Sessions\CreateSessionDTO;
use App\Domains\Inclusion\Domain\DTOs\Sessions\UpdateSessionDTO;
use App\Domains\Inclusion\Domain\Enums\AttendanceType;
use App\Domains\Inclusion\Domain\Enums\SessionStatus;
use App\Domains\Inclusion\Domain\Enums\SessionType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Session extends Model implements Auditable
{
    use AuditsChanges;
    use SoftDeletes;

    protected $table = 'attendance_sessions';

    protected $fillable = [
        'professional_id',
        'creator_id',
        'session_date',
        'start_time',
        'end_time',
        'type',
        'attendance_type',
        'location',
        'session_objective',
        'status',
        'cancellation_reason',
    ];

    protected $casts = [
        'session_date' => 'date',
        'attendance_type' => AttendanceType::class,
    ];

    public static function auditType(): string
    {
        return 'session';
    }

    public static function auditLabel(): string
    {
        return 'Agendamento';
    }

    public static function auditedFields(): array
    {
        return [
            // O nome de profissionais e alunos mora em `people`; o histórico guarda o vínculo pelo id.
            'professional_id' => AuditedField::reference('Profissional', Professional::class, 'person.name'),
            'creator_id' => AuditedField::reference('Criado por', User::class),
            'session_date' => AuditedField::date('Data do agendamento'),
            'start_time' => AuditedField::time('Início'),
            'end_time' => AuditedField::time('Fim'),
            'type' => AuditedField::enum('Formato', SessionType::class),
            'attendance_type' => AuditedField::enum('Tipo de atendimento', AttendanceType::class),
            'location' => AuditedField::text('Local'),
            'session_objective' => AuditedField::text('Objetivo do agendamento'),
            // O status é gravado com o rótulo em português ("Agendada").
            'status' => AuditedField::enum('Status', valueLabels: SessionStatus::options()),
            'cancellation_reason' => AuditedField::text('Motivo do cancelamento'),
            'students' => AuditedField::list('Alunos', model: Student::class, nameColumn: 'person.name'),
        ];
    }

    public function auditDescription(): string
    {
        $date = $this->session_date?->format('d/m/Y');

        return $date !== null
            ? "o agendamento #{$this->getKey()} de {$date}"
            : "o agendamento #{$this->getKey()}";
    }

    /**
     * @return array<string, mixed>
     */
    public function auditExtraValues(): array
    {
        return $this->exists ? ['students' => $this->studentIds()] : [];
    }

    /**
     * @param  list<int>  $studentIds
     */
    public function syncStudents(array $studentIds): void
    {
        $before = $this->studentIds();

        $this->students()->sync($studentIds);
        $this->recordAuditFieldChange('students', $before, $this->studentIds());
    }

    /**
     * @throws InvalidSession
     */
    public static function schedule(CreateSessionDTO $data): self
    {
        self::ensureAttendanceTypeAcceptsStudents($data->attendanceType, $data->studentIds);

        return new self([
            'professional_id' => $data->professionalId,
            'creator_id' => $data->creatorId,
            'session_date' => $data->sessionDate,
            'start_time' => $data->startTime,
            'end_time' => $data->endTime,
            'type' => $data->type->value,
            'attendance_type' => $data->attendanceType,
            'location' => $data->location,
            'session_objective' => $data->sessionObjective,
            'status' => $data->status,
        ]);
    }

    /**
     * @throws InvalidSession
     */
    public function revise(UpdateSessionDTO $data): void
    {
        $this->ensureCanChangeAttendanceTypeTo($data->attendanceType);
        self::ensureAttendanceTypeAcceptsStudents($data->attendanceType, $data->studentIds);

        $this->fill([
            'professional_id' => $data->professionalId,
            'session_date' => $data->sessionDate,
            'start_time' => $data->startTime,
            'end_time' => $data->endTime,
            'type' => $data->type->value,
            'attendance_type' => $data->attendanceType,
            'location' => $data->location,
            'session_objective' => $data->sessionObjective,
            'status' => $data->status,
        ]);
    }

    /**
     * @throws InvalidSession
     */
    public function ensureCreatedBy(int $userId, string $action): void
    {
        if ((int) $this->creator_id !== $userId) {
            throw new InvalidSession("Apenas quem criou o agendamento pode {$action}.");
        }
    }

    /**
     * @throws InvalidSession
     */
    public function ensureIsScheduled(): void
    {
        if (! $this->isScheduled()) {
            throw new InvalidSession('Apenas agendamentos com status Agendada podem ser editados.');
        }
    }

    public function cancel(string $reason): void
    {
        $this->fill([
            'status' => SessionStatus::CANCELLED->label(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function ensureCanBeCancelled(): void
    {
        if (! $this->isScheduled()) {
            throw new InvalidSession('Apenas agendamentos com status Agendada podem ser cancelados.');
        }

        if (! $this->sessionDateIsUpcoming()) {
            throw new InvalidSession('Apenas agendamentos de datas futuras podem ser cancelados.');
        }
    }

    public function sessionDateHasPassed(): bool
    {
        return $this->session_date->copy()->startOfDay()->isBefore(today());
    }

    public function sessionDateHasArrived(): bool
    {
        return ! $this->session_date->copy()->startOfDay()->isAfter(today());
    }

    public function sessionDateIsUpcoming(): bool
    {
        return $this->session_date->copy()->startOfDay()->isAfter(today());
    }

    public static function typeForAttendance(AttendanceType|string $attendanceType, string $type): string
    {
        if (AttendanceType::isPedagogical($attendanceType)) {
            return SessionType::INDIVIDUAL->value;
        }

        return $type;
    }

    public static function acceptsStudentCountForAttendance(AttendanceType|string $attendanceType, int $studentCount): bool
    {
        return ! AttendanceType::isPedagogical($attendanceType) || $studentCount === 1;
    }

    /**
     * @param  list<int>  $studentIds
     *
     * @throws InvalidSession
     */
    public static function ensureAttendanceTypeAcceptsStudents(AttendanceType|string $attendanceType, array $studentIds): void
    {
        if (! self::acceptsStudentCountForAttendance($attendanceType, count($studentIds))) {
            throw new InvalidSession('Atendimentos pedagógicos devem possuir exatamente um aluno.');
        }
    }

    public function hasLinkedAttendanceRecord(): bool
    {
        return $this->aeeRecord()->exists() || $this->pedagogicalRecord()->exists();
    }

    public function canChangeAttendanceTypeTo(AttendanceType|string $attendanceType): bool
    {
        $currentType = AttendanceType::valueOf($this->attendance_type);

        return $currentType === AttendanceType::valueOf($attendanceType) || ! $this->hasLinkedAttendanceRecord();
    }

    /**
     * @throws InvalidSession
     */
    public function ensureCanChangeAttendanceTypeTo(AttendanceType|string $attendanceType): void
    {
        if (! $this->canChangeAttendanceTypeTo($attendanceType)) {
            throw new InvalidSession('Não é possível alterar o tipo de atendimento de um agendamento que já possui registro vinculado.');
        }
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'attendance_session_student',
            'attendance_session_id',
            'student_id',
        );
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function aeeRecord(): HasOne
    {
        return $this->hasOne(AeeRecord::class, 'attendance_session_id');
    }

    public function pedagogicalRecord(): HasOne
    {
        return $this->hasOne(PedagogicalRecord::class, 'attendance_session_id');
    }

    public function isScheduled(): bool
    {
        return SessionStatus::isScheduledValue($this->status);
    }

    /**
     * @return list<int>
     */
    private function studentIds(): array
    {
        return $this->students()->pluck('students.id')->map(fn (mixed $id): int => (int) $id)->all();
    }
}
