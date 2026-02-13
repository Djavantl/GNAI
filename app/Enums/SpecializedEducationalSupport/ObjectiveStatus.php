<?php

namespace App\Enums\SpecializedEducationalSupport;

enum ObjectiveStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case ACHIEVED = 'achieved'; 
    case NOT_ACHIEVED = 'not_achieved';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::IN_PROGRESS => 'Em Andamento',
            self::ACHIEVED => 'Alcançado',
            self::NOT_ACHIEVED => 'Não Alcançado',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function labels(): array
    {
        $map = [];
        foreach (self::cases() as $case) {
            $map[$case->value] = $case->label();
        }
        return $map;
    }
}