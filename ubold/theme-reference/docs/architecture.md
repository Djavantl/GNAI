# Arquitetura do frontend React

A organização separa a composição da aplicação, as telas e os componentes reutilizáveis, preservando a identidade visual e as URLs existentes. O repositório contém somente a aplicação React; os assets e plugins usados por ela ficam em `public/`.

```text
react-app/
  src/
    main.tsx
    app/
      App.tsx
      App.css
      routes.ts
      routes.data.ts
      useSidebarState.ts
    pages/
      Ecommerce/
      EcommerceDashboard/
      Crm/
      TabsPage/
      ...
    components/
      Button/
        Button.tsx
        Button.css
      ActivityTimeline/
        ActivityTimeline.tsx
        ActivityTimeline.css
        ActivityTimeline.data.ts
        ActivityTimeline.types.ts
      ...
    hooks/
      useChart.ts
      useChart.types.ts
      useCustomScrollbar.ts
      usePathname.ts
      ...
    utils/
      chartRuntime.ts
      carouselInteraction.ts
      loadExternalScript.ts
      ...
    types/
      BusinessRecord.types.ts
    styles/
      reset.css
      tokens.css
      globals.css
      icons.css
  public/assets/
  scripts/check-architecture.mjs
  deploy/nginx.conf
  docs/
```

## Responsabilidades

| Pasta | Responsabilidade |
| --- | --- |
| `app` | Composição geral, resolução das URLs, política de sidebar e estilos do shell. |
| `pages` | Telas e seções específicas. As páginas orquestram componentes e seus dados. Famílias como CRM e Extras podem compor outras telas da mesma camada. |
| `components` | UI reutilizável e seus estilos, tipos, dados de demonstração e integrações locais com plugins. |
| `hooks` | Comportamento React compartilhado, sem dependência de telas ou componentes visuais. |
| `utils` | Helpers e adaptadores sem hooks React. |
| `types` | Contratos de domínio realmente compartilhados entre componentes e telas. |
| `styles` | Reset, tokens extraídos do legado, estilos globais e fontes de ícones. |
| `public/assets` | Assets e bibliotecas originais. Os nomes e URLs desses arquivos são preservados. |

Componentes não importam páginas nem módulos de `app`. Hooks compartilhados não importam UI. Utilitários e tipos de domínio não dependem das camadas superiores. Essas regras são verificadas pelo build.

## Nomes e imports

- Pastas de telas e componentes usam **PascalCase** e possuem uma entrada com o mesmo nome: `Button/Button.tsx`.
- JSX fica em `.tsx`; lógica, dados e tipos ficam em `.ts`.
- Dados e configurações usam `Nome.data.ts`; tipos compartilhados usam `Nome.types.ts`; seções de uma tela usam `Nome.sections.tsx`.
- Conteúdo que precisa de JSX pode ficar em `Nome.content.tsx`, como as descrições formatadas de FormAdvanced.
- Estilos ficam junto do proprietário: `Nome.css`. Estilos de plugins locais usam `Nome.plugin.css`.
- Hooks usam `useNome.ts`. Um hook usado somente pelo seu componente pode continuar na pasta desse componente; quando compartilhado, fica em `hooks/`.
- Helpers em `utils/` usam camelCase. Nomes em minúsculas nessas camadas e em `styles/` são intencionais.
- Imports da mesma pasta usam `./Arquivo`; imports entre pastas usam `@/`, que aponta para `src/` no Vite e no TypeScript.
- Não há um barrel global exportando toda a aplicação. Os imports apontam para o arquivo responsável, explicitando as dependências.

## Reutilização

Antes de criar um componente, procure o equivalente em `components/`. Para reutilizar em outro projeto, leve sua pasta, os componentes/hooks/helpers que ele importa, os tokens e os assets que ele utiliza. Configure o alias `@/` no projeto de destino. Evite copiar a tela inteira quando ela apenas demonstra o componente.

Tipos locais continuam próximos do componente; contratos de domínio só vão para `types/` quando são usados em diferentes partes da aplicação. Por exemplo, cards de leads e oportunidades usam `BusinessRecord.types.ts` e não precisam importar a página de CRM.

Os adaptadores de plugins legados preservam o carregamento dos scripts originais e o ciclo de montagem/limpeza. Algumas APIs dessas bibliotecas ainda têm tipagem dinâmica, localizada nos adaptadores; esta organização não substitui os plugins ou seus comportamentos.

## Validar uma alteração

1. Investigue a implementação React existente, seus estilos, estados e dependências.
2. Escolha a camada conforme a responsabilidade e mantenha estilos/dados próximos do proprietário.
3. Rode `npm run check` e `npm run build`.
4. Verifique a aplicação em desktop e mobile, incluindo estados, animações, navegação e limpeza dos plugins ao trocar de tela. Compare com o estado anterior à alteração para detectar regressões.

O script de arquitetura verifica capitalização, colisões de nomes, entradas dos componentes, arquivos de dados/tipos, resolução dos imports e dependências entre camadas. O TypeScript valida os contratos em modo estrito. Essas verificações não substituem a revisão visual e comportamental da aplicação.

## Verificação desta organização

- Os 665 arquivos de origem foram preservados ou extraídos para seus novos responsáveis.
- A comparação das 9.222 regras de CSS e do JSX de 253 módulos confirmou a preservação dos estilos e da estrutura das telas. Os caminhos dos imports foram normalizados.
- As 99 URLs do menu foram verificadas contra os identificadores de navegação, incluindo caminhos com prefixo. A resolução das rotas foi comparada com a implementação anterior em 168 casos.
- Os 26 diagnósticos de TypeScript anteriores foram corrigidos; o build agora exige a checagem global sem erros.
- No navegador, foram verificados scroll por teclado e arraste, preloader com fontes pendentes, navegação rápida, modal, larguras de 375 e 1440 pixels, páginas independentes e telas sem rolagem.
- O build servido por Nginx carregou CRM, Tabs, FormAdvanced, C3, Timeline, produtos, calendário e login, sem erros de console ou imagens quebradas. As URLs diretas responderam e assets inexistentes retornaram 404.
- A comparação direta com o build anterior não encontrou diferenças de coordenadas, dimensões, fontes, cores, espaçamentos ou bordas em Tabs, FormAdvanced e edição de produtos em 1440 pixels, nem em Tabs e edição de produtos em 375 pixels.
