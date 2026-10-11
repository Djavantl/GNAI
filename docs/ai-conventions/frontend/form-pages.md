# Convenção de telas de formulários React do Gnai

Esta convenção documenta o padrão já implementado no cadastro e na edição de alunos. Consulte-a antes de criar, refatorar ou migrar formulários React, junto do `AGENTS.md` da raiz e de `resources/react/AGENTS.md`. Instruções explícitas do usuário prevalecem; uma experiência visual local não redefine automaticamente o padrão compartilhado.

**Na migração, o Blade antigo é referência de dados, comportamentos e regras existentes; o formulário React de alunos é referência para a nova estrutura e identidade visual.** Quando os visuais divergirem, siga o React padronizado e preserve as funcionalidades, permissões e contratos do formulário de origem.

## Referência principal e responsabilidades

Os caminhos abaixo são referências da implementação atual, não uma obrigação de organizar todos os domínios como alunos. Releia o código antes de reutilizar suas APIs.

| Responsabilidade | Arquivo de referência |
| --- | --- |
| Página de cadastro, título e Cancelar no topo | [Create.tsx](../../../resources/react/pages/SpecializedEducationalSupport/Students/Create.tsx) |
| Página de edição, registro existente e identidade do formulário | [Edit.tsx](../../../resources/react/pages/SpecializedEducationalSupport/Students/Edit.tsx) |
| Composição da página, breadcrumbs, cabeçalho e feedback | [StudentPage.tsx](../../../resources/react/components/Students/StudentPage.tsx) |
| Dados, campos, seções, submissão, upload e processamento | [StudentForm.tsx](../../../resources/react/components/Students/StudentForm.tsx) |
| Layout dos campos e adaptações responsivas do exemplo | [Students.css](../../../resources/react/components/Students/Students.css) |
| Tipos recebidos e auxiliares do domínio | `resources/react/components/Students/Students.types.ts` e `Students.utils.ts` |
| Falhas de conexão e de resposta HTTP | `resources/react/components/Students/useStudentRequest.ts` |
| Resolução Inertia e layout compartilhado | `resources/react/main.tsx` e `resources/react/components/Navigation/AppLayout/AppLayout.tsx` |

A página compõe o cabeçalho e as ações e recebe as props reais do controller. O componente de formulário controla os dados, erros, envio e estados locais. Componentes compartilhados encapsulam controles, estilos e plugins. No exemplo, `onProcessingChange` comunica o processamento do formulário à página para desabilitar também o Cancelar do topo.

`StudentPage`, `StudentForm`, seus helpers e suas classes CSS pertencem a alunos. Em outro cadastro, componha os componentes compartilhados adequados; não importe componentes de alunos apenas para obter seu visual, nem faça componentes dependerem de páginas.

## Classificação das orientações

| Classe | O que significa | Exemplos |
| --- | --- | --- |
| Obrigatório | Padrão estrutural, visual ou de integração para novas telas de formulário, salvo instrução explícita diferente | Cabeçalho compartilhado, card único, ações à direita, labels e erros associados, componentes existentes, contratos do backend |
| Condicional | Aplica-se somente quando a funcionalidade existe no cadastro | Foto de pessoa, calendário, editor de texto rico, máscaras, progresso de upload, método de atualização |
| Particularidade do exemplo | Referência de uma solução local, sem obrigatoriedade para outros cadastros | Campos de alunos, duas seções específicas, foto à esquerda, rotas de alunos, status apenas na edição |

## Padrões obrigatórios

### Estrutura e composição

