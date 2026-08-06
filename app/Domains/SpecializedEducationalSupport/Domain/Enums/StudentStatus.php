<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum StudentStatus: string
{
    case ACTIVE = 'active';
    case LOCKED = 'locked';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';
    case FULL_ATTENDANCE = 'full_attendance';
    case AEE_ONLY = 'aee_only';
    case PEDAGOGICAL_ONLY = 'pedagogical_only';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativo',
            self::LOCKED => 'Trancado',
            self::COMPLETED => 'Concluído',
            self::DROPPED => 'Desistente',
            self::FULL_ATTENDANCE => 'Atendimento Completo',
            self::AEE_ONLY => 'Atendimento Educacional Especializado',
            self::PEDAGOGICAL_ONLY => 'Atendimento Pedagógico',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::LOCKED => 'warning',
            self::COMPLETED => 'primary',
            self::DROPPED => 'danger',
            self::FULL_ATTENDANCE => 'success',
            self::AEE_ONLY => 'info',
            self::PEDAGOGICAL_ONLY => 'primary',
        };
    }

    public function isEnabled(): bool
    {
        return in_array($this, [
            self::ACTIVE,
            self::FULL_ATTENDANCE,
            self::AEE_ONLY,
            self::PEDAGOGICAL_ONLY,
        ], true);
    }

    public function allowsAttendanceType(AttendanceType|string $attendanceType): bool
    {
        return match ($this) {
            self::ACTIVE, self::FULL_ATTENDANCE => true,
            self::AEE_ONLY => AttendanceType::isAee($attendanceType),
            self::PEDAGOGICAL_ONLY => AttendanceType::isPedagogical($attendanceType),
            default => false,
        };
    }
}
