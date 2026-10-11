# Desenvolvimento React

Esta pasta contém a base React/TypeScript do sistema Laravel.

Antes de criar, refatorar, migrar ou modificar telas de formulários, consulte [a convenção de formulários React](../../docs/ai-conventions/frontend/form-pages.md), indexada no `AGENTS.md` da raiz. Use o formulário atual de alunos como referência estrutural e visual, preservando os contratos do cadastro trabalhado. Atualize a convenção quando uma mudança autorizada alterar o padrão compartilhado.

- Preserve estilos, tokens, responsividade e comportamento dos componentes existentes, observando a identidade Gnai e os limites documentados no `AGENTS.md` da raiz; aparência demonstrativa não define a marca do produto.
- Use `components/{UI,Forms,Navigation,Tables}/PascalCase/PascalCase.tsx` e `pages/PascalCase/`.
- CSS, dados, hooks locais e tipos específicos ficam junto de seu proprietário.
- Imports entre pastas usam `@/`; imports locais usam `./`.
- Componentes não dependem de páginas ou app. Hooks compartilhados não dependem de UI.
- Utilitários e tipos não dependem das camadas superiores.
- Assets e plugins do tema ficam em `public/assets/react`; assets próprios do Gnai ficam em `public/images/`. Consulte o inventário da raiz antes de escolher logo/banner/imagens; preserve licenças e dependências transitivas.
- Não importe o CSS global do Bootstrap na entrada React.
- Mantenha a limpeza dos adaptadores de plugins ao desmontar componentes.
- Interface em pt-BR. Dados do catálogo são demonstrativos.
- Para títulos/subtítulos de páginas de negócio, reutilize `Navigation/PageTitle` no padrão antigo do Blade de criar aluno: título maior e roxo escuro, subtítulo cinza e `actions` à direita. O componente mantém `h1` por padrão e suporta quebra responsiva; fontes e cores estão nos tokens. `Navigation/PageHeader` permanece como referência Ubold dos exemplos existentes. Breadcrumbs seguem `Navigation/PageBreadcrumb`, com links azuis e hover/foco em azul escuro, conforme o tema. Labels dos campos são roxo escuro; títulos e ícones das seções são cinza, usando os tokens compartilhados.
- Siga as convenções de formulários e ações do `AGENTS.md` da raiz: `SelectField`/Select2 roxo com opções sem grupos; um card por formulário com seções `FormSection variant="inline"`, ícones e divisores; campos em grids responsivos; ações à direita via `FormActions`; botões com ícone e texto por padrão e variantes `save`/`cancel` nas cores do Blade. Exceções de ícone/posição dependem do pedido do usuário. Reutilize esses componentes em vez de reproduzir os estilos por tela.
- Use `Forms/PersonPhotoUpload` para fotos de pessoas, preservando o design Blade, clique e arrastar/soltar. O formulário controla arquivo, remoção e envio; confira formatos e limite no backend do cadastro. No Select2, o controle tem foco roxo como os inputs, mas busca e painel de opções mantêm bordas cinza.
- Em migrações comuns, investigue o legado e os contratos, reutilize componentes, implemente, ajuste as integrações necessárias, revise o diff e entregue. A homologação visual e funcional, inclusive desktop, mobile e interações, será feita manualmente pelo usuário.
- `npm run check` e `npm run build` na raiz, ou pelo serviço Docker `node`, são referências técnicas. Execute-os somente quando o usuário solicitar explicitamente ou quando a própria tarefa tiver como objetivo testar/validar o ambiente.
- Não crie, altere ou execute testes, mocks, scripts temporários ou infraestrutura de homologação automaticamente. Não prepare SQLite, fixtures ou bancos temporários, não inicie servidores/Docker e não abra navegador, acesse URLs, gere screenshots ou execute o catálogo React apenas para conferir a migração. Consulte o código do catálogo e dos testes existentes como documentação quando necessário.
- A inspeção de código, a paridade funcional, os contratos reais, o feedback, a acessibilidade e a responsividade continuam obrigatórios. A execução de homologação segue os limites do `AGENTS.md` da raiz.
- Controllers de telas retornam Inertia e páginas em `resources/react/pages`; não crie respostas HTML parciais.
- Páginas Inertia usam `pages/Dominio/Recurso/Acao.tsx` e o identificador `Dominio/Recurso/Acao`.
- Os templates antigos de telas ficam preservados apenas como referência; não são uma alternativa de renderização.
