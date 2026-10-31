<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Enums;

/**
 * Tipos de publicação do site. Os registros da antiga agenda institucional
 * permanecem como "event".
 */
enum InstitutionalEventType: string
{
    case NEWS = 'news';
    case EVENT = 'event';
    case ANNOUNCEMENT = 'announcement';
    case ARTICLE = 'article';
    case RECORD = 'record';

    public function label(): string
    {
        return match ($this) {
            self::NEWS => 'Notícia',
            self::EVENT => 'Evento',
            self::ANNOUNCEMENT => 'Comunicado',
            self::ARTICLE => 'Artigo',
            self::RECORD => 'Registro',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
