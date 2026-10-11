# Gnai — instruções do repositório

Estas são as diretrizes permanentes para o Gnai, sistema de acessibilidade e inclusão com módulos AEE e Radar Inclusivo, originado como projeto acadêmico/TCC. Instruções específicas do usuário prevalecem no escopo da tarefa; uma exceção visual para uma tela não redefine o padrão global. Leia também `resources/react/AGENTS.md` ao trabalhar no frontend e preserve suas regras.

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

O catálogo `/react/index.html`, disponível em ambiente `local` ou `testing` com Laravel em execução, reúne exemplos implementados. `pages/Catalog/Catalog.tsx` carrega `app/App.tsx`, e `app/routes.tsx` organiza suas demonstrações. Use `pages/FormElements/`, `pages/FormValidation/` e `pages/AccountPages/` como referências de composição, sem tratar dados, links ou submits demonstrativos como integração pronta com o Gnai. Procure novamente telas de negócio concluídas antes de cada migração e siga seus padrões quando existirem.

Os templates Blade antigos de telas e seus JS/CSS estão preservados como referência, fora da entrada Vite atual; não reintroduza renderização alternativa Blade ou fragmentos HTML nas listagens. Templates Blade de PDFs e e-mails continuam em uso e devem ser preservados.

## Fluxo obrigatório para reconstruir uma tela

**Blade → requisitos, mensagens e feedback → integrações → assets e padrões Gnai → componentes e páginas React de referência → composição TSX com identidade e acessibilidade → validação e comparação funcional → relato.**

Antes de implementar:

1. Localize a versão Blade, seus componentes, partials e scripts. Pesquise no repositório antes de pedir caminhos ao usuário.
2. Rastreie rotas, controllers, dados de entrada/validações, queries/actions, permissões e contratos. Validações usam também Spatie Laravel Data; não procure apenas por Form Requests.
3. Faça um inventário dos campos, valores iniciais, opções, máscaras, uploads, textos, botões/links, ações, estados e condicionais. Confira métodos HTTP, parâmetros, nomes dos campos, props, redirects, toasts/alerts, erros inline, loading e recuperação após falhas.
4. Verifique assets próprios usados na área e em áreas semelhantes, antes de criar logo, banner, imagem ou cabeçalho. Inspecione componentes que encapsulam esses padrões, seus tipos/props, layouts e telas React semelhantes. Distinga exemplos do catálogo de funcionalidades já integradas.
5. Só então defina e implemente a composição da página, identificando eventuais lacunas de integração.

Exemplo para alunos: compare `resources/views/pages/specialized-educational-support/students/{create,edit}.blade.php` com `routes/modules/specialized-educational-support.php` e, dentro de `app/Domains/SpecializedEducationalSupport/`, `UI/Controllers/StudentController.php`, `Application/Data/Students/` e `Application/Queries/Students/StudentFormQuery.php`. A criação e a edição têm contratos diferentes; campos como `status` e `remove_photo` precisam ser conferidos na edição.

## Paridade funcional e limites da tarefa

- Use o Blade como referência de requisitos, informações e comportamento; reconstrua em TSX por composição, sem tradução mecânica do HTML/CSS antigo.
- Preserve campos, submits, validações, loading, erros, sucesso, navegação/redirects, filtros, busca, paginação, modais, confirmações, permissões, condições e integrações aplicáveis. Compare o resultado com o inventário, incluindo estados vazios e falhas.
- Preserve nomes/formato dos campos e valores de enums aceitos pelo backend, inclusive normalização, uploads e remoção de arquivos. Validação no cliente não substitui a do servidor.
- Preserve significado dos textos do Gnai; corrija erros evidentes e branding herdado sem reescrever o conteúdo desnecessariamente.
- Não adicione campos, funcionalidades ou novos fluxos por iniciativa própria. A primeira entrega busca paridade moderna, com mudanças de UX limitadas ao necessário.
- Não remova funcionalidades por parecerem antigas. Remoções ou alterações funcionais exigem solicitação explícita ou razão técnica clara, relatada ao usuário.
- Preserve os arquivos Blade legados até haver pedido explícito de remoção. Complete a página React existente ou adicione a necessária, com integração mínima.
- Preserve endpoints, contratos, regras de negócio, modelos e banco. Não altere controllers, migrations ou contratos por conveniência do frontend; se necessário, explique o motivo antes de ampliar significativamente o escopo.
- Mantenha alterações focadas na tela e nos componentes necessários. Relate oportunidades externas ao escopo em vez de fazer refatorações abrangentes.

## Componentização e integração React

As páginas compõem componentes, organizam seções e coordenam estado e integração da funcionalidade. Evite páginas monolíticas, cópias de componentes e extrações triviais sem responsabilidade ou reutilização real.

Procure primeiro em `resources/react/components/`; os caminhos abaixo são relativos a essa pasta:

