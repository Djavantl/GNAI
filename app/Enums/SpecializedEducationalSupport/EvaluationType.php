<?php

namespace App\Enums\SpecializedEducationalSupport;
use App\Enums\HasEnumHelper;

enum EvaluationType: string
{
    case PROGRESS = 'progress';
    case FINAL = 'final';

    public function label(): string
    {
        return match($this) {
            self::PROGRESS => 'Avaliação de progresso',
            self::FINAL => 'Avaliação final',
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