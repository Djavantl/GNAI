<?php

namespace App\Enums;

trait HasEnumHelper
{
    /**
     * Retorna opções no formato:
     * ['value' => 'Label']
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => method_exists($case, 'label')
                    ? $case->label()
                    : $case->name
            ])
            ->toArray();
    }

    /**
     * Retorna apenas os valores do enum
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Retorna apenas os labels
     */
    public static function labels(): array
    {
        return array_map(
            fn ($case) => method_exists($case, 'label')
                ? $case->label()
                : $case->name,
            self::cases()
        );
    }
}