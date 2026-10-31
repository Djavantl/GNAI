<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Enums;

enum ItemType: string
{
    case AssistiveTechnology = 'assistive_technology';
    case AccessibleEducationalMaterial = 'accessible_educational_material';

    public function label(): string
    {
        return match ($this) {
            self::AssistiveTechnology => 'Tecnologia Assistiva',
            self::AccessibleEducationalMaterial => 'Material Pedagógico Acessível',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::AssistiveTechnology => 'fa-microchip',
            self::AccessibleEducationalMaterial => 'fa-book',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(static fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }
}
