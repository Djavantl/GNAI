# AGENTS.md

## Objetivo e estado do projeto

Este repositório contém somente o frontend **Bold em React e TypeScript**, localizado em `react-app/`.

A aplicação HTML independente, os arquivos de distribuição originais e seu serviço Docker foram removidos por solicitação explícita do usuário. Não recriar uma segunda aplicação nem exigir sua execução para desenvolver ou validar o React.

A interface existente do React é a referência visual e comportamental. Refatorar a tecnologia e a organização preservando a identidade visual; não executar redesign sem solicitação explícita.

## Fidelidade visual e comportamental

Preservar cores, fontes, tamanhos, espaçamentos, bordas, sombras, ícones, imagens, alinhamentos, proporções, hierarquia, breakpoints e responsividade.

Preservar estados de hover, focus, active, disabled e loading, transições, animações, menus, dropdowns, modais, tooltips, sliders, accordions, abas e navegação. Avaliar interações e estados além de uma captura estática.

Antes de implementar uma mudança, investigar o componente React existente, seus estilos, hooks, helpers, bibliotecas, assets e consumidores. Comparar com o estado anterior à alteração em desktop e mobile para detectar regressões.

Não inventar uma nova paleta ou um novo design system. Valores globais de identidade pertencem aos tokens existentes; valores exclusivos de um componente ficam junto dele.

## Estrutura e responsabilidades

```text
react-app/src/
  main.tsx
  app/
  pages/
  components/
  hooks/
  utils/
  types/
  styles/
```

- `app/`: composição geral, navegação, política de sidebar e estilos do shell.
- `pages/PascalCase/`: telas e seções específicas; páginas orquestram componentes.
- `components/PascalCase/`: componentes reutilizáveis, isolados e com responsabilidade clara.
- `hooks/`: comportamento React compartilhado.
- `utils/`: lógica auxiliar e adaptadores sem hooks React.
- `types/`: contratos de domínio realmente compartilhados.
- `styles/`: reset, tokens, estilos globais e fontes de ícones.

A arquitetura detalhada está em `react-app/docs/architecture.md`.

## Componentização e nomenclatura

- Utilizar React com TypeScript e props, estados, funções e retornos tipados quando aplicável.
- JSX fica em `.tsx`; dados, tipos, hooks e helpers ficam em `.ts`.
- Pastas de telas e componentes usam PascalCase e contêm uma entrada com o mesmo nome, como `Button/Button.tsx`.
- CSS específico fica junto ao proprietário, em `Nome.css` ou `Nome.module.css`.
- Dados/configurações usam `Nome.data.ts`; tipos compartilhados usam `Nome.types.ts`.
- Seções específicas podem usar `Nome.sections.tsx`; conteúdo que exige JSX pode usar `Nome.content.tsx`.
- Hooks usam `useNome.ts`; hooks locais permanecem junto ao proprietário até haver reutilização real.
- Helpers em `utils/` usam camelCase.
- Imports na mesma pasta usam `./Arquivo`; imports entre pastas usam `@/`, configurado para `src/`.
- Componentes não dependem de páginas ou módulos de `app`.
- Hooks compartilhados não dependem de componentes visuais ou telas.
- Utilitários e tipos de domínio não dependem das camadas superiores.

Não criar abstrações prematuras ou componentes excessivamente configuráveis para eliminar pequenas duplicações. Reutilizar componentes existentes quando houver equivalência real, sem comprometer a fidelidade visual.

Subcomponentes reconhecíveis e repetidos, como badge, avatar, botão, input, alerta, modal, tabela, dropdown, tooltip e carousel, devem ser tratados explicitamente quando fizer sentido. Não substituir um componente compartilhável por CSS local improvisado.

## Assets e plugins

Imagens, fontes, ícones, modelos de e-mail e bibliotecas utilizadas pelo React ficam em `react-app/public/`. Esses arquivos fazem parte das dependências da aplicação e devem funcionar sem pastas externas ao React.

Não remover ou substituir assets e plugins ativos apenas por sua idade ou origem. Preservar URLs, licenças e comportamento dos adaptadores. Auditar dependências transitivas e arquivos carregados dinamicamente antes de excluir qualquer arquivo de `public/`.

Não adicionar Bootstrap como dependência ou importar seu CSS global sem solicitação explícita. Alguns componentes possuem adaptadores para bibliotecas existentes; manter montagem, atualização e desmontagem corretas, incluindo limpeza de listeners, timers, observers e elementos auxiliares.

## Escopo

Modificar somente arquivos necessários à tarefa. Não alterar identidade visual, textos, nomenclaturas de domínio, fluxos, regras ou assets sem necessidade ou solicitação explícita. Manter os textos de interface em pt-BR.

Não remover código React funcional ou bibliotecas utilizadas sem autorização. A remoção da aplicação HTML independente já foi autorizada e concluída; essa restrição não deve ser usada para exigir sua restauração.

## Validação e execução

Na pasta `react-app/`:

```bash
npm ci
npm run check
npm run build
```

`check` valida a arquitetura e o TypeScript global em modo estrito. `build` executa essas verificações antes de gerar `dist/`.

Desenvolvimento local: `npm run dev`. Pela raiz: `docker compose up --build`, com somente o serviço React na porta 5173. A imagem estática de produção usa `react-app/Dockerfile.production` e `react-app/deploy/nginx.conf`.

Executar verificações proporcionais à alteração e revisar a interface em diferentes larguras. Validar URLs diretas, navegação, estados visuais e interações relevantes. O resultado esperado é uma aplicação React organizada, reutilizável e com a identidade visual e o comportamento existentes preservados.