1. Use o layout compartilhado existente. `main.tsx` atribui `AppLayout` às páginas de negócio; ele monta topbar, sidebar e footer para usuários autenticados. Não duplique essa estrutura dentro do formulário.
2. Defina o título do documento com `Head` do Inertia e componha o cabeçalho visual com `Navigation/PageTitle`. Ele recebe `title`, `subtitle` e `actions`, usa `h1` por padrão e oferece quebra responsiva. A referência visual é o título maior e roxo do antigo Blade de criar aluno. `Navigation/PageHeader` continua disponível nos exemplos Ubold, mas não é o padrão oficial desses cabeçalhos de negócio.
3. Use `Navigation/PageBreadcrumb` dentro de uma navegação com nome acessível. Passe a cadeia de navegação real; o último item é apresentado como atual. Links permanecem azuis, com hover/foco azul escuro, conforme seu CSS compartilhado.
4. Mantenha um `<form>` semântico, com `onSubmit`, prevenção do submit nativo e indicação de processamento. Dentro dele, reúna os campos em um único `UI/CardBox`.
5. Divida o conteúdo com `Forms/FormSection`, usando `variant="inline"`, título pertinente e `iconClass` adequado. A variante padrão `card` cria outro card: não a use inadvertidamente para cada seção de um formulário que deve ter um só card.
6. Coloque `Forms/FormActions` depois do card, ainda dentro do `<form>`, para salvar/cancelar à direita e apresentar o status de envio. Preserve a navegação de Cancelar. Quando houver uma ação também no cabeçalho, sincronize sua desabilitação com o processamento, como no exemplo.

As seções atuais têm título em maiúsculas, ícone decorativo e divisor cinza claro de 2 px abaixo do título. O conteúdo e o início da linha ficam alinhados no padding do card. Não reproduza esse cabeçalho com markup e CSS próprios por tela.

### Biblioteca interna de componentes

Os caminhos desta tabela são relativos a `resources/react/components/`.

| Necessidade | Componente e utilização |
| --- | --- |
| Título, subtítulo e ações no topo | `Navigation/PageTitle/PageTitle.tsx`; passe as ações por `actions` |
| Breadcrumbs | `Navigation/PageBreadcrumb/PageBreadcrumb.tsx`; passe `items` com labels e destinos reais |
| Card do formulário | `UI/CardBox/CardBox.tsx` |
| Seções dentro do mesmo card | `Forms/FormSection/FormSection.tsx`; `variant="inline"`, `id`, `title`, `iconClass` |
| Label, obrigatório, dica e erro | `Forms/FormField/FormField.tsx`; associe o controle ao mesmo `id` |
| Texto e atributos nativos | `Forms/TextInput/TextInput.tsx`; valores controlados e `invalid` |
| Texto simples com várias linhas | `Forms/TextArea/TextArea.tsx`, quando o contrato não requer texto rico |
| Seleção | `Forms/SelectField/SelectField.tsx`; `options`, `value`, `onChange`, `label`, `id`, `name` |
| Data de calendário | `Forms/DatePicker/DatePicker.tsx`, quando aplicável ao formato de data esperado |
| Foto de pessoa | `Forms/PersonPhotoUpload/PersonPhotoUpload.tsx`, quando o cadastro precisar dessa informação |
| Texto rico | `Forms/SummernoteEditor/SummernoteEditor.tsx`, quando houver requisito de editor |
| Ações finais e andamento | `Forms/FormActions/FormActions.tsx`; `children` e `status` |
| Botões e links de ação | `UI/Button/Button.tsx`; variantes `save`/`cancel`, ícones e `disabled` |
| Falha geral ou resumo de validação | `UI/InlineAlert/InlineAlert.tsx`, dentro de uma região de anúncio apropriada |

Use imports diretos com `@/` entre pastas e `./` para arquivos locais. Leia os tipos reais antes de compor o controle. Estenda o componente existente quando houver uma necessidade comprovada; não crie um clone, uma biblioteca de formulário ou outro adaptador de plugin por página.

### Identidade visual e organização dos campos

As fontes e cores compartilhadas estão em `resources/react/styles/tokens.css`, que importa as variáveis de cores de `resources/css/variables.css`. O CSS de cada componente define sua aparência; o CSS do proprietário do formulário define apenas a composição necessária.

