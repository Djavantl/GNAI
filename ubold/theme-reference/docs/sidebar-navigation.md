# Navegação em múltiplos níveis

Multi Level é uma demonstração de navegação na sidebar da aplicação React. Não possui página própria nem destinos de domínio. A hierarquia original foi preservada em pt-BR:

- Nível de menu 1.1, com Nível de menu 2.1, 2.2 e 2.3;
- Nível de menu 1.2, sem filhos.

## Organização

`SidebarPrimary.multilevel.data.ts` contém essa configuração. Os contratos compartilhados estão em `SidebarPrimary.types.ts`; `useSidebarBranches` e `toggleSidebarBranch` concentram o estado e o fechamento dos irmãos. A árvore continua usando os componentes existentes `SidebarMenu` e `SidebarMenuItem`.

`SidebarSubmenu` foi extraído para pasta própria, com CSS local e `useSidebarSubmenuHeight`. Após a abertura de 350 ms, o limite de altura é removido, como a limpeza de `slideDown` do legado. Assim, abrir um filho faz o pai crescer sem cortar suas opções. O fechamento parte da altura atual para preservar a transição.

## Comportamento preservado e diferenças corrigidas

- Abrir 1.1 revela os três itens do segundo nível. Selecionar 1.2 fecha o ramo 1.1; selecionar uma folha marca essa opção e limpa a seleção de seus irmãos. Fechar e reabrir o pai reinicia seu ramo.
- Os itens demonstrativos não mudam URL, hash ou conteúdo principal.
- Mantidos os recuos de 65 e 80 px, texto de 14 px, destaque verde, borda esquerda de 3 px e fundo do estado aberto. O menu pequeno mantém seu recuo original de 20 px.
- Restaurado `waves-effect waves-light` em 1.1, incluindo sua apresentação no menu recolhido. O ícone original `ti-share` foi mantido; não foram acrescentadas setas que não existem na referência.
- No menu recolhido, o primeiro painel tem 190 px. O segundo usa o deslocamento de 190 px, margem superior de -36 px, padding superior de 5 px e borda de 2 px do legado. Foram corrigidos o posicionamento e a sobrescrita desses valores pelo reset de listas.
- Passar do ícone para o título projetado mantém o painel aberto, assim como passar do primeiro para o segundo nível. Sair do conjunto fecha os painéis.
- Recolher a sidebar mantém o ramo aberto e a seleção, inclusive o destaque nos painéis flutuantes. Reabrir restaura os dois níveis sem exibir brevemente os outros ramos fechados.
- O recorte horizontal do `body` foi alinhado ao `core.css` original para que o submenu não crie rolagem horizontal no celular. Como na referência, uma parte do painel lateral pode ficar fora da tela quando a sidebar está recolhida; abrir a sidebar disponibiliza a árvore em coluna.
- Os submenus ocultos ficam fora do foco de teclado; os ramos expõem seus estados e relações por `aria-expanded` e `aria-controls`. Abertura e seleção por Enter e percurso por Tab foram conferidos.

A navegação usa os componentes e estilos da aplicação React.
