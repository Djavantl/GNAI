<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum PedagogicalFollowUpStatus: string
{
    case ONGOING = 'ongoing';
    case UNDER_OBSERVATION = 'under_observation';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::ONGOING => 'Em andamento',
            self::UNDER_OBSERVATION => 'Em observação',
            self::COMPLETED => 'Finalizado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ONGOING => 'primary',
            self::UNDER_OBSERVATION => 'warning',
            self::COMPLETED => 'success',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(static fn (self $status): array => [
                $status->value => $status->label(),
            ])
            ->all();
    }
}
