<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/**
 * Conteúdo inicial do site enquanto a retaguarda ainda não salvou a
 * configuração. Todos os textos podem ser alterados no construtor.
 */
final class WebsiteDefaults
{
    public const PRIMARY_COLOR = '#322b75';

    /**
     * @return array<string, mixed>
     */
    public static function identity(): array
    {
        return [
            'site_name' => 'NAPNE',
            'tagline' => 'Núcleo de Atendimento às Pessoas com Necessidades Específicas',
            'logo_path' => null,
            'primary_color' => self::PRIMARY_COLOR,
            'contact_email' => null,
            'contact_phone' => null,
            'address' => null,
            'social_links' => [
                'instagram' => null,
                'facebook' => null,
                'youtube' => null,
                'website' => null,
            ],
            'footer_text' => 'Promovendo acessibilidade, inclusão e permanência de todas as pessoas na instituição.',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function sections(): array
    {
        return [
            self::section('hero', 'hero', [
                'title' => 'Inclusão que transforma a vida acadêmica',
                'subtitle' => 'Apoiamos estudantes, servidores e a comunidade na construção de uma instituição acessível para todas as pessoas.',
                'button_label' => 'Conheça a equipe',
                'button_url' => '/site/equipe',
            ]),
            self::section('about', 'about', [
                'title' => 'Quem somos',
                'body' => '<p>O núcleo atua no acompanhamento de estudantes com necessidades específicas, na promoção de ações de acessibilidade e na articulação com professores, famílias e setores da instituição.</p>',
            ]),
            self::section('highlights', 'highlights', [
                'title' => 'Nossa atuação em números',
                'items' => [
                    ['icon' => '', 'value' => '+100', 'title' => 'Estudantes acompanhados', 'text' => ''],
                    ['icon' => '', 'value' => '+50', 'title' => 'Ações realizadas', 'text' => ''],
                    ['icon' => '', 'value' => '+20', 'title' => 'Recursos de acessibilidade', 'text' => ''],
                ],
            ]),
            self::section('features', 'features', [
                'title' => 'O que fazemos',
                'subtitle' => 'Frentes de trabalho do núcleo.',
                'items' => [
                    ['icon' => 'fa fa-users', 'value' => '', 'title' => 'Atendimento especializado', 'text' => 'Acompanhamento individual e planos educacionais personalizados.'],
                    ['icon' => 'fa fa-universal-access', 'value' => '', 'title' => 'Acessibilidade', 'text' => 'Identificação de barreiras e oferta de tecnologias assistivas.'],
                    ['icon' => 'fa fa-graduation-cap', 'value' => '', 'title' => 'Formação', 'text' => 'Palestras, oficinas e orientações para a comunidade.'],
                ],
            ]),
            self::section('events', 'events', [
                'title' => 'Próximos eventos',
                'subtitle' => 'Acompanhe o que vem por aí.',
                'limit' => 3,
            ]),
            self::section('publications', 'publications', [
                'title' => 'Publicações',
                'subtitle' => 'Notícias, comunicados e registros do núcleo.',
                'limit' => 3,
            ]),
            self::section('team', 'team', [
                'title' => 'Nossa equipe',
                'subtitle' => 'Quem faz o núcleo acontecer.',
                'limit' => 4,
            ]),
            self::section('contact', 'contact', [
                'title' => 'Fale conosco',
                'subtitle' => 'Estamos à disposição para orientar e acolher.',
            ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public static function section(string $id, string $type, array $values = []): array
    {
        return [
            'id' => $id,
            'type' => $type,
            'enabled' => true,
            'title' => '',
            'subtitle' => '',
            'body' => '',
            'button_label' => '',
            'button_url' => '',
            'image_path' => null,
            'image_alt' => '',
            'limit' => 3,
            'items' => [],
            ...$values,
        ];
    }
}
