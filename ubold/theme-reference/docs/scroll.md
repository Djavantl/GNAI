# Rolagem e preloader

O preloader aplicava `overflow: hidden` a `html` e `body` e removia o bloqueio após o fade. A barra nativa do documento desaparecia durante esse intervalo; em ambientes onde ela ocupa espaço, isso altera a largura disponível. Como a barra era do viewport inteiro, ela também passava ao lado da top bar.

A gravação mostrou ainda duas trocas de medidas de textos/radios durante o carregamento das fontes. Na captura realizada, elas ocorreram com o preloader opaco. Havia também uma disputa entre o preloader inicial e o de navegação: navegar durante o fade inicial permitia que o primeiro liberasse a rolagem enquanto o segundo ainda aguardava prontidão.

## Correções

- `PageScrollbar` acompanha a rolagem nativa do documento, com trilho fixo abaixo da altura real da top bar. Mantém os 6 px, cor `#98a6ad` e raio de 5 px dos estilos existentes, sem reservar espaço no layout.
- A barra nativa do documento é ocultada desde o CSS inicial de `index.html`, antes da montagem do React. Barras internas de tabelas, sidebar, dropdowns e modais continuam usando seus componentes existentes.
- Preloaders aguardam `document.fonts.ready` antes do fade e só liberam o bloqueio quando ambos os overlays foram removidos. Promessas de prontidão de uma navegação anterior não fecham o overlay da seguinte.
- Arraste, clique no trilho, setas, Page Up/Down, Home/End e informações de acessibilidade foram implementados na barra. A rolagem permanece em `window.scrollY`, preservando os listeners e plugins existentes.
- Sem top bar, o trilho começa em zero; páginas sem excesso de conteúdo dispensam a barra. O tamanho é recalculado ao redimensionar, mudar de rota ou carregar conteúdo.