| Elemento | Padrão atual a reutilizar |
| --- | --- |
| Título e subtítulo de página | `PageTitle`: `--ui-font-gnai-heading`, `--ui-font-gnai-body`, `--ui-color-page-title` e `--ui-color-page-subtitle` |
| Labels | Roxo escuro de `--ui-color-form-label`; não usar o roxo claro do foco como cor permanente do label |
| Títulos e ícones das seções | Cinza de `--ui-color-heading`, pelo CSS de `FormSection` |
| Entradas | `TextInput`: borda de `--ui-color-input-border`, foco de `--ui-color-input-focus-border`, mesma espessura e transição do componente |
| Select2 | Controle com borda/foco dos inputs; painel e busca com bordas cinza; opções destacadas em `--ui-color-gnai-primary` |
| Calendário | Botão e seleção roxos, com os tokens Gnai e estilos do `DatePicker` |
| Summernote | Toolbar neutra e estados ativos roxos, definidos no componente |
| Erros de campo | `FormField`, estado inválido do controle e tokens de erro; erro não pode depender somente de cor |
| Salvar e cancelar | `Button` com `tone="save"`/`tone="cancel"`; verde e cinza do Blade via aliases de cores e ícones automáticos |

`--ui-color-primary` e `--ui-color-custom` ainda representam cores do tema em outros componentes. Não use esses nomes como se já fossem os tokens oficiais roxos do Gnai, nem consolide toda a paleta durante uma migração de formulário.

Organize campos relacionados em grids que aproveitem a largura disponível e reduzam colunas em telas menores. Campos longos, como o endereço com editor, ocupam a linha completa. Preserve padding, bordas, foco e espaçamentos compartilhados; não copie dimensões locais de alunos como uma regra global.

Botões devem ter ícone pertinente e texto, salvo pedido explícito diferente. `Button` admite `leftIconClass` e outras posições de ícone, `showIcon={false}` para a exceção solicitada e `iconOnly` com `aria-label` para ações só com ícone. Ações ficam à direita, exceto quando o usuário definir outra posição. Não suponha que todas as variantes do tema já tenham as cores do Gnai.

### Labels, validação e acessibilidade

`FormField` gera o label e os IDs `${id}-hint`/`${id}-error`, mas não injeta esses atributos automaticamente no filho. Passe o mesmo `id` ao controle, indique o estado inválido e ligue o erro/dica por `aria-describedby` ou pela prop equivalente do componente. `requiredIndicator` apresenta o marcador visual; `required` no controle é uma decisão separada, conforme o contrato real.

Trecho do padrão usado em `StudentForm`, em um contexto com `form` inicializado por `useForm`:

```tsx
import FormField from '@/components/Forms/FormField/FormField';
import TextInput from '@/components/Forms/TextInput/TextInput';

<FormField id="student-name" label="Nome Completo" error={form.errors.name} requiredIndicator>
  <TextInput id="student-name" name="name" required value={form.data.name}
    invalid={!!form.errors.name}
    aria-describedby={form.errors.name ? 'student-name-error' : undefined}
    onChange={event => form.setData('name', event.target.value)} />
</FormField>
```

O nome e o ID acima pertencem ao exemplo; adapte-os ao cadastro. Use hierarquia de headings, navegação nomeada, alternativas para imagens e foco visível. Erros específicos permanecem junto aos campos; um resumo geral pode complementar, sem substituí-los. No exemplo, o resumo fica em `role="alert"` e o primeiro controle inválido recebe tentativa de foco após a resposta de validação.

### Estado e submissão

