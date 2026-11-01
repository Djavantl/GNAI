<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Support;

/**
 * Conteúdo institucional inicial, editável no construtor.
 * Não pressupõe equipes, recursos, contatos ou regras de atendimento locais.
 */
final class WebsiteDefaults
{
    public const PRIMARY_COLOR = '#322b75';

    /** @return array<string, mixed> */
    public static function identity(): array
    {
        return [
            'site_name' => 'NAI',
            'tagline' => 'Núcleo de Acessibilidade e Inclusão',
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
            'footer_text' => 'Acolhimento, acessibilidade e participação na vida acadêmica.',
        ];
    }

    /** @return list<array<string, mixed>> */
    public static function sections(): array
    {
        return [
            self::section('hero', 'hero', [
                'title' => 'Acessibilidade e inclusão na vida acadêmica',
                'subtitle' => 'Um espaço de escuta, orientação e articulação de apoios para a participação e a aprendizagem de estudantes com necessidades específicas.',
                'button_label' => 'Conheça as formas de apoio',
                'button_url' => '#apoio',
            ]),
            self::section('about', 'about', [
                'title' => 'Conheça o núcleo',
                'subtitle' => 'Uma instituição acessível se constrói com a participação de todos.',
                'body' => '<p>O Núcleo de Acessibilidade e Inclusão articula ações para identificar e reduzir barreiras à participação e à aprendizagem na instituição. Seu trabalho reúne estudantes, docentes, famílias e diferentes setores em torno de uma educação inclusiva.</p><p>A partir da escuta das necessidades de cada estudante, o núcleo orienta a comunidade acadêmica e contribui para a organização dos apoios ao longo do percurso educacional.</p>',
            ]),
            self::section('pillars', 'pillars', [
                'title' => 'Princípios que orientam nossa atuação',
                'items' => [
                    self::item('fa fa-heart-o', 'Acolhimento e escuta', 'Reconhecer as necessidades e experiências de cada pessoa, com respeito e diálogo.'),
                    self::item('fa fa-wheelchair', 'Acessibilidade', 'Identificar barreiras nos espaços, na comunicação, nos materiais e nas práticas educacionais.'),
                    self::item('fa fa-users', 'Autonomia e participação', 'Valorizar a voz do estudante e sua participação nas decisões sobre os apoios de que necessita.'),
                    self::item('fa fa-graduation-cap', 'Permanência e aprendizagem', 'Articular condições para que o estudante participe da vida acadêmica e desenvolva seu percurso formativo.'),
                ],
            ]),
            self::section('audience', 'audience', [
                'title' => 'Para quem é este espaço',
                'subtitle' => 'O estudante está no centro do trabalho, em diálogo com toda a comunidade acadêmica.',
                'items' => [
                    self::item('fa fa-graduation-cap', 'Estudantes', 'Quem encontra barreiras de acesso, participação ou aprendizagem e precisa de orientação sobre apoios educacionais.'),
                    self::item('fa fa-book', 'Docentes e servidores', 'Quem busca orientação para tornar práticas, materiais, comunicação e ambientes mais acessíveis.'),
                    self::item('fa fa-comments-o', 'Famílias e rede de apoio', 'Quem deseja dialogar com a instituição sobre as necessidades educacionais do estudante.'),
                ],
            ]),
            self::section('apoio', 'features', [
                'title' => 'Como o núcleo pode apoiar',
                'subtitle' => 'Os apoios são organizados conforme as necessidades identificadas e as condições de cada instituição.',
                'items' => [
                    self::item('fa fa-comments-o', 'Escuta e orientação', 'Compreender as dificuldades encontradas no cotidiano acadêmico e orientar sobre os caminhos de apoio.'),
                    self::item('fa fa-book', 'Apoio à aprendizagem', 'Dialogar com docentes e setores pedagógicos sobre estratégias e materiais que favoreçam a participação.'),
                    self::item('fa fa-wheelchair', 'Recursos de acessibilidade', 'Orientar sobre recursos e tecnologias assistivas e articular alternativas para reduzir barreiras.'),
                    self::item('fa fa-sitemap', 'Articulação institucional', 'Aproximar os setores envolvidos e a rede de apoio para acompanhar as necessidades educacionais.'),
                    self::item('fa fa-lightbulb-o', 'Formação e sensibilização', 'Contribuir com ações de orientação e reflexão sobre acessibilidade, respeito às diferenças e inclusão.'),
                ],
            ]),
            self::section('atendimento', 'guidance', [
                'title' => 'Como buscar apoio',
                'subtitle' => 'Você pode procurar orientação para entender quais caminhos estão disponíveis na instituição.',
                'items' => [
                    self::item('', 'Entre em contato', 'Procure o núcleo pelos canais institucionais ou peça orientação ao setor pedagógico de sua instituição.'),
                    self::item('', 'Compartilhe suas necessidades', 'Converse com a equipe sobre as barreiras encontradas e os apoios que podem contribuir para sua participação acadêmica.'),
                    self::item('', 'Conheça os encaminhamentos', 'A equipe orientará os próximos passos e a articulação com os setores responsáveis, conforme as normas institucionais.'),
                ],
                'button_label' => 'Consultar canais de contato',
                'button_url' => '#contato',
            ]),
            self::section('publications', 'publications', [
                'title' => 'Informações e ações do núcleo',
                'subtitle' => 'Notícias, orientações e registros para a comunidade acadêmica.',
                'limit' => 3,
            ]),
            self::section('events', 'events', [
                'title' => 'Atividades e encontros',
                'subtitle' => 'Acompanhe as próximas ações de formação e convivência.',
                'limit' => 3,
            ]),
            self::section('team', 'team', [
                'title' => 'Equipe do núcleo',
                'subtitle' => 'Conheça as pessoas que participam desse trabalho.',
                'limit' => 4,
            ]),
            self::section('duvidas', 'faq', [
                'title' => 'Dúvidas frequentes',
                'items' => [
                    self::item('', 'Quando posso procurar o núcleo?', 'Quando encontrar barreiras à participação ou à aprendizagem, ou precisar de orientação sobre acessibilidade no cotidiano acadêmico.'),
                    self::item('', 'Como sei quais apoios estão disponíveis?', 'Converse com a equipe. Os apoios e encaminhamentos dependem das necessidades identificadas e da organização de cada instituição.'),
                    self::item('', 'Docentes e familiares também podem buscar orientação?', 'Sim. O diálogo com docentes, servidores, familiares e a rede de apoio contribui para compreender e enfrentar as barreiras vivenciadas pelo estudante.'),
                    self::item('', 'Encontrei uma barreira de acessibilidade. O que fazer?', 'Informe ao núcleo ou ao setor responsável onde está a barreira e como ela afeta a participação. A equipe poderá orientar o encaminhamento institucional.'),
                ],
            ]),
            self::section('documentos', 'resources', [
                'enabled' => false,
                'title' => 'Orientações e documentos',
                'subtitle' => 'Consulte materiais, formulários e referências da instituição.',
            ]),
            self::section('contato', 'contact', [
                'title' => 'Contato e localização',
                'subtitle' => 'Converse com a equipe sobre acessibilidade e apoio à participação acadêmica.',
            ]),
        ];
    }

