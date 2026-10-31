<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Enums;

/**
 * Blocos disponíveis no construtor da página inicial do site.
 */
enum WebsiteSectionType: string
{
    case HERO = 'hero';
    case ABOUT = 'about';
    case HIGHLIGHTS = 'highlights';
    case FEATURES = 'features';
    case TEAM = 'team';
    case EVENTS = 'events';
    case PUBLICATIONS = 'publications';
    case CTA = 'cta';
    case CONTACT = 'contact';

    public function label(): string
    {
        return match ($this) {
            self::HERO => 'Banner principal',
            self::ABOUT => 'Sobre nós',
            self::HIGHLIGHTS => 'Números em destaque',
            self::FEATURES => 'O que fazemos',
            self::TEAM => 'Equipe',
            self::EVENTS => 'Próximos eventos',
            self::PUBLICATIONS => 'Últimas publicações',
            self::CTA => 'Chamada para ação',
            self::CONTACT => 'Contato',
        };
    }

    public function hasItems(): bool
    {
        return $this === self::HIGHLIGHTS || $this === self::FEATURES;
    }

    public function hasImage(): bool
    {
        return $this === self::HERO || $this === self::ABOUT;
    }

    public function hasRichBody(): bool
    {
        return $this === self::ABOUT;
    }

    public function hasLimit(): bool
    {
        return in_array($this, [self::TEAM, self::EVENTS, self::PUBLICATIONS], true);
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $type): array => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
