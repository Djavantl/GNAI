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

## Personalização do banner principal

O tipo `hero` também aceita `hero` (aparência) e `slides` (imagens ordenadas), no mesmo JSON de seções. Não há nova tabela nem migration. `HeroAppearance` centraliza os valores iniciais, as opções aceitas e os limites usados pela validação do backend. Configurações antigas recebem esses valores pelo presenter e continuam utilizando sua imagem de fundo.

No modo **Textos sobre o fundo**, o editor permite:

- Alinhamento independente de título, texto de apoio, identificação e botão.
- Cores do fundo, textos, identificação e botão; campos vazios restauram o padrão.
- Tamanho e largura máxima dos textos, com adaptação para telas menores.
- Texto próprio ou nome do site na identificação, exibição opcional e arredondamento.
- Arredondamento e sombra do botão, mantendo os campos de texto e link existentes.
- Sobreposição de cor sobre a imagem, intensidade, degradê opcional, direção, cor final e intensidade final.

No modo **Somente banners com links**, as imagens são apresentadas sem sobreposição textual nem sombreamento. Cada item possui descrição acessível e link opcional, inclusive para endereços HTTPS do WhatsApp. O título da seção continua identificando a página para leitores de tela. A opção de mostrar a imagem inteira evita recorte do texto incorporado ao banner.

Os dois modos permitem até oito imagens JPG, PNG ou WEBP de até 4 MB cada, com inclusão, substituição, remoção e ordenação. Duas ou mais imagens formam um carrossel. Sem itens na lista, a imagem única anterior continua sendo usada. O modo de banners exige uma imagem e uma descrição.

Há ajustes de altura, enquadramento, posição, reprodução automática e intervalo entre imagens. O carrossel reutiliza `UI/BootstrapCarousel`, com controles acessíveis, pausa manual, pausa durante foco/hover e respeito à preferência de movimento reduzido. Os sliders reutilizam `Forms/RangeSlider` com `live`, sem alterar o comportamento padrão das demais telas.

A prévia utiliza URLs temporárias dos uploads, liberadas ao trocar arquivos ou desmontar. No salvamento, caminhos enviados pelo navegador não são aceitos para adotar arquivos existentes. Cada imagem mantida precisa pertencer ao mesmo slide da mesma seção salva. Substituições e exclusões limpam os arquivos antigos depois da persistência; falhas limpam somente os arquivos novos. Erros permanecem no formulário, com o transporte global de feedback já existente.

A Action de atualização mantém apenas `execute()`, coordenando o salvamento. As validações de configuração e imagens ficam em `UpdateWebsiteData`, incluindo `withValidator()`; o armazenamento em lote, a descoberta de caminhos e a limpeza de arquivos ficam em `WebsiteMediaStorage`, seguindo as referências de validação de barreiras e armazenamento de evidências da `main`.

Esta ampliação recebeu revisão de código e de diff. Não foram executados build, typecheck, testes, upload real ou homologação em navegador.

## Usabilidade da edição e prévia

Editor e prévia pertencem ao mesmo card, com ações no rodapé compartilhado. No desktop, a prévia acompanha a rolagem com posicionamento sticky dentro do card, mantendo altura limitada e rolagem própria do site. Em telas menores, os painéis ficam empilhados e a prévia acompanha o fluxo normal.

As grades do editor respondem à largura do próprio painel, passando para uma coluna quando o espaço é reduzido. Identificação, switches e controles de cor mantêm espaço para seus textos e ações sem sobreposição.

A conferência desta alteração foi feita na sessão autenticada existente do Brave, percorrendo o formulário até os ajustes da identificação e alterando seu arredondamento na prévia. A configuração usada na conferência não foi salva. Build e testes automatizados não foram executados.


O editor do banner agrupa os campos na ordem visual dos elementos: identificação, título, texto de apoio, botão, apresentação/fundo e carrossel. Cada grupo reúne seu conteúdo e os respectivos ajustes de posição, tamanho, cores e acabamento. Os toggles usam `size="small"`, como nos formulários integrados. Os IDs dos campos permanecem os mesmos para preservar o foco pelo clique duplo na prévia e a associação dos erros.

## Personalização de Conheça o núcleo

O tipo `about` aceita o objeto `about` de aparência no JSON já existente. `AboutAppearance` fornece os valores iniciais e os limites da validação. Não há migration; o presenter completa as configurações antigas e o salvamento mantém a identidade e a imagem da seção.

O formulário reúne conteúdo e aparência em grupos de título, texto de apoio, texto principal, imagem, botão e fundo. Título e subtítulo têm alinhamento, tamanho e cor; o título também permite ajustar ou ocultar sua linha decorativa. O texto principal preserva o editor rico e a sanitização, acrescentando cor, tamanho, alinhamento e espaçamento entre linhas. A formatação explícita feita dentro do editor rico continua preservada.

A imagem mantém upload, prévia, remoção e descrição acessível, com opção de exibição, posição ao lado/acima/abaixo do texto, largura da coluna, altura, enquadramento, foco, arredondamento e sombra. Em telas menores, as colunas se reorganizam verticalmente. O botão reúne texto, link, posição, cores, arredondamento e sombra. A seção permite ajustar fundo, degradê, espaçamento vertical e distância entre imagem e texto.

Os seletores de cor, sliders e seleções são compartilhados com a edição do banner principal. A prévia e o site público usam o mesmo componente de apresentação, e os IDs existentes são mantidos para o clique duplo e os erros por campo. Os ajustes desta ampliação foram revisados em código e no diff, sem executar build, testes ou homologação no navegador.

## Personalização de Princípios de atuação

O tipo `pillars` aceita a aparência da seção em `pillars` e a aparência de cada card em `items[*].appearance`. `PillarsAppearance` e `PillarCardAppearance` centralizam valores iniciais, opções e limites usados pela validação e pela normalização. A Action de salvamento não ganhou métodos auxiliares privados. A tabela e os contratos de título, texto, ícone, ordem e visibilidade são preservados; configurações antigas recebem os padrões pelo presenter, sem migration.

A seção permite configurar título e texto de apoio com alinhamento, tamanho e cor, além da linha decorativa. A organização pode ser em grade de uma a quatro colunas ou lista, com distância entre cards, espaçamento vertical, fundo e degradê com direção e cor final.

Cada item tem controles próprios, agrupados junto do conteúdo:

- Ícone: biblioteca, desenho, posição, alinhamento, cor, cor do fundo, tamanho do desenho, largura e altura do fundo, arredondamento e distância até o texto.
- Título e descrição: conteúdo, alinhamento, tamanhos e cores, com espaçamento entre linhas na descrição.
- Card: largura na coluna, altura mínima, espaçamento interno, fundo, cor e espessura da borda, arredondamento e sombra.
- Degradê próprio e efeito opcional ao passar o ponteiro, com cores, elevação e sombra.

As colunas se adaptam em telas menores e os textos podem aumentar a altura do card sem corte. O movimento de hover respeita a preferência de movimento reduzido. A prévia simula esse hover por geometria, preservando sua navegação inerte.

Os itens do editor podem ser expandidos individualmente. Seus controles são montados ao abrir, evitando inicializar sliders em áreas ocultas. O clique duplo na prévia e os erros abrem o item correspondente; adicionar um item abre sua edição. Chaves locais preservam o item durante reordenações e são removidas do envio ao servidor. O botão de adicionar segue o mesmo padrão de participantes e imagens.

O catálogo oferece **2.742 ícones** presentes no CSS e nas fontes locais: Font Awesome (668), Material Design (744), Ionicons (593), Themify (350), Simple Line (181) e Weather (206). A seleção tem filtro por biblioteca e pesquisa pelo nome, mantendo o desenho dos ícones na grade. As opções são filtradas pelo CSS ativo, sem fontes externas, e o seletor também é reutilizado pelos outros itens do construtor.

A revisão incluiu código, diff, uma conferência visual no Brave existente e leitura das props públicas. Os quatro itens salvos permaneceram preservados. Não houve envio de configuração durante a conferência, nem execução de build ou testes automatizados.

## Público e formas de apoio

As seções `audience` (Para quem é este espaço) e `features` (Como o núcleo pode apoiar) utilizam os mesmos controles, validação e apresentação de cards de `pillars`. O objeto `pillars` é mantido como chave do contrato compartilhado, e cada item conserva sua aparência em `items[*].appearance`.

As três seções permitem título e texto de apoio configuráveis, grade ou lista, espaçamentos, fundo e degradê. Cada card tem escolhas próprias de ícone, cores, tamanhos, alinhamentos, arredondamento, borda, sombra e hover. Inclusão, remoção, reordenação, pesquisa de ícones, expansão dos itens, foco por clique duplo e prévia reutilizam o fluxo já padronizado.

Os valores iniciais mantêm fundo branco para público e apoio, três colunas para público e quatro para apoio, com adaptação responsiva. Personalizações existentes de princípios são preservadas. A mudança não modifica os textos nem a quantidade dos itens cadastrados e não exige migration. A conferência desta ampliação foi de código e diff, sem executar build, testes ou navegador.


## Como buscar apoio

A seção `guidance` guarda a aparência geral em `guidance` e reutiliza `items[*].appearance` para cada etapa. Os números acompanham automaticamente a ordem dos itens. O editor permite adicionar, remover e reordenar etapas, com configurações de formato, tamanho, cores, posição do número, textos, card, degradê e hover agrupadas junto do conteúdo.

A exibição pode ser em grade, lista vertical, linha do tempo ou carrossel. A linha do tempo conecta os números e permite escolher a cor da linha. O carrossel reutiliza o componente compartilhado, com controles, indicadores, avanço automático opcional, intervalo e pausa. O movimento respeita a preferência de movimento reduzido.

A ação aceita apresentação de link ou botão, com alinhamento, tamanho do texto, cores, arredondamento, sombra, seta e hover. O destino utiliza um seletor compartilhado também pela apresentação e pela seção sobre o núcleo: lista as seções visíveis pelos seus títulos e as páginas de equipe e publicações. Outro endereço permite informar links externos, telefone ou e-mail. Âncoras são montadas pelo editor; o operador não precisa digitá-las. Destinos antigos indisponíveis são preservados e identificados no seletor.

As configurações antigas recebem os valores iniciais no presenter, preservando as etapas e o link cadastrados, sem migration. Os cards de princípios, público e formas de apoio compartilham a renderização com as etapas. A conferência desta frente foi de código e diff, sem executar build, testes ou navegador.


## Últimas publicações

A seção `publications` guarda sua aparência no objeto `publications` do JSON existente, sem migration. `PublicationsAppearance` centraliza padrões, limites, validação e normalização. Configurações antigas recebem os padrões pelo presenter e preservam título, texto de apoio e quantidade.

O editor reutiliza os grupos das demais seções: título e linha decorativa, texto de apoio, publicações, ação e organização/fundo. Permite configurar cores, tamanhos, alinhamento, fundo e degradê, espaçamentos, colunas, lista ou carrossel com avanço opcional e pausa. Os cards compartilham a aparência definida para a seção, com capa, resumo, data e marcadores opcionais, altura da capa, tamanhos de texto, cores, borda, arredondamento e padding. O conteúdo continua vindo dos cadastros reais; não há edição de notícias dentro do construtor.

A ação mantém o destino inicial da listagem de publicações e permite escolher outro destino pelo seletor compartilhado, texto, apresentação como link ou botão, cores, alinhamento, sombra, arredondamento e hover. Os cards reutilizam `PublicationCard`; os ajustes se aplicam somente à home. A prévia usa o mesmo renderer público, permite foco dos campos por clique duplo e mantém a seção vazia editável. O site público continua ocultando a seção sem registros e priorizando destaques antes das publicações recentes.

Conferência de código e diff, sem build, typecheck, testes ou navegador.

As badges de tipo, marcadores e situação das publicações têm um controle compartilhado de arredondamento entre 0 e 100 px, mantendo o formato de pílula por padrão. O ajuste fica no grupo Publicações e também pode ser localizado por clique duplo na badge da prévia.


## Atividades e encontros

A seção `events` utiliza o mesmo editor e renderer de publicações, com aparência independente guardada em `events` e normalizada por `PublicationsAppearance`. A integração cobre validação, normalização no salvamento, modelos de novas seções e preenchimento das configurações antigas pelo presenter, sem migration.

Mantém os eventos reais com programação ainda não encerrada, ordenados por início, limite de 1 a 12 e ocultação pública quando vazia. Os cards conservam o destaque de dia/mês, período, horário, local, situação por texto e ícone e acesso aos detalhes pelo título. Os controles de capa e resumo ficam restritos à seção de publicações, pois o card de evento apresenta informações da programação.

Título, texto de apoio, fundo, degradê, espaçamentos, colunas, grade/lista/carrossel, ação e hover seguem o padrão anterior. Cada seção configura cores, tipografia, padding, borda e arredondamento dos seus cards. Eventos também permitem escolher a largura, arredondamento, fundo e cor do destaque de data, além de ocultar data ou situação e ajustar o arredondamento das badges. A ação inicial leva à listagem filtrada por eventos e aceita outro destino no seletor compartilhado.

Prévia e site público utilizam o mesmo componente; o clique duplo nas informações, título, data e badge abre seus ajustes. A conferência foi de código e diff. Implementado no código; comportamento em runtime deve ser conferido na homologação manual. Não foram executados build, typecheck, testes ou navegador.


## Equipe do núcleo

A seção `team` armazena sua aparência em `team`, no JSON existente. `TeamAppearance` centraliza padrões, opções e limites utilizados pela validação e normalização. O presenter completa configurações antigas e o modelo de novas seções recebe os mesmos valores, sem migration.

O editor organiza título, texto de apoio, profissionais, ação e organização/fundo como nas seções anteriores. Permite configurar tamanho, arredondamento e borda da foto; exibição de foto e cargo; alinhamento, tamanhos e cores de nome/cargo; fundo, borda, padding e arredondamento dos cards. A organização oferece grade de uma a quatro colunas, lista ou carrossel com avanço opcional e pausa. Fundo, degradê, espaçamentos e ação de equipe completa possuem os controles compartilhados de aparência e destinos.

A consulta permanece limitada aos profissionais ativos marcados como “Aparece no site”, em ordem alfabética; nomes, cargos e fotos continuam sendo gerenciados no cadastro de profissionais. O limite permanece entre 1 e 12, sem inventar registros na prévia. Sem profissionais elegíveis, a seção fica oculta no site público e editável na prévia. A página completa de equipe conserva sua apresentação atual.

`SiteCollection` compartilha cabeçalho, fundo, organização, carrossel e ação entre equipe, publicações e eventos; cada renderer mantém seus próprios cards e configurações. `SiteTeam` reutiliza `TeamMemberCard`, com estilos restritos à home. Clique duplo em foto, nome, cargo e card direciona aos ajustes correspondentes, e o hover é simulado na prévia como nas seções anteriores.

Revisão de código e diff, sem executar build, typecheck, testes ou navegador. Implementado no código; comportamento em runtime deve ser conferido na homologação manual.


## Dúvidas frequentes, documentos e contato

As seções pendentes `faq`, `resources` e `contact` passam a utilizar os controles de aparência do construtor, mantendo os conteúdos e contratos existentes. Não há migration nem criação de registros demonstrativos.

Perguntas e documentos compartilham a aparência geral `pillars` e a aparência por item `items[*].appearance`, com validação e normalização já utilizadas nas demais listas de cards. Título, texto de apoio, alinhamento, tipografia, linha decorativa, grade/lista, espaçamentos, fundo e degradê seguem o padrão anterior. Cada item mantém seus próprios textos, cores, borda, arredondamento, sombra, dimensões e hover. Perguntas possuem rótulos de pergunta/resposta e mantêm `details`/`summary`, acionáveis pelo teclado e abertos na prévia para edição; começam em lista de uma coluna. Materiais mantêm ícones e links reais de consulta, agora com o seletor compartilhado de destinos, e permanecem desativados por padrão até serem cadastrados. Inclusão, exclusão, reordenação, expansão do editor, erros e clique duplo reutilizam os componentes existentes.

Contato possui aparência independente em `contact`, centralizada em `ContactAppearance`. Reutiliza `SiteCollection` para título, texto de apoio, organização, espaçamentos e fundo, com grade ou lista. Os controles abrangem ícones (exibição, tamanho, fundo, borda e arredondamento), textos dos canais, alinhamento, cores, padding e arredondamento dos cards, além da exibição de redes sociais. Endereço, telefone, e-mail e redes continuam vindo da Identidade; apenas os canais preenchidos são exibidos, e os links `tel:` e `mailto:` são preservados. Não foi criado formulário público nem canal fictício. O clique duplo sobre os valores leva ao campo correspondente da Identidade; os elementos de aparência levam aos ajustes da seção.

O presenter completa configurações antigas e o salvamento normaliza os novos ajustes. O editor genérico anterior foi substituído pela composição compartilhada, sem manter uma segunda lista de controles. Prévia e site público utilizam os mesmos renderers. Revisão de código e diff, sem executar build, typecheck, testes ou navegador. Implementado no código; comportamento em runtime deve ser conferido na homologação manual.


O construtor agora reúne editor e prévia dentro de um único `CardBox`, seguido imediatamente pelo `FormActions`, como nas páginas de visualização. A prévia acompanha a rolagem no desktop, limitada ao mesmo card que contém o editor, e mantém a rolagem interna do site. O overflow da aplicação usa clip somente nesta tela para permitir o sticky; flow-root contém a margem inferior e preserva o espaço reservado ao rodapé. Correção revisada no código e no diff; conferência visual permanece para a homologação manual.
