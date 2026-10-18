# Gnai — instruções do repositório

Estas são as diretrizes permanentes para o Gnai, sistema de acessibilidade e inclusão com módulos AEE e Radar Inclusivo, originado como projeto acadêmico/TCC. Instruções específicas do usuário prevalecem no escopo da tarefa; uma exceção visual para uma tela não redefine o padrão global. Leia também `resources/react/AGENTS.md` ao trabalhar no frontend e preserve suas regras.

## Documentação e convenções de IA

Consulte as convenções relevantes em `docs/ai-conventions/` antes de implementar ou modificar telas. **Ao criar, refatorar ou migrar telas de formulários React, leia [a convenção de formulários](docs/ai-conventions/frontend/form-pages.md) antes de iniciar a implementação.** Ela documenta o padrão atual de alunos, separando regras gerais, componentes condicionais e particularidades do exemplo. Para listagens React, siga [a convenção de listagens](docs/ai-conventions/frontend/list-pages.md), baseada nos componentes compartilhados de `Tables/TableList`.

Novas convenções devem ser adicionadas a `docs/ai-conventions/` e indexadas aqui quando existirem. Atualize o documento correspondente quando uma mudança autorizada alterar o padrão oficial; não crie uma convenção por formulário nem documentos vazios para usos futuros. Preserve também as instruções específicas do diretório em que estiver trabalhando.

## Arquitetura e caminhos reais

- Backend: Laravel 12, PHP `^8.2` no Composer e PHP 8.3-FPM no `Dockerfile`; MySQL 8 em Docker. Desenvolvimento usa Nginx; produção usa Apache no host encaminhando ao PHP-FPM, conforme `README.md` e os arquivos Compose.
- Backend organizado em `app/Domains/`: `Auth`, `Backup`, `Dashboard`, `InclusiveRadar`, `Notifications`, `Reporting` e `SpecializedEducationalSupport`. Procure controllers em `UI/Controllers`, validação e normalização em `Application/Data` e `Application/Rules`, operações em `Application/Actions`, consultas em `Application/Queries` e modelos/enums/DTOs em `Domain`. Código transversal fica em `app/Shared`; não presuma a estrutura convencional `app/Http/Controllers`.
- Rotas: `routes/web.php`, `routes/auth.php` e `routes/modules/{specialized-educational-support,inclusive-radar,reporting,backup}.php`. Confira também middlewares e tratamento de exceções em `bootstrap/app.php`.
- Blade legado: `resources/views/pages/`, especialmente `specialized-educational-support/` e `inclusive-radar/`; autenticação em `resources/views/auth/`. Componentes, layouts e fragmentos ficam em `resources/views/components/`, `resources/views/layouts/` e `resources/views/partials/`, além dos `partials/` de cada funcionalidade.
- Scripts e estilos legados: `resources/js/` e `resources/css/`. Leia scripts inline e componentes Blade referenciados pela tela, além dos arquivos externos.
- Frontend ativo: React 19 + TypeScript + Inertia, em `resources/react/`. Entrada `main.tsx`; páginas em `pages/`; alias `@/` aponta para essa pasta. `vite.config.js` compila apenas a entrada React. Componentes e páginas novos usam **`.tsx`**; lógica e tipos sem markup usam `.ts`.
- Documento de entrada: `resources/views/inertia.php`, um arquivo PHP mínimo. Controllers de telas usam `Inertia::render('Dominio/Recurso/Acao', $props)`, resolvido como `resources/react/pages/Dominio/Recurso/Acao.tsx`.
- Estilos ativos: `resources/react/styles/{reset,tokens,globals,app-shell,icons}.css`, além de CSS junto dos componentes e páginas. Assets/plugins locais: `public/assets/react/`.
- Testes: `tests/`, `app/Domains/*/Tests/` e `phpunit.xml`; documentação adicional em `Docs/`, especialmente `Docs/inclusive-radar/`.

## Estado da migração: não confundir estrutura com tela pronta

