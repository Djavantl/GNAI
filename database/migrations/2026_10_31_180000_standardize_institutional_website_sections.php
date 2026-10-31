<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Snapshot independente do conteúdo padrão que poderá mudar no futuro.
        $defaults = json_decode(<<<'JSON'
[
    {
        "id": "hero",
        "type": "hero",
        "enabled": true,
        "title": "Acessibilidade e inclusão na vida acadêmica",
        "subtitle": "Um espaço de escuta, orientação e articulação de apoios para a participação e a aprendizagem de estudantes com necessidades específicas.",
        "body": "",
        "button_label": "Conheça as formas de apoio",
        "button_url": "#apoio",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": []
    },
    {
        "id": "about",
        "type": "about",
        "enabled": true,
        "title": "Conheça o núcleo",
        "subtitle": "Uma instituição acessível se constrói com a participação de todos.",
        "body": "<p>O Núcleo de Acessibilidade e Inclusão articula ações para identificar e reduzir barreiras à participação e à aprendizagem na instituição. Seu trabalho reúne estudantes, docentes, famílias e diferentes setores em torno de uma educação inclusiva.</p><p>A partir da escuta das necessidades de cada estudante, o núcleo orienta a comunidade acadêmica e contribui para a organização dos apoios ao longo do percurso educacional.</p>",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": []
    },
    {
        "id": "pillars",
        "type": "pillars",
        "enabled": true,
        "title": "Princípios que orientam nossa atuação",
        "subtitle": "",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": [
            {
                "icon": "fa fa-heart-o",
                "title": "Acolhimento e escuta",
                "text": "Reconhecer as necessidades e experiências de cada pessoa, com respeito e diálogo.",
                "url": ""
            },
            {
                "icon": "fa fa-wheelchair",
                "title": "Acessibilidade",
                "text": "Identificar barreiras nos espaços, na comunicação, nos materiais e nas práticas educacionais.",
                "url": ""
            },
            {
                "icon": "fa fa-users",
                "title": "Autonomia e participação",
                "text": "Valorizar a voz do estudante e sua participação nas decisões sobre os apoios de que necessita.",
                "url": ""
            },
            {
                "icon": "fa fa-graduation-cap",
                "title": "Permanência e aprendizagem",
                "text": "Articular condições para que o estudante participe da vida acadêmica e desenvolva seu percurso formativo.",
                "url": ""
            }
        ]
    },
    {
        "id": "audience",
        "type": "audience",
        "enabled": true,
        "title": "Para quem é este espaço",
        "subtitle": "O estudante está no centro do trabalho, em diálogo com toda a comunidade acadêmica.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": [
            {
                "icon": "fa fa-graduation-cap",
                "title": "Estudantes",
                "text": "Quem encontra barreiras de acesso, participação ou aprendizagem e precisa de orientação sobre apoios educacionais.",
                "url": ""
            },
            {
                "icon": "fa fa-book",
                "title": "Docentes e servidores",
                "text": "Quem busca orientação para tornar práticas, materiais, comunicação e ambientes mais acessíveis.",
                "url": ""
            },
            {
                "icon": "fa fa-comments-o",
                "title": "Famílias e rede de apoio",
                "text": "Quem deseja dialogar com a instituição sobre as necessidades educacionais do estudante.",
                "url": ""
            }
        ]
    },
    {
        "id": "apoio",
        "type": "features",
        "enabled": true,
        "title": "Como o núcleo pode apoiar",
        "subtitle": "Os apoios são organizados conforme as necessidades identificadas e as condições de cada instituição.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": [
            {
                "icon": "fa fa-comments-o",
                "title": "Escuta e orientação",
                "text": "Compreender as dificuldades encontradas no cotidiano acadêmico e orientar sobre os caminhos de apoio.",
                "url": ""
            },
            {
                "icon": "fa fa-book",
                "title": "Apoio à aprendizagem",
                "text": "Dialogar com docentes e setores pedagógicos sobre estratégias e materiais que favoreçam a participação.",
                "url": ""
            },
            {
                "icon": "fa fa-wheelchair",
                "title": "Recursos de acessibilidade",
                "text": "Orientar sobre recursos e tecnologias assistivas e articular alternativas para reduzir barreiras.",
                "url": ""
            },
            {
                "icon": "fa fa-sitemap",
                "title": "Articulação institucional",
                "text": "Aproximar os setores envolvidos e a rede de apoio para acompanhar as necessidades educacionais.",
                "url": ""
            },
            {
                "icon": "fa fa-lightbulb-o",
                "title": "Formação e sensibilização",
                "text": "Contribuir com ações de orientação e reflexão sobre acessibilidade, respeito às diferenças e inclusão.",
                "url": ""
            }
        ]
    },
    {
        "id": "atendimento",
        "type": "guidance",
        "enabled": true,
        "title": "Como buscar apoio",
        "subtitle": "Você pode procurar orientação para entender quais caminhos estão disponíveis na instituição.",
        "body": "",
        "button_label": "Consultar canais de contato",
        "button_url": "#contato",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": [
            {
                "icon": "",
                "title": "Entre em contato",
                "text": "Procure o núcleo pelos canais institucionais ou peça orientação ao setor pedagógico de sua instituição.",
                "url": ""
            },
            {
                "icon": "",
                "title": "Compartilhe suas necessidades",
                "text": "Converse com a equipe sobre as barreiras encontradas e os apoios que podem contribuir para sua participação acadêmica.",
                "url": ""
            },
            {
                "icon": "",
                "title": "Conheça os encaminhamentos",
                "text": "A equipe orientará os próximos passos e a articulação com os setores responsáveis, conforme as normas institucionais.",
                "url": ""
            }
        ]
    },
    {
        "id": "publications",
        "type": "publications",
        "enabled": true,
        "title": "Informações e ações do núcleo",
        "subtitle": "Notícias, orientações e registros para a comunidade acadêmica.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": []
    },
    {
        "id": "events",
        "type": "events",
        "enabled": true,
        "title": "Atividades e encontros",
        "subtitle": "Acompanhe as próximas ações de formação e convivência.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": []
    },
    {
        "id": "team",
        "type": "team",
        "enabled": true,
        "title": "Equipe do núcleo",
        "subtitle": "Conheça as pessoas que participam desse trabalho.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 4,
        "items": []
    },
    {
        "id": "duvidas",
        "type": "faq",
        "enabled": true,
        "title": "Dúvidas frequentes",
        "subtitle": "",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": [
            {
                "icon": "",
                "title": "Quando posso procurar o núcleo?",
                "text": "Quando encontrar barreiras à participação ou à aprendizagem, ou precisar de orientação sobre acessibilidade no cotidiano acadêmico.",
                "url": ""
            },
            {
                "icon": "",
                "title": "Como sei quais apoios estão disponíveis?",
                "text": "Converse com a equipe. Os apoios e encaminhamentos dependem das necessidades identificadas e da organização de cada instituição.",
                "url": ""
            },
            {
                "icon": "",
                "title": "Docentes e familiares também podem buscar orientação?",
                "text": "Sim. O diálogo com docentes, servidores, familiares e a rede de apoio contribui para compreender e enfrentar as barreiras vivenciadas pelo estudante.",
                "url": ""
            },
            {
                "icon": "",
                "title": "Encontrei uma barreira de acessibilidade. O que fazer?",
                "text": "Informe ao núcleo ou ao setor responsável onde está a barreira e como ela afeta a participação. A equipe poderá orientar o encaminhamento institucional.",
                "url": ""
            }
        ]
    },
    {
        "id": "documentos",
        "type": "resources",
        "enabled": false,
        "title": "Orientações e documentos",
        "subtitle": "Consulte materiais, formulários e referências da instituição.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": []
    },
    {
        "id": "contato",
        "type": "contact",
        "enabled": true,
        "title": "Contato e localização",
        "subtitle": "Converse com a equipe sobre acessibilidade e apoio à participação acadêmica.",
        "body": "",
        "button_label": "",
        "button_url": "",
        "image_path": null,
        "image_alt": "",
        "limit": 3,
        "items": []
    }
]
JSON, true, 512, JSON_THROW_ON_ERROR);
        $templates = array_column($defaults, null, 'type');

        DB::transaction(function () use ($defaults, $templates): void {
            foreach (DB::table('website_settings')->orderBy('id')->get() as $setting) {
                $sections = json_decode($setting->sections ?? '[]', true, 512, JSON_THROW_ON_ERROR) ?? [];
                $legacyIds = ['hero', 'about', 'highlights', 'features', 'events', 'publications', 'team', 'contact'];
                $oldIdentity = $setting->site_name === 'NAPNE'
                    && $setting->tagline === 'Núcleo de Atendimento às Pessoas com Necessidades Específicas';
                $prototype = $oldIdentity
                    && array_column($sections, 'id') === $legacyIds
                    && array_column($sections[2]['items'] ?? [], 'value') === ['+100', '+50', '+20'];
                $updates = [];

                if ($prototype) {
                    // Substitui a home experimental. Preserva imagens enviadas.
                    $previous = array_column($sections, null, 'type');
                    $sections = array_map(function (array $section) use ($previous): array {
                        if (in_array($section['type'], ['hero', 'about'], true)) {
                            $old = $previous[$section['type']] ?? [];
                            $section['image_path'] = $old['image_path'] ?? null;
                            $section['image_alt'] = $old['image_alt'] ?? '';
                        }

                        return $section;
                    }, $defaults);
                } else {
                    // Sites personalizados mantêm ordem, conteúdo e visibilidade.
                    foreach ($sections as &$section) {
                        if (($section['type'] ?? null) === 'highlights') {
                            $section = [
                                ...$templates['pillars'],
                                'id' => $section['id'],
                                'enabled' => $section['enabled'] ?? true,
                            ];
                        } elseif (($section['type'] ?? null) === 'cta') {
                            $section['type'] = 'about';
                        }

                        foreach ($section['items'] ?? [] as $index => $item) {
                            unset($item['value']);
                            $section['items'][$index] = [...$item, 'url' => $item['url'] ?? ''];
                        }
                    }
                    unset($section);
                }

                if ($oldIdentity) {
                    $updates['site_name'] = 'NAI';
                    $updates['tagline'] = 'Núcleo de Acessibilidade e Inclusão';
                }

                DB::table('website_settings')->where('id', $setting->id)->update([
                    ...$updates,
                    'sections' => json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                ]);
            }
        });
    }

    public function down(): void
    {
        // A limpeza dos textos experimentais não deve recriar números fictícios.
        // O esquema e os dados institucionais permanecem compatíveis com a tabela.
    }
};
