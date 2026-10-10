# Extras

As 11 rotas `extra-*.html` usam React com TypeScript. `Extras` apenas
orquestra componentes, com rotas em `Extras.data.ts`. A interface e os
modelos de e-mail estão em pt-BR; nomes próprios, URLs, imagens originais
e os textos de exemplo em latim foram preservados.

## Verificações por tela

| Tela | Componentes e comportamento conferidos |
| --- | --- |
| Perfil | Avatar original, informações pessoais, biografia traduzida, atividades, mapa externo e colunas no breakpoint de 992 px. Barras de habilidades esperam entrar na janela, com margem de 50 px e animação de 5 s. A detecção de dispositivos móveis conserva o `mobile: false` do WOW original. |
| Linha do tempo | Ícones, cores, datas, imagem, setas dos cards e alternância de lados. Itens abaixo de 75% da altura da janela começam ocultos; o scroll revela ícone e card uma vez, com bounce de 0,6 s. A ocultação e a animação se aplicam a partir de 1170 px, como no CSS original. |
| Mapa do site | Grupos e destinos originais, colunas a partir de 992 px e cores no hover. |
| Fatura | Logo, endereços, status, itens e totais originais. Scroll horizontal fica dentro da tabela no mobile; ações são ocultadas na impressão. |
| Modelos de e-mail | Três imagens originais de prévia; links abrem os documentos traduzidos em outra aba. O HTML dos documentos é um asset de e-mail, independente das páginas React. As imagens de prévia conservam os textos embutidos originais. |
| Manutenção | Fundo e ícone originais, título com as três cores, centralização, breakpoints e ausência de navegação lateral. |
| Em breve | Mesmo fundo, texto rotativo com a fila, easing e tempos de fade do legado, contador atualizado a cada segundo e cores por unidade. O prazo original de novembro de 2016 já venceu: o contador permanece zerado. |
| Perguntas frequentes | Oito perguntas sempre abertas, duas colunas a partir de 992 px, bordas e tipografia. O legado não possui accordion nessa tela. |
| Resultados da busca | Campo e botão, três abas, badges, seis resultados, cinco usuários e seis parágrafos na última aba. Abas mostram ícones abaixo de 768 px, com nomes acessíveis em pt-BR. Paginação conserva a página demonstrativa ativa. |
| Galeria | Doze imagens originais, cinco filtros, redistribuição animada com o Isotope original, hover dos filtros e popup Magnific original. Abertura, navegação pelas setas, contador, legenda, clique na imagem e Esc foram conferidos. |
| Planos e preços | Duas apresentações distintas, cores e ícones originais, efeitos Waves e grid de uma, duas ou quatro colunas nos breakpoints de 768 e 1200 px. |

A chave `cd-bounce-2-inverse` era referenciada pelo CSS do legado, mas não
possuía definição em `core.css`. A versão React completa essa entrada
espelhando o deslocamento dos cards da esquerda, com o mesmo tempo e escala.

## Componentização e ciclo de vida

Cards, botões, imagens, badges, progressbars, tabela, panel, abas e breadcrumb
existentes são reutilizados. `Pagination` foi extraída de UI Bootstrap, e
os estilos do Magnific foram movidos para `ImageLightbox`, compartilhado
com a tabela editável. `Panel` permite omitir o cabeçalho, e as abas e os
objetos de mídia aceitam conteúdo React tipado nos rótulos.

`useScrollReveal` concentra o comportamento de entrada da timeline e das
habilidades. Listeners, frames, intervalos, animações e instâncias de plugins
são encerrados ao sair da tela. O loader continua montado ao trocar entre
páginas com navegação e telas independentes.

Os JS/CSS de Isotope e Magnific ficam em `public/assets/plugins/`. Eles são dependências locais dos componentes e permanecem versionáveis.

## Limites dos exemplos originais

O botão de busca conserva os resultados estáticos. Paginação, ações dos
planos, links de atividades e ações da fatura continuam demonstrativos,
conforme o HTML legado. Eles não implementam serviços, cobrança ou pesquisa
remota. O mapa mantém o endereço externo original, usando HTTPS.