    /** @return array<string, string> */
    private static function item(string $icon, string $title, string $text): array
    {
        return ['icon' => $icon, 'title' => $title, 'text' => $text, 'url' => ''];
    }

    /**
     * @param array<string, mixed> $values
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
            'button_label' => match ($type) {
                'publications' => 'Ver todas as publicações',
                'events' => 'Ver todos os eventos',
                'team' => 'Ver equipe completa',
                default => '',
            },
            'button_url' => match ($type) {
                'publications' => '/site/publicacoes',
                'events' => '/site/publicacoes?type=event',
                'team' => '/site/equipe',
                default => '',
            },
            'image_path' => null,
            'image_alt' => '',
            'limit' => 3,
            'items' => [],
            ...($type === 'publications' ? ['publications' => PublicationsAppearance::normalize((array) ($values['publications'] ?? []))] : []),
            ...($type === 'events' ? ['events' => PublicationsAppearance::normalize((array) ($values['events'] ?? []))] : []),
            ...($type === 'team' ? ['team' => TeamAppearance::normalize((array) ($values['team'] ?? []))] : []),
            ...($type === 'contact' ? ['contact' => ContactAppearance::normalize((array) ($values['contact'] ?? []))] : []),
            ...($type === 'about' ? ['about' => AboutAppearance::defaults()] : []),
            ...($type === 'hero' ? ['hero' => HeroAppearance::defaults(), 'slides' => []] : []),
            ...$values,
            ...($type === 'guidance' ? [
                'guidance' => GuidanceAppearance::normalize((array) ($values['guidance'] ?? [])),
                'items' => array_map(fn (array $item): array => [
                    ...$item,
                    'appearance' => PillarCardAppearance::normalize((array) ($item['appearance'] ?? []), 'guidance'),
                ], $values['items'] ?? []),
            ] : []),
            ...(in_array($type, PillarsAppearance::SECTION_TYPES, true) ? [
                'pillars' => PillarsAppearance::normalize((array) ($values['pillars'] ?? []), $type),
                'items' => array_map(fn (array $item): array => [
                    ...$item,
                    'appearance' => PillarCardAppearance::normalize((array) ($item['appearance'] ?? [])),
                ], $values['items'] ?? []),
            ] : []),
        ];
    }
}
