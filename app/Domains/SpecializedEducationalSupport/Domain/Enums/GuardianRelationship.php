<?php

declare(strict_types=1);
namespace App\Domains\SpecializedEducationalSupport\Domain\Enums;

enum GuardianRelationship: string
{
    case FATHER = 'father';
    case MOTHER = 'mother';
    case GRANDFATHER = 'grandfather';
    case GRANDMOTHER = 'grandmother';
    case GUARDIAN = 'guardian';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FATHER => 'Pai',
            self::MOTHER => 'Mãe',
            self::GRANDFATHER => 'Avô',
            self::GRANDMOTHER => 'Avó',
            self::GUARDIAN => 'Responsável Legal',
            self::OTHER => 'Outro',
        };
    }
}