O roteamento de telas já usa Inertia. Muitas páginas de negócio ainda contêm somente `PageScaffold` e um título, por exemplo `pages/Auth/ResetPassword.tsx` e `pages/SpecializedEducationalSupport/Students/{Create,Edit}.tsx`. Esses arquivos indicam destino e nomenclatura, mas não comprovam paridade funcional. Já `pages/Auth/{Login,ForgotPassword}.tsx` compõem `Navigation/AuthLayout` e formulários em `components/Forms/{LoginForm,ForgotPasswordForm}/`: são referências reais de `useForm`, erros inline e processamento, não de identidade visual definitivamente consolidada.

O catálogo `/react/index.html`, disponível em ambiente `local` ou `testing` com Laravel em execução, reúne exemplos implementados. `pages/Catalog/Catalog.tsx` carrega `app/App.tsx`, e `app/routes.tsx` organiza suas demonstrações. Consulte o código de `pages/FormElements/`, `pages/FormValidation/` e `pages/AccountPages/` como referência de composição, sem tratar dados, links ou submits demonstrativos como integração pronta com o Gnai. Consultar essas referências não exige executar o catálogo ou iniciar a aplicação. Procure novamente telas de negócio concluídas antes de cada migração e siga seus padrões quando existirem.

Os templates Blade antigos de telas e seus JS/CSS estão preservados como referência, fora da entrada Vite atual; não reintroduza renderização alternativa Blade ou fragmentos HTML nas listagens. Templates Blade de PDFs e e-mails continuam em uso e devem ser preservados.

## Fluxo obrigatório para reconstruir uma tela

**Entender o legado e seus requisitos → entender os contratos → verificar assets e padrões Gnai → reutilizar componentes → implementar React com identidade e acessibilidade → ajustar integrações necessárias → comparar o código com o legado e revisar o diff → entregar.**

Em uma migração comum, a responsabilidade do agente é investigar, implementar, ajustar os arquivos necessários, revisar o código e entregar. A homologação visual e funcional no navegador será feita manualmente pelo usuário posteriormente. Não prepare nem execute um ambiente de homologação automaticamente; siga os limites da seção “Desenvolvimento e homologação”.

Antes de implementar:

1. Localize a versão Blade, seus componentes, partials e scripts. Pesquise no repositório antes de pedir caminhos ao usuário.
2. Rastreie rotas, controllers, dados de entrada/validações, queries/actions, permissões e contratos. Validações usam também Spatie Laravel Data; não procure apenas por Form Requests.
3. Faça um inventário dos campos, valores iniciais, opções, máscaras, uploads, textos, botões/links, ações, estados e condicionais. Confira métodos HTTP, parâmetros, nomes dos campos, props, redirects, toasts/alerts, erros inline, loading e recuperação após falhas.
4. Verifique assets próprios usados na área e em áreas semelhantes, antes de criar logo, banner, imagem ou cabeçalho. Inspecione componentes que encapsulam esses padrões, seus tipos/props, layouts e telas React semelhantes. Distinga exemplos do catálogo de funcionalidades já integradas.
5. Só então defina e implemente a composição da página, identificando eventuais lacunas de integração.

Após implementar, ajuste apenas as integrações necessárias, compare o código com o inventário funcional, revise o próprio diff e entregue. Essa revisão é de código e não exige execução da aplicação ou de testes.

Exemplo para alunos: compare `resources/views/pages/specialized-educational-support/students/{create,edit}.blade.php` com `routes/modules/specialized-educational-support.php` e, dentro de `app/Domains/SpecializedEducationalSupport/`, `UI/Controllers/StudentController.php`, `Application/Data/Students/` e `Application/Queries/Students/StudentFormQuery.php`. A criação e a edição têm contratos diferentes; campos como `status` e `remove_photo` precisam ser conferidos na edição.

## Paridade funcional e limites da tarefa