- Use `useForm` de `@inertiajs/react`, como na referência, com um tipo de dados próprio do cadastro. Inicialize campos vazios, valores padrão recebidos do backend e dados existentes de edição de maneira explícita. Não invente defaults de enums ou dados de negócio.
- Atualize valores com `form.setData`; use a versão funcional para mudanças relacionadas, como arquivo e indicador de remoção. Não esconda o valor enviado em estado interno desconectado do formulário.
- Apresente `form.errors` junto dos campos. `form.setError` registra também falhas locais de controles, e `form.clearErrors` limpa os erros pertinentes quando o usuário corrige ou remove o valor, conforme o comportamento existente.
- Configure `form.transform` somente para diferenças reais de payload. Criação e edição podem ter contratos distintos: não envie automaticamente todos os campos do estado nos dois casos.
- Use o método e o endpoint previstos nas rotas. O exemplo combina `form.post`, `forceFormData: true`, `preserveScroll: true` e `_method: 'put'` na edição porque contém upload. Multipart e method spoofing são condicionais; não obrigue formulários sem arquivos a adotá-los por cópia.
- Durante o envio, sinalize `aria-busy`, bloqueie campos e ações apropriados e impeça submits duplicados. Alunos usam `fieldset disabled`, props `disabled` dos adaptadores e uma trava em `useRef`, liberada em `onFinish`. O Cancelar do topo recebe `form.processing` por callback. Não confie apenas no `fieldset` para widgets com elementos gerados fora dele.
- Mantenha mensagens como “Salvando...” e status compreensíveis. `FormActions.status` anuncia o andamento; `form.progress` é usado quando houver upload. Falhas devem permitir corrigir e tentar novamente.

As falhas de conexão e de resposta HTTP são apresentadas por `useStudentRequest` e `InlineAlert` na referência. Esse hook é do domínio de alunos: não o copie nem o importe indiscriminadamente em outros cadastros. Reutilize o transporte de feedback adequado e, se houver necessidade real de compartilhamento, trate-a no escopo autorizado.

## Padrões condicionais

### Selects

Quando houver seleção de opções, use `SelectField` com Select2 habilitado por padrão. Passe `options` como `{ value, label }[]`, valores controlados e `onChange`, sem títulos de grupo ou repetição do label no menu. O placeholder padrão é “Selecione”; filtros podem exigir “Todos”, mas isso não define o placeholder de todo formulário. Preserve valores do backend, inclusive zero e string vazia quando tiverem significado distinto.

O componente sincroniza seleção e desabilitação com os controles gerados pelo plugin, encaminha as alterações para o React e mantém associações de erro. Se o plugin não carregar, informa a falha e mantém a seleção nativa disponível. Não inicialize Select2 manualmente na página.

### Datas e máscaras

Quando o campo representar uma data compatível, use `DatePicker` com `id`, `name`, `label`, `value` e `onChange`. Ele exibe e mascara `DD/MM/AAAA`, recebe/emite datas completas em `AAAA-MM-DD` e permite que entradas incompletas cheguem à validação existente, sem criar uma regra de calendário diferente. Associe o erro por `invalid` e `describedBy`. A falha no calendário mantém a digitação disponível e apresenta feedback.

O Date Picker não é um controle de hora ou de intervalo. Não aplique datas de nascimento, limites de idade ou relacionamento entre datas a outro cadastro por cópia do exemplo.

CPF e telefone são mascarados por helpers locais de alunos. Só use máscaras quando o campo e o contrato exigirem; confira se o backend recebe o texto mascarado ou normaliza os dígitos. A máscara não substitui validação.

### Foto de pessoa

Use `PersonPhotoUpload` exclusivamente quando o cadastro precisar de foto de pessoa. A API real está em `PersonPhotoUpload.types.ts`: o pai controla `file`, `current`, `removed`, erros e envio, com callbacks `onChange`, `onRemove`, `onError` e, quando necessário, `onValidatingChange`.

O componente preserva o círculo do Blade, aceita clique e arrastar/soltar, apresenta preview de arquivo ou foto existente e ação de remoção. A validação local confere formato, tamanho e leitura da imagem; URLs temporárias e leituras pendentes são liberadas. Seleção e remoção não fazem upload automático.

JPG/PNG e 2 MB são os defaults que atendem a alunos. `acceptedTypes` e `maxSizeBytes` devem corresponder ao backend do cadastro que o reutilizar; não universalize esses limites. O estado do pai determina se uma remoção significa apenas descartar um arquivo novo ou também remover a foto persistida. Bloqueie o envio enquanto a validação local da imagem estiver pendente.

