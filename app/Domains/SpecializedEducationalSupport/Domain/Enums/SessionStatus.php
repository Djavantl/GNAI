<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum SessionStatus: string
{
    public const SCHEDULED_DATABASE_VALUE = 'Agendada';
    public const COMPLETED_DATABASE_VALUE = 'Realizada';
    public const CANCELLED_DATABASE_VALUE = 'Cancelada';

    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => self::SCHEDULED_DATABASE_VALUE,
            self::COMPLETED => self::COMPLETED_DATABASE_VALUE,
            self::CANCELLED => self::CANCELLED_DATABASE_VALUE,
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

    public static function labelFor(?string $status): string
    {
        return self::fromDatabaseValue($status)?->label() ?? (string) $status;
    }

    public static function colorFor(?string $status): string
    {
        return self::fromDatabaseValue($status)?->color() ?? 'warning';
    }

    public static function isScheduledValue(?string $status): bool
    {
        return self::fromDatabaseValue($status)?->isScheduled() ?? false;
    }

    public static function options(): array
    {
        return [
            self::SCHEDULED_DATABASE_VALUE => self::SCHEDULED->label(),
            self::COMPLETED_DATABASE_VALUE => self::COMPLETED->label(),
            self::CANCELLED_DATABASE_VALUE => self::CANCELLED->label(),
        ];
    }

    public static function fromDatabaseValue(?string $status): ?self
    {
        return match (mb_strtolower(trim((string) $status))) {
            'agendada', 'agendado', 'scheduled', 'pending' => self::SCHEDULED,
            'realizada', 'realizado', 'completed', 'confirmed' => self::COMPLETED,
            'cancelada', 'cancelado', 'cancelled', 'canceled' => self::CANCELLED,
            default => null,
        };
    }
}
