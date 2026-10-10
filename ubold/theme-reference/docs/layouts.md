# Layouts

As quatro rotas `layout-*.html` têm implementações React próprias. Textos de interface estão em pt-BR; nome do usuário, avatar e identidade visual permanecem originais.

## Rotas

| Rota | Variação preservada |
| --- | --- |
| `layout-leftbar_2.html` | Sidebar de 240 px com avatar de 48 px, John Doe, cargo Administrador e dropdown de Perfil/Configurações/Tela de bloqueio/Sair. O bloco do usuário desaparece ao recolher o menu. |
| `layout-menu-collapsed.html` | Sidebar inicialmente recolhida em 70 px, independentemente da preferência das outras telas. Permite abrir pelo botão e navegar pelos submenus no hover. |
| `layout-menu-small.html` | Sidebar e área do logo de 180 px, textos centralizados, ícones de 18 px acima das legendas e submenus com padding de 20 px. Recolhida, usa o mesmo menu de 70 px do legado. |
| `layout-header_2.html` | Cabeçalho de página com fundo `#e3e7ec`, borda `#dee2e8`, margem superior de -25 px, padding de 25 px e margem inferior de 22 px. Não altera o desenho da topbar. |

Essas telas possuem somente título, breadcrumb e espaço demonstrativo de pelo menos 1000 px no legado. Esse conteúdo, inclusive a posição do rodapé, foi mantido.

## Organização e comportamento

- `Layouts` orquestra o `PageHeader` reutilizado e o espaço demonstrativo. Configurações e tipos ficam em `Layouts.data.ts` e `Layouts.types.ts`.
- `SidebarPrimary` e `TopBarPrimary` recebem props simples para o menu pequeno. Seu comportamento padrão continua disponível para as outras páginas.
- `SidebarUser` tem dados, tipos e CSS locais. Reutiliza `ImageDisplay` para a imagem circular de tamanho médio.
- `useDropdownMenu` concentra abertura, fechamento por clique externo e navegação por setas/Escape. É compartilhado entre o menu do usuário e `DropdownMenu`, incluindo os dropdowns dos aplicativos.
- `PageHeader` mantém a API de título/subtítulo dos dashboards e acrescenta breadcrumb e a variante visual de cabeçalho.
- `useLayoutSidebar` segue o breakpoint de 990 px. O menu recolhido começa com estado forçado; nos demais layouts o tamanho da janela define o estado inicial. Após clicar no botão, a escolha permanece durante a visita, inclusive ao redimensionar, como a classe `forced` do legado.
- Cada rota de Layouts reinicia a demonstração correspondente. A navegação pelos links retorna ao topo, como o carregamento da página original; o botão Voltar pode restaurar a rolagem pelo histórico do navegador. A navegação não grava esses estados na preferência `bold.sidebarCollapsed` das outras páginas.
- A sidebar aberta permanece fixa e pode rolar internamente; recolhida, fica posicionada em relação à página e acompanha sua rolagem. A topbar continua fixa. O bloco do usuário não fica visível no menu recolhido.
- O recolhimento não acrescenta animação de largura. Os submenus reutilizam a transição de 350 ms; o scrollbar segue os tempos `fast`/`slow` de 200/600 ms do SlimScroll original.
- No celular, abrir o menu desloca o conteúdo como no legado. O logo completo volta a aparecer a partir de 420 px quando o menu está aberto; abaixo disso o ícone permanece compacto.

## Limites da demonstração

As ações do dropdown do usuário são placeholders no HTML original. Foram preservadas sem criar autenticação, alteração de perfil ou mensagens falsas de execução. A tradução pode mudar as quebras de linha dos títulos, breadcrumbs e opções em telas estreitas.
