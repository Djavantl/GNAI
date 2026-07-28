<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Sessions\CreateSessionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Sessions\UpdateSessionDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\AttendanceType;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\SessionType;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidSession;
use App\Models\SpecializedEducationalSupport\PedagogicalRecord;
use App\Models\SpecializedEducationalSupport\SessionRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Session extends Model
{
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
    ];

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
            'type' => $data->type,
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
            'type' => $data->type,
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

    public static function typeForAttendance(string $attendanceType, string $type): string
    {
        if (AttendanceType::isPedagogical($attendanceType)) {
            return SessionType::INDIVIDUAL->value;
        }

        return $type;
    }

    public static function acceptsStudentCountForAttendance(string $attendanceType, int $studentCount): bool
    {
        return ! AttendanceType::isPedagogical($attendanceType) || $studentCount === 1;
    }

    /**
     * @param list<int> $studentIds
     *
     * @throws InvalidSession
     */
    public static function ensureAttendanceTypeAcceptsStudents(string $attendanceType, array $studentIds): void
    {
        if (! self::acceptsStudentCountForAttendance($attendanceType, count($studentIds))) {
            throw new InvalidSession('Atendimentos pedagógicos devem possuir exatamente um aluno.');
        }
    }

    public function hasLinkedAttendanceRecord(): bool
    {
        return $this->sessionRecord()->exists() || $this->pedagogicalRecord()->exists();
    }

    public function canChangeAttendanceTypeTo(string $attendanceType): bool
    {
        $currentType = $this->attendance_type ?? AttendanceType::AEE->value;

        return $currentType === $attendanceType || ! $this->hasLinkedAttendanceRecord();
    }

    /**
     * @throws InvalidSession
     */
    public function ensureCanChangeAttendanceTypeTo(string $attendanceType): void
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

    public function sessionRecord(): HasOne
    {
        return $this->hasOne(SessionRecord::class, 'attendance_session_id');
    }

    public function pedagogicalRecord(): HasOne
    {
        return $this->hasOne(PedagogicalRecord::class, 'attendance_session_id');
    }

    public function isScheduled(): bool
    {
        return SessionStatus::isScheduledValue($this->status);
    }
}
