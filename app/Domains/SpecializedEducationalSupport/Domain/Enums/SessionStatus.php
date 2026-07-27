<?php

declare(strict_types=1);

namespace app\Domains\SpecializedEducationalSupport\Domain\Enums;

enum SessionStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Agendada',
            self::COMPLETED => 'Realizada',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SCHEDULED => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function isScheduled(): bool
    {
        return $this === self::SCHEDULED;
    }
}