- Use o Blade como referência de requisitos, informações e comportamento; reconstrua em TSX por composição, sem tradução mecânica do HTML/CSS antigo.
- Preserve campos, submits, validações, loading, erros, sucesso, navegação/redirects, filtros, busca, paginação, modais, confirmações, permissões, condições e integrações aplicáveis. Compare o resultado com o inventário, incluindo estados vazios e falhas.
- Preserve nomes/formato dos campos e valores de enums aceitos pelo backend, inclusive normalização, uploads e remoção de arquivos. Validação no cliente não substitui a do servidor.
- Preserve significado dos textos do Gnai; corrija erros evidentes e branding herdado sem reescrever o conteúdo desnecessariamente.
- Não adicione campos, funcionalidades ou novos fluxos por iniciativa própria. A primeira entrega busca paridade moderna, com mudanças de UX limitadas ao necessário.
- Não remova funcionalidades por parecerem antigas. Remoções ou alterações funcionais exigem solicitação explícita ou razão técnica clara, relatada ao usuário.
- Preserve os arquivos Blade legados até haver pedido explícito de remoção. Complete a página React existente ou adicione a necessária, com integração mínima.
- Preserve endpoints, contratos, regras de negócio, modelos e banco. Pequenos ajustes reais de integração em controllers, props Inertia, Queries, Data/DTO, flash ou rotas são permitidos quando necessários para disponibilizar os dados ou comportamentos que o Blade já utilizava. Não altere banco, migrations ou regras de negócio por conveniência do frontend nem faça refatorações amplas no backend; explique o motivo antes de ampliar significativamente o escopo.
- Mantenha alterações focadas na tela e nos componentes necessários. Relate oportunidades externas ao escopo em vez de fazer refatorações abrangentes.

## Componentização e integração React

As páginas compõem componentes, organizam seções e coordenam estado e integração da funcionalidade. Evite páginas monolíticas, cópias de componentes e extrações triviais sem responsabilidade ou reutilização real.

Procure primeiro em `resources/react/components/`; os caminhos abaixo são relativos a essa pasta:

| Necessidade | Componentes existentes |
| --- | --- |
| Formulários | `Forms/FormLayout`, `FormGrid`, `FormField`, `TextInput`, `TextArea`, `SelectField`, `ChoiceControl`, `ChoiceGroup`, `ToggleControl`, `MaskedInput`, `FileUploadButton`, `FileDropzone`, `EvidenceUpload`, `FormSection`, `FormPage` (todos sob `Forms/`) |
| Ações, agrupamento e feedback | `UI/Button`, `UI/CardBox`, `UI/Panel`, `UI/Modal`, `UI/ConfirmActionModal`, `UI/InlineAlert`, `UI/NotificationToast`, `UI/AppLoader`, `UI/ErrorState` |
| Tabelas e navegação | `Tables/Table`, `Tables/DataTable`, `Tables/TableList`, `Navigation/Pagination`, `Navigation/PageBreadcrumb`, `Navigation/PageTitle` (cabeçalho de negócio), `Navigation/PageHeader` (referência Ubold) |
| Estrutura compartilhada | `Navigation/AuthLayout`, `Navigation/AccountLayout`, `Navigation/TopBarPrimary`, `Navigation/SidebarPrimary`, `Navigation/AppFooter` |

- Leia a API real antes de usar um componente; adapte ou estenda o equivalente adequado quando necessário, evitando recriá-lo dentro da página.
- Siga `components/{UI,Forms,Navigation,Tables}/PascalCase/PascalCase.tsx` e, para páginas de negócio, `pages/Dominio/Recurso/Acao.tsx`. Mantenha CSS, dados, hooks locais e tipos específicos junto de seu proprietário.
- Use imports diretos com `@/` entre pastas e `./` localmente. Componentes não dependem de páginas/app; hooks compartilhados não dependem de UI; utilitários/tipos não dependem das camadas superiores.
- Não repita header, footer, barras ou navegação por página. A autenticação usa `Navigation/AuthLayout`, composto com `Navigation/AccountLayout`; `app/App.tsx` mostra a composição do catálogo, mas inclui suas rotas e dados demonstrativos. Ainda não há `resources/react/layouts/` nem layout de negócio completo no `PageScaffold`; componha uma estrutura compartilhada adequada quando a tela exigir, sem copiar o catálogo inteiro.
- Consuma props reais dos controllers via Inertia. O middleware `app/Shared/Infrastructure/Http/Middleware/HandleInertiaRequests.php` compartilha `auth.user` e `flash`; os erros de validação vêm da integração Inertia. Respeite redirects e contratos JSON dos endpoints que já os oferecem.
- Confira `can:*` nas rotas e as regras de `app/Domains/Auth/`. O usuário compartilhado não contém um mapa completo de permissões; não invente capacidades nem substitua autorização do servidor por condicionais visuais.
- Listagens continuam usando respostas Inertia, inclusive em recargas parciais; não recrie o mecanismo antigo de tabelas HTML via AJAX.
- Preserve limpeza de listeners, instâncias e adaptadores de plugins ao desmontar. Reutilize `hooks/` e `utils/`, preserve licenças/dependências de `public/assets/react/` e não importe Bootstrap global na entrada React.

## Identidade e responsividade

### Convenções de formulários e ações

- Títulos e subtítulos de páginas de negócio reutilizam `Navigation/PageTitle`, baseado no cabeçalho Blade de criar aluno (`h2.text-title`), com título roxo escuro, peso 700, fonte legada e tamanho responsivo de até 2 rem (32 px na base padrão). O subtítulo usa a fonte legada, 1 rem e o cinza do antigo `text-muted`. O componente usa `h1` por padrão sem alterar a aparência pelo nível do heading, e recebe botões ou outras ações por `actions`, na mesma linha à direita, com quebra responsiva. Nas telas de visualizar, criar e editar refatoradas, as ações principais ficam no rodapé sticky compartilhado, sem repetição no cabeçalho. Use esse componente nas novas telas de negócio; `Navigation/PageHeader` mantém a referência Ubold para exemplos existentes. Reutilize `Navigation/PageBreadcrumb` com o padrão visual do tema, links azuis (`#337ab7`) e hover/foco em azul escuro (`#23527c`). Evite reproduzir o cabeçalho ou seus estilos por página.
- Selects de negócio usam `Forms/SelectField`, baseado no Select2 do Ubold, com foco do controle na mesma cor roxa dos inputs e opções destacadas em roxo. As bordas da busca e do painel aberto são cinza neutro, inclusive ao focar a busca. Passe uma lista `options` com valores e textos; o menu mostra o placeholder ("Selecione" ou "Todos", conforme o campo) e as opções, sem títulos de grupo nem repetição do label dentro do menu. Preserve valores aceitos pelo backend e seleção controlada. Não crie outro select ou adaptador por tela.
- Fotos de pessoas usam `Forms/PersonPhotoUpload`, baseado no círculo de 11 rem do Blade `forms/photo-upload.blade.php`, com clique, arrastar e soltar, preview e remoção. O formulário pai controla `file`, `current`, `removed` e o envio; o componente não faz upload automático. Os padrões JPG/PNG e 2 MB correspondem a alunos; configure `acceptedTypes`/`maxSizeBytes` somente conforme as regras reais do cadastro que o reutiliza. Use `onValidatingChange` para impedir o envio durante a leitura da imagem. Não substitua esse componente por um uploader genérico de arquivos.
- Reúna os campos de um formulário em um único `UI/CardBox`. Divida-o com `Forms/FormSection` usando `variant="inline"` e um `iconClass` pertinente a cada seção. Esse componente também pode organizar informações em telas de visualização e usa o contorno compartilhado `--ui-color-card-border`, o mesmo das listagens. Títulos e ícones das seções ficam em cinza (`--ui-color-heading`), enquanto os labels dos campos usam o roxo escuro (`--ui-color-form-label`), sem acompanhar a cor clara do foco. A linha cinza clara de 2 px fica abaixo do título, alinhada com seu início e contida no padding do card, sem tocar suas bordas.
- Organize campos relacionados na mesma linha quando houver espaço, com grids responsivos que reduzam colunas em telas menores. Campos longos, como endereço com editor, ocupam toda a largura. Preserve todos os campos e condições existentes; compactar o formulário não permite remover campos ou alterar contratos.
- Nas telas de visualizar, criar e editar refatoradas, reutilize `Forms/FormActions` imediatamente depois do conteúdo principal. Ele forma uma unidade contínua com o card, cria um rodapé sticky com ações principais centralizadas e apresenta o status do envio sem deslocar os botões. Ações secundárias, como exportar PDF, usam a prop `secondary` e ficam recuadas à esquerda do rodapé. Não repita essas ações no cabeçalho.
- Botões usam ícone pertinente e texto por padrão. No rodapé sticky, as ações principais usam somente ícones, com `iconOnly` e um `aria-label` que descreva a ação. Escolha o ícone pelos props do `UI/Button`; ícones decorativos ficam ocultos para leitores de tela. Remova o ícone somente quando solicitado (`showIcon={false}`).
- Para salvar e cancelar no rodapé sticky, reutilize `UI/Button` com `tone="save"` e `tone="cancel"`: salvar usa check verde e cancelar/voltar usa X cinza. As cores estão centralizadas em `resources/css/variables.css` e nos aliases de `resources/react/styles/tokens.css`; não fixe novas cores nas páginas. Preserve desabilitação durante o envio e indicação de processamento.
- Essas convenções orientam novas telas e alterações autorizadas; não faça uma refatoração de todas as telas existentes por iniciativa própria. Exemplos de widgets distintos no catálogo continuam referências demonstrativas.

**A migração moderniza a tecnologia e a apresentação do Gnai, preservando sua identidade visual, padrões de interação, feedbacks e funcionalidade. Antes de criar algo novo, procure o que o próprio Gnai já possui.** Componentes do tema são ferramentas; logo, conteúdo, imagens e navegação demonstrativos não se tornam padrões do produto. A identidade inclui também organização das listagens, títulos/banners, respostas dos formulários, confirmações de ações destrutivas e carregamentos.

- A interface é em pt-BR e a marca é **Gnai**. Não exponha “Bold”, “Interface”, nomes do template ou dados fictícios do catálogo como identidade/dados reais do produto; revise também menus, títulos e rodapé dos componentes reutilizados.
- Reutilize tokens e estilos existentes, sem criar paletas por tela. Há uma divergência atual: `resources/react/styles/tokens.css` define `--ui-color-primary: #5d9cec`, enquanto `resources/css/variables.css` registra o roxo legado do Gnai (`#322b75`). Consulte as telas Gnai antes de decidir; não resolva essa divergência com cores de marca hardcoded nas páginas ou novas variantes locais de roxo/azul. A consolidação oficial da paleta é uma tarefa separada, centralizada nos tokens/tema e nos componentes que ainda fixam cores (como `UI/Button/Button.css`), considerando seus efeitos compartilhados. Não importe todo o CSS legado nem recolora o sistema fora do escopo.
- Preserve estilos e comportamentos dos componentes, seguindo seus padrões responsivos. Melhore estrutura, tipografia e espaçamento; evite dimensões rígidas que quebrem telas menores. Preserve labels, foco, teclado e apresentação acessível dos erros.
- Uma reprodução temporária de outro tema, pedida pelo usuário, vale apenas para a tela/tarefa indicada.

### Assets próprios confirmados

| Asset | Uso real e referência |
| --- | --- |
| `public/images/logo.png`, `logo2.png`, `gnai.png` (todos em `public/images/`) | Identidade existente: login Blade usa `logo.png`; `resources/views/partials/sidebar.blade.php` usa `logo2.png`; `resources/views/components/pdf/header.blade.php` usa `gnai.png`. Reutilize a variação apropriada; não substitua por logo do template ou representação improvisada. |
| `public/images/favicon.png` | Referenciado por `resources/views/layouts/master.blade.php` e telas Blade de erro/sobre. Ainda não está ligado ao documento `resources/views/inertia.php`. |
| `public/images/page-header3.png` | Banner institucional NAI usado como fundo repetido, com overlay, título/subtítulo e ações em `resources/views/components/table/page-header.blade.php`; exemplo: listagem Blade de alunos. Avalie sua reutilização antes de propor outro cabeçalho. `Navigation/PageHeader` possui variante `banner`, mas ela não incorpora essa imagem nem reproduz esse padrão. |
| `public/images/default-user.jpg`, `public/images/team/` | Avatar padrão dos modelos `User`/`Person` e fotos da equipe em `resources/views/pages/about-us.blade.php`. |
| `resources/images/icons/select-down.svg` e `public/images/icons/select-down.svg` | Seta dos filtros; o CSS legado `resources/css/components/filter-search.css` referencia a versão em resources. Não foi encontrada uma biblioteca própria extensa de ícones/SVGs institucionais. |

`public/assets/react/images/` contém imagens do tema; não as trate como assets oficiais. A autenticação React atual ainda usa a textura `agsquare.png` via `Navigation/AccountLayout`, sem a logo do login legado. Não foi encontrada outra imagem própria de fundo para autenticação; confira o CSS legado `resources/css/auth/login.css`. Preserve os arquivos originais e escolha assets pelo uso real, não apenas pelo nome.

## Feedback de operações

- Feedback é requisito de paridade: confira login/logout, CRUD, ativação/desativação, upload/importação, configurações, ações em modal e demais operações assíncronas aplicáveis. Preserve a mensagem específica do Gnai e avalie confirmação de sucesso conforme a ação; falhas não podem ficar apenas no console, Network ou Promise. Não documente como existente um feedback que ainda não foi implementado.
- Fluxo atual: controllers fazem redirect com mensagens de sessão; `HandleInertiaRequests.php` compartilha `flash.success`, `error`, `warning` e `status`. `withErrors`/validações chegam por `props.errors` e `useForm().errors`; endpoints JSON mantêm seu próprio contrato. O toast Blade (`resources/views/components/messages/toast.blade.php`, com `resources/js/pages/messages.js`) também aceita `info`, chave ainda não compartilhada pelo middleware atual; confira se a funcionalidade depende dela.
- Reutilize `resources/react/components/UI/NotificationToast/{NotificationToast,NotificationToastLayer}.tsx`; exemplos de uso estão em `resources/react/pages/Notifications/Notifications.tsx`. `Navigation/AppLayout` monta uma única instância global de `UI/FlashNotifications/FlashNotifications.tsx`, responsável por converter `flash.success`, `error`, `warning` e `status` em toast nas telas autenticadas. Não monte outro consumidor de flash em páginas, wrappers de domínio ou `BusinessPage`, pois isso duplica a mesma notificação. `UI/AuthFeedback/AuthFeedback.tsx` continua consumindo `error`, `status` e `success` como `InlineAlert` na autenticação, cujas páginas não usam `AppLayout`.
- Ao integrar feedback em uma migração, reutilize esse transporte e os componentes existentes; complete a integração compartilhada necessária em vez de criar toast por página, biblioteca ou sistema global paralelo sem necessidade técnica comprovada. Confira no código o consumo das mensagens após redirects e evite repetir a mesma mensagem ou mostrá-la simultaneamente por dois mecanismos.
- Toast/alert geral informa resultado ou falha geral; erro de campo fica junto à entrada, com associação acessível. Um resumo pode complementar, mas não substituir erros específicos nem disparar um toast para cada campo inválido.
- Durante processamento, indique a operação, desabilite o submit e impeça envios duplicados; após falha, permita corrigir e tentar novamente. `Forms/LoginForm` e `Forms/ForgotPasswordForm` usam `form.processing`, texto de processamento e `aria-busy`. `UI/AppLoader` é montado uma única vez em `main.tsx`, acompanha as trocas de página do Inertia e usa o preloader visual do Ubold; ele não substitui o estado de processamento específico de formulários e operações assíncronas.

## Acessibilidade básica e limites atuais

Sem exigir auditoria completa por tela, confira HTML semântico, hierarquia de títulos, labels ligados por `htmlFor`/`id`, indicação compreensível de obrigatórios, foco visível e teclado nas ações principais. Ações usam botão; navegação usa link. Garanta contraste legível e comunique estados também por texto/ícone, não apenas cor. Logo/imagem informativa precisa de alternativa adequada; decoração usa `alt=""` ou ocultação apropriada. Não substitua texto essencial por imagem sem alternativa nem aplique ARIA quando HTML nativo resolve.

- `FormField` fornece label e IDs de hint/erro; o consumidor ainda deve passar o `id` ao controle e ligar `aria-describedby`. `TextInput` suporta `invalid`/`aria-invalid` e atributos nativos; `ChoiceControl` associa input/label. O login já associa erros aos campos e os anuncia. `Button` diferencia botão/link e aceita `aria-label`; ações só com ícone precisam de nome acessível. Use `UI/Tooltip` quando faltar contexto, sem espalhar `title`; tooltip não substitui o nome do controle.
- `Tooltip` abre por hover/foco e usa `role="tooltip"`/`aria-describedby`, mas sobrescreve a descrição anterior e depende de o filho encaminhar esse atributo (o `Button` atual não o encaminha). Verifique a composição real; não presuma associação automática. `Button.css` remove o outline e várias variantes igualam foco ao estado normal: melhoria compartilhada de foco permanece pendente.
- `NotificationToast` já usa `role="alert"` e oculta o ícone decorativo; `AuthFeedback` usa região `aria-live="polite"`/`aria-atomic`. `InlineAlert` sozinho não cria região de anúncio. O toast comum fecha por clique/temporizador sem botão de fechamento por teclado; sua opção `confirm` é demonstração com texto fixo e `window.alert`, não confirmação de negócio pronta. Registre essas lacunas para melhoria do componente comum, sem sistema paralelo.
- `Modal` já tem `role="dialog"`, `aria-modal`, fechamento por Escape e botão nomeado; faltam gestão de foco inicial, contenção e retorno ao disparador, e o título usa ID fixo. São pendências a tratar no componente compartilhado quando pertinente, não garantia de acessibilidade completa.

## Desenvolvimento e homologação

### Limites de execução em migrações comuns

- A inspeção de código continua obrigatória: leia Blade, TSX, rotas, controllers, Data/Rules, Queries/Actions, componentes, feedback, assets e permissões necessários. A simplificação da homologação não reduz os requisitos de integração, paridade funcional, acessibilidade e responsividade.
- Não crie nem altere testes automaticamente apenas porque uma tela foi migrada: Unit, Feature, Inertia, React, frontend ou E2E (Playwright/Cypress). Não crie mocks, scripts temporários, arquivos de teste manual ou infraestrutura de teste. Testes existentes podem ser consultados como documentação, sem alterá-los ou executá-los.
- Não prepare SQLite para homologar a migração: não crie banco temporário, execute migrations, configure fixtures ou popule banco de testes. Configurações de teste existentes não tornam sua preparação ou execução parte da migração.
- Não inicie `npm run dev`, Vite, Laravel, Docker, Nginx, Apache ou outro servidor apenas para conferir a implementação. Não abra navegador, acesse URLs, inspecione telas em runtime, gere screenshots ou execute o catálogo React automaticamente.
- Não execute automaticamente build, typecheck, PHPUnit, suítes de testes, testes de frontend/E2E ou outros processos de homologação, inclusive quando houver pequenos ajustes backend ou componentes compartilhados.
- Essas ações só devem ocorrer quando o usuário solicitar explicitamente ou quando a própria tarefa tiver como objetivo testar/validar o ambiente. Em tarefas normais de criação, refatoração ou migração de telas, trabalhe sobre o código, revise o diff e entregue.

### Comandos disponíveis como referência

**Esses comandos são referências técnicas e não fazem parte automaticamente do fluxo de cada migração. Execute-os somente quando o usuário solicitar explicitamente ou quando a própria tarefa tiver como objetivo testar/validar o ambiente.**

Comandos na raiz, conforme `package.json`, `composer.json`, `Makefile` e `phpunit.xml`:

| Finalidade | Comando |
| --- | --- |
| Desenvolvimento Vite | `npm run dev` ou `make npm-dev` no Docker |
| Typecheck TS/TSX | `npm run check` |
| Build, incluindo typecheck | `npm run build` ou `make npm-build` em desenvolvimento |
| Typecheck no Docker | `docker compose --env-file .env.dev -f docker-compose.dev.yml exec node npm run check` |
| Testes padrão do Make (Unit + Domains) | `make test` |
| Integração de páginas Inertia | `make test TEST=tests/Feature/InertiaPagesTest.php` |
| Testes específicos | `make test TEST=app/Domains/InclusiveRadar/Tests/Feature/Waitlists` (substitua pelo caminho relevante existente) |
| Todos os testes no PHP local preparado | `composer test` (limpa config e executa `php artisan test`) |
| Verificação de estilo PHP | `vendor/bin/pint --test <arquivo.php>`; para formatar, remova `--test` e informe os arquivos alterados |

- Em Docker, use o serviço `node` para npm e `app` para PHP; não há servidor React separado. Veja `README.md` e `resources/react/README.md` para preparação do ambiente. Os comandos `make` usam `ENV=dev` por padrão.
- `npm run check` verifica os tipos TS/TSX; `npm run build` já inclui o typecheck. Esses comandos não são critérios obrigatórios de conclusão de uma migração comum.
- `make test` não inclui a suíte `Feature` da raiz; `make test TEST=tests/Feature/InertiaPagesTest.php` é a referência para a integração Inertia quando essa execução for solicitada.
- Não há scripts npm de lint, formatter ou testes frontend no estado atual. Não invente `npm run lint` ou `npm test`; siga os formulários integrados existentes quando aplicável. Respeite `.editorconfig` e o estilo local do arquivo.
- Mudanças exclusivamente documentais pedem revisão dos caminhos/comandos e do diff; não precisam executar a aplicação. Corrija problemas identificados na revisão e relate lacunas sem expandir a tarefa para corrigi-las.

### Conclusão e relato de migrações

Considere uma migração comum concluída quando os arquivos React necessários estiverem implementados, os componentes existentes tiverem sido reutilizados e a integração com os contratos existentes estiver feita. Compare o código com o inventário: campos, ações, validações, mensagens, permissões, estados vazios, loading, prevenção de submits duplicados, feedback, assets, identidade Gnai, acessibilidade e responsividade. Revise o diff e mantenha as alterações dentro do escopo.

Não exija navegador aberto, servidor rodando, screenshot, teste criado, PHPUnit verde, SQLite configurado, E2E, suíte completa ou homologação visual automática como critérios de conclusão. A homologação final será feita manualmente pelo usuário.

Entregue um resumo curto com arquivos alterados, telas implementadas, componentes reutilizados, funcionalidades principais preservadas, eventuais ajustes backend necessários e pontos para a homologação manual. Não gere relatório extenso de testes não solicitados nem trate a ausência de testes ou servidores como problema. Se um comportamento depender de confirmação em runtime, informe objetivamente: “Implementado no código; comportamento em runtime deve ser conferido na homologação manual.” Registre diferenças e lacunas reais sem afirmar que houve homologação automática.