### Editor de texto rico

Quando o requisito pedir editor, use `SummernoteEditor`; não transforme todo campo de texto em editor. Ele aceita HTML inicial por `defaultValue`, lido uma vez na montagem, e entrega HTML por `onChange`. Use o valor inicial seguro que o backend disponibiliza e atualize o campo correspondente em `form.setData`.

No modo padrão com `id`, o conteúdo editável recebe `${id}-input`; o `FormField` do endereço de alunos usa esse ID. Passe `disabled`, `invalid`, `describedBy` e `onLoadError`, conforme necessário. Preserve a toolbar completa padrão, sem reduzi-la por iniciativa própria. A altura é uma decisão contextual, não um tamanho obrigatório para todos os editores.

A referência bloqueia Salvar após falha de carregamento do editor e apresenta erro no campo. Não declare HTML recebido como seguro por ter vindo do editor: respeite o tratamento do backend. `StudentPagePresenter` disponibiliza `address_html` por `RichTextSanitizer`; a toolbar oferece recursos que não são todos preservados pelo sanitizador, como imagens e atributos de estilo. Essa diferença precisa ser considerada no contrato do campo, sem ampliar as regras do servidor durante uma migração visual.

Plugins usam os assets locais de `public/assets/react/`, com adaptadores que carregam suas dependências e limpam listeners/instâncias ao desmontar. Não importe Bootstrap global, não reinicialize os plugins por tela e não instale bibliotecas equivalentes sem necessidade comprovada.

## Particularidades do formulário de alunos

- “Dados Pessoais” e “Dados Acadêmicos”, seus ícones e campos são do domínio de alunos; outros cadastros devem definir seus próprios agrupamentos.
- A foto ocupa uma coluna estreita à esquerda, e os seis campos pessoais ficam à direita: nome/e-mail na primeira linha e CPF/nascimento/gênero/telefone na seguinte, quando houver largura. O endereço ocupa toda a linha abaixo das duas colunas.
- A composição e os respiros especiais dessa área estão em `Students.css`, com media queries e container queries. As medidas da coluna, distâncias e quantidade de campos não são uma grade obrigatória para outros formulários, especialmente os que não têm foto.
- O status aparece apenas na edição. `StudentForm` não envia `status` nem `remove_photo` na criação. Os defaults de gênero/status vêm das props preparadas para cada operação.
- `Create` cancela para a listagem; `Edit` cancela para o prontuário. Ambas mantêm Cancelar no cabeçalho e no final do formulário. Preserve o destino e as ações do cadastro de origem em vez de impor essas rotas a outros módulos.
- A edição usa `key={student.id}` para reinicializar o formulário/editor quando o registro muda. Não espere que `SummernoteEditor.defaultValue` acompanhe cada alteração de props como um campo controlado.

## Integração com o backend e feedback

Para alunos, consulte estes arquivos reais; em outra tela, localize os equivalentes do respectivo domínio:

| Contrato | Referência |
| --- | --- |
| Endpoints e middlewares `can:*` | `routes/modules/specialized-educational-support.php`, com prefixo em `routes/web.php` |
| Páginas Inertia, operações e redirects | `app/Domains/SpecializedEducationalSupport/UI/Controllers/StudentController.php` |
| Opções e defaults de criação/edição | `app/Domains/SpecializedEducationalSupport/Application/Queries/Students/StudentFormQuery.php` |
| Dados de apresentação e permissões | `app/Domains/SpecializedEducationalSupport/UI/Presenters/StudentPagePresenter.php` |
| Nomes, tipos e validações | `app/Domains/SpecializedEducationalSupport/Application/Data/Students/CreateStudentData.php` e `UpdateStudentData.php` |
| Normalização de entrada de pessoa | `app/Domains/SpecializedEducationalSupport/Application/Data/People/Concerns/NormalizesPersonInput.php` |
| Dados compartilhados e flash | `app/Shared/Infrastructure/Http/Middleware/HandleInertiaRequests.php` |

