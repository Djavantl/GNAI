# Home institucional do site público

Auditoria e adaptação realizadas em 31/10/2026.

## Finalidade

Apresentar o núcleo, explicar a quem se destina, orientar a busca de apoio e divulgar suas ações. A home não é uma página de vendas nem uma vitrine de indicadores. O padrão inicial usa **NAI, Núcleo de Acessibilidade e Inclusão**, com nome, descrição e identidade editáveis para outras denominações institucionais, incluindo NAPNE.

A inspeção do site local encontrou uma home experimental salva em `website_settings.sections`, com contadores fictícios de estudantes, ações e recursos. Não existe uma tabela por tipo de seção. A limpeza acontece no JSON de configuração.

## Referências consultadas

- [IF Baiano, apresentação e finalidades do NAPNE](https://ifbaiano.edu.br/portal/ensino/diversidade-e-inclusao/napne-nucleo-de-atendimento-a-pessoas-com-necessidades-especificas/): apresentação institucional, articulação de apoios, orientação à comunidade e contatos.
- [IFSP, apresentação do NAPNE](https://ifsp.edu.br/component/content/article/1472): eliminação de barreiras, acompanhamento educacional e diálogo entre setores.
- [IFSP São Paulo, documentos do NAPNE](https://spo.ifsp.edu.br/menu/3146-napne-documentos): importância de disponibilizar documentos e formulários institucionais.
- [Ifes São Mateus, apresentação do Napne](https://saomateus.ifes.edu.br/institucional-sm/1447-napne-nucleo-de-atendimento-as-pessoas-com-necessidades-especificas): participação, aprendizagem e percurso educacional.
- [UFR, Núcleo de Acessibilidade e Inclusão](https://ufr.edu.br/prae/nai/): referência da denominação NAI, com apresentação, atendimento, equipe, contatos e orientações.

A estrutura abaixo é uma decisão de composição para o Gnai, informada por essas referências. Os textos iniciais são próprios e genéricos. Regulamentos, serviços disponíveis, público formalmente elegível, prazos e documentação exigida dependem de cada instituição e não são presumidos pelo sistema.

## Estrutura do motor

| Tipo | Papel na home | Configuração específica |
| --- | --- | --- |
| `hero` | Apresentação inicial | Título, texto de apoio, imagem opcional e link de orientação |
| `about` | Conheça o núcleo | Texto rico, imagem com alternativa e link opcional |
| `pillars` | Princípios de atuação | Cards com ícone, título e descrição |
| `audience` | Para quem é este espaço | Cards de estudantes, comunidade e rede de apoio |
| `features` | Formas de apoio | Cards com ícone, título e descrição |
| `guidance` | Como buscar apoio | Passos ordenados, descrições e link opcional |
| `publications` | Informações e ações | Publicações reais, com limite configurável |
| `events` | Atividades e encontros | Eventos reais, com limite configurável |
| `team` | Equipe do núcleo | Profissionais ativos autorizados a aparecer no site |
| `faq` | Dúvidas frequentes | Perguntas e respostas em controles nativos de expansão |
| `resources` | Orientações e documentos | Cards com título, descrição e link para materiais reais |
| `contact` | Contato e localização | Canais e localização definidos na identidade |

Todos os blocos mantêm ordenação, visibilidade, inclusão e remoção pelo construtor. Os modelos de novas seções vêm de `WebsiteDefaults`, a mesma fonte do site sem configuração. Não há uma segunda home fixa em React.

A seção de documentos começa desativada e sem materiais fictícios. Equipe, eventos e publicações sem registros, assim como listas de itens vazias, não aparecem na home pública. Na prévia do construtor, os blocos habilitados continuam disponíveis para edição, mesmo vazios.

Os links iniciais usam `#apoio` e `#contato`. Cada seção expõe sua própria âncora no editor. Ao excluir uma seção ou alterar a composição, o responsável pode ajustar os links nos campos do construtor.

A identidade do núcleo, contatos e redes sociais continuam compartilhados pelo cabeçalho, rodapé e páginas públicas. A página de equipe, a listagem e os detalhes das publicações conservam suas rotas e contratos. Não foi criado um formulário público de solicitação de atendimento.

## Limpeza das configurações anteriores

A migration `2026_10_31_180000_standardize_institutional_website_sections.php`:

- Reconhece a home experimental pelo nome e descrição originais, pela sequência exata das oito seções antigas e pelos três valores fictícios.
- Substitui essa composição pelo modelo institucional, preservando imagens das seções de apresentação e sobre o núcleo.
- Atualiza apenas a identidade que ainda corresponde ao padrão NAPNE antigo.
- Em composições personalizadas, converte `highlights` em pilares e `cta` em uma seção informativa, preservando texto, link e ordem desta última.
- Retira o campo numérico `value` dos itens. Identidade personalizada, contatos, publicações, equipe e arquivos não são excluídos.
- Não recria conteúdo experimental em rollback. A limpeza dos textos é uma transformação de dados sem restauração automática.

## Escopo da conferência

Foram inspecionados os contratos do motor, validação, normalização, armazenamento, prévia, renderização e conteúdo retornado pelas quatro rotas públicas locais. A migration específica foi aplicada ao ambiente local existente e as respostas HTTP confirmaram a identidade NAI e os novos tipos de seção.

Não foram iniciados servidores, executados build, typecheck ou suítes de testes. Não houve homologação visual em navegador nem submissão autenticada do construtor.