| Necessidade | Componentes existentes |
| --- | --- |
| Formulários | `Forms/FormField`, `TextInput`, `TextArea`, `SelectField`, `ChoiceControl`, `MaskedInput`, `FileUploadButton`, `FileDropzone`, `FormSection`, `FormPage` (todos sob `Forms/`) |
| Ações, agrupamento e feedback | `UI/Button`, `UI/CardBox`, `UI/Panel`, `UI/Modal`, `UI/InlineAlert`, `UI/NotificationToast`, `UI/AppLoader`, `UI/ErrorState` |
| Tabelas e navegação | `Tables/Table`, `Tables/DataTable`, `Navigation/Pagination`, `Navigation/PageBreadcrumb`, `Navigation/PageHeader` |
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
- Reutilize `resources/react/components/UI/NotificationToast/{NotificationToast,NotificationToastLayer}.tsx`; exemplos de uso estão em `resources/react/pages/Notifications/Notifications.tsx`. Hoje seu estado é local ao catálogo e **não existe conexão global entre flash e toast**. `UI/AuthFeedback/AuthFeedback.tsx` já consome `error`, `status` e `success` como `InlineAlert` na autenticação. O login envia sucesso para o dashboard, mas este ainda é um `PageScaffold` sem consumidor de flash; o logout atual apenas redireciona, sem mensagem de sucesso.
- Ao integrar feedback em uma migração, reutilize esse transporte e os componentes existentes; complete a integração compartilhada necessária em vez de criar toast por página, biblioteca ou sistema global paralelo sem necessidade técnica comprovada. Confira a exibição após redirects e evite repetir a mesma mensagem ou mostrá-la simultaneamente por dois mecanismos.
- Toast/alert geral informa resultado ou falha geral; erro de campo fica junto à entrada, com associação acessível. Um resumo pode complementar, mas não substituir erros específicos nem disparar um toast para cada campo inválido.
- Durante processamento, indique a operação, desabilite o submit e impeça envios duplicados; após falha, permita corrigir e tentar novamente. `Forms/LoginForm` e `Forms/ForgotPasswordForm` usam `form.processing`, texto de processamento e `aria-busy`. `UI/AppLoader` existe, mas não está montado na entrada atual e seu overlay é `aria-hidden`; não o considere feedback acessível ou bloqueio de submit automaticamente disponível.

## Acessibilidade básica e limites atuais

Sem exigir auditoria completa por tela, confira HTML semântico, hierarquia de títulos, labels ligados por `htmlFor`/`id`, indicação compreensível de obrigatórios, foco visível e teclado nas ações principais. Ações usam botão; navegação usa link. Garanta contraste legível e comunique estados também por texto/ícone, não apenas cor. Logo/imagem informativa precisa de alternativa adequada; decoração usa `alt=""` ou ocultação apropriada. Não substitua texto essencial por imagem sem alternativa nem aplique ARIA quando HTML nativo resolve.

- `FormField` fornece label e IDs de hint/erro; o consumidor ainda deve passar o `id` ao controle e ligar `aria-describedby`. `TextInput` suporta `invalid`/`aria-invalid` e atributos nativos; `ChoiceControl` associa input/label. O login já associa erros aos campos e os anuncia. `Button` diferencia botão/link e aceita `aria-label`; ações só com ícone precisam de nome acessível. Use `UI/Tooltip` quando faltar contexto, sem espalhar `title`; tooltip não substitui o nome do controle.
- `Tooltip` abre por hover/foco e usa `role="tooltip"`/`aria-describedby`, mas sobrescreve a descrição anterior e depende de o filho encaminhar esse atributo (o `Button` atual não o encaminha). Verifique a composição real; não presuma associação automática. `Button.css` remove o outline e várias variantes igualam foco ao estado normal: melhoria compartilhada de foco permanece pendente.
- `NotificationToast` já usa `role="alert"` e oculta o ícone decorativo; `AuthFeedback` usa região `aria-live="polite"`/`aria-atomic`. `InlineAlert` sozinho não cria região de anúncio. O toast comum fecha por clique/temporizador sem botão de fechamento por teclado; sua opção `confirm` é demonstração com texto fixo e `window.alert`, não confirmação de negócio pronta. Registre essas lacunas para melhoria do componente comum, sem sistema paralelo.
- `Modal` já tem `role="dialog"`, `aria-modal`, fechamento por Escape e botão nomeado; faltam gestão de foco inicial, contenção e retorno ao disparador, e o título usa ID fixo. São pendências a tratar no componente compartilhado quando pertinente, não garantia de acessibilidade completa.

## Desenvolvimento e validação

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
| Estilo PHP, se houver mudanças PHP | `vendor/bin/pint --test <arquivo.php>`; para formatar, remova `--test` e informe os arquivos alterados |

- Em Docker, use o serviço `node` para npm e `app` para PHP; não há servidor React separado. Veja `README.md` e `resources/react/README.md` para preparação do ambiente. Os comandos `make` usam `ENV=dev` por padrão.
- Execute typecheck/build para mudanças React e testes relevantes para integrações/backend. O build já inclui o typecheck. Confira desktop, mobile e interações da tela real; se alterar componentes compartilhados, confira também o catálogo `/react/index.html`.
- `make test` não inclui a suíte `Feature` da raiz; execute explicitamente `tests/Feature/InertiaPagesTest.php` quando afetar a integração. PHPUnit está configurado com SQLite em memória.
- Não há scripts npm de lint, formatter ou testes frontend no estado atual. Não invente `npm run lint` ou `npm test`; siga os formulários integrados existentes quando aplicável. Respeite `.editorconfig` e o estilo local do arquivo.
- Mudanças exclusivamente documentais pedem revisão dos caminhos/comandos e do diff; não precisam executar a aplicação. Corrija regressões introduzidas e relate impedimentos históricos sem expandir a tarefa para corrigi-los.
- Ao entregar uma tela, relate arquivos alterados, funcionalidades preservadas, diferenças intencionais e verificações executadas ou impedidas. Aparência renderizada, isoladamente, não comprova conclusão.
- Compare a entrega com o inventário: campos/ações/validações/permissões; sucesso e falha visíveis, erros inline e processamento sem submits duplicados; componentes e feedback compartilhados; assets, navegação e identidade Gnai sem branding do tema ou novas cores locais; labels, identificação de ícones, teclado e responsividade; verificações aplicáveis. Registre diferenças e lacunas restantes.