A referência cria por POST em `/specialized-educational-support/students/store` e atualiza por PUT em `/specialized-educational-support/students/{id}`, via POST multipart com `_method`. O controller redireciona para o prontuário com mensagem de sucesso. Essas URLs e destinos são exemplos, não uma convenção de URLs obrigatória para novos módulos.

Preserve nomes de campos, enums, formato das datas, arquivos e flags aceitos pelo backend. Não substitua validações, permissões, queries/actions ou regras de negócio por condições visuais. Os erros de validação chegam por Inertia em `form.errors`; os redirects e flash devem seguir os contratos existentes. Não acrescente capabilities ao usuário compartilhado nem invente permissões que o servidor não fornece.

**Atenção ao consumo de flash na referência atual:** `AppLayout` monta `UI/NotificationToast/FlashNotifications.tsx`, enquanto `StudentPage` monta outro consumidor em `UI/FlashNotifications/FlashNotifications.tsx`. Essa sobreposição pode gerar notificações duplicadas e não deve ser reproduzida como padrão. Ao compor um novo formulário, confira o consumidor já montado e use uma única apresentação de resultado. A existência de ambos no exemplo é uma particularidade a considerar, não motivo para criar outro toast ou refatorar o layout fora do escopo.

## Processo de criação ou migração

1. Leia esta convenção e as instruções `AGENTS.md` aplicáveis antes de implementar.
2. Releia `Create`, `Edit`, `StudentPage`, `StudentForm` e seus componentes compartilhados para verificar o padrão atual; não presuma que uma nota antiga sobre scaffolds descreve essas páginas hoje.
3. Localize a biblioteca interna e confira as APIs, CSS e hooks dos controles necessários.
4. Em migrações, inspecione o Blade de origem, partials, componentes, estilos e scripts. Inventarie campos, defaults, condições, máscaras, uploads, ações, erros, loading e mensagens.
5. Rastreie rotas, controllers, Data/Rules, Queries/Presenters, permissões e redirects. Identifique diferenças entre criar e editar antes de definir o payload.
6. Componha a nova página e o formulário seguindo os padrões obrigatórios desta convenção, aplicando os componentes condicionais somente quando o cadastro exigir.
7. Reutilize os componentes compartilhados e mantenha tipos, hooks, utilitários e CSS específicos com seu proprietário. Use páginas `pages/Dominio/Recurso/Acao.tsx`, componentes `.tsx` e lógica/tipos sem markup em `.ts`.
8. Preserve integração e regras de negócio. Aplique a apresentação React padronizada sem copiar mecanicamente o visual do Blade ou criar padrões paralelos.
9. Compare o código com o inventário funcional, revise o diff e confirme campos, submissão, edição, remoção de foto quando aplicável, mensagens, foco e responsividade. Corrija divergências no escopo autorizado.
10. Entregue um resumo curto dos arquivos e comportamentos implementados. A homologação visual/funcional é manual; não afirme que houve execução quando a revisão foi apenas estática.

Siga os limites de execução do `AGENTS.md`: não crie ou execute testes, build, typecheck, scripts de homologação, servidores, navegador, banco temporário ou infraestrutura automaticamente. Essas ações dependem de solicitação explícita ou de uma tarefa cujo objetivo seja validar o ambiente.

## Manutenção da convenção

Quando uma alteração autorizada redefinir o padrão compartilhado de formulários, atualize este documento e os links do índice no `AGENTS.md`, se os caminhos mudarem. Verifique o código real antes de atualizar exemplos e APIs. Uma nova tela que segue o mesmo padrão não exige uma nova convenção.

Outras convenções podem ser adicionadas a `docs/ai-conventions/` e indexadas no `AGENTS.md` conforme existirem. Não crie documentos vazios para planejar essa expansão. A documentação de domínio já existente em `Docs/` permanece separada; evite duplicar seus contratos nesta convenção.
