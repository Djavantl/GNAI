# Migração do e-commerce

As cinco rotas do e-commerce renderizam componentes React com TypeScript. Os textos de interface estão em pt-BR; marcas, referências, datas, preços, quantidades, imagens e valores dos gráficos vêm do legado.

| Rota | Conteúdo |
| --- | --- |
| `ecommerce-dashboard.html` | Quatro indicadores, receita por ano, cinco barras animadas, pedidos recentes e produtos populares |
| `ecommerce-products.html` | Doze cartões, cinco categorias, filtros animados, avaliação, estoque, ações no hover e galeria |
| `ecommerce-product-detail.html` | Dados gerais, categorias com Select2, status, metadados, quatro imagens, funções das imagens e seleção de arquivo |
| `ecommerce-orders.html` | Busca, seleção visual exclusiva da ordenação, quinze pedidos e ações |
| `ecommerce-sellers.html` | Busca, formulário de vendedor, quatorze registros, contagens de produtos, seleção e dropdown |

## Referências e componentes

Os componentes React preservam as cores, fontes, espaços, dados e comportamento do Bold, utilizando assets e plugins locais em `public/assets/`.

`Ecommerce` identifica a rota e orquestra `EcommerceDashboard`, `ProductCatalog`, `ProductEditor`, `EcommerceOrders` e `EcommerceSellers`. Configurações e dados ficam nos respectivos arquivos `.data.ts`; tipos compartilhados ficam em `.types.ts`.

Foram reutilizados os indicadores e a seção de receita do Dashboard 2, `ChartLegend`, Morris/Raphael, `PeopleTable`, `RecordActions`, `ContactSearch`, o formulário/modal de cadastro de pessoas, `Button`, `ChoiceControl`, `FormField`, `ImageDisplay`, `TableLabel` e `ImageLightbox`.

Os novos componentes reutilizáveis incluem `GalleryFilter`, `SortButtonGroup`, `StarRating`, `SelectField`, `TextArea`, `FileUploadButton`, `ProductCard`, `ProductOrdersTable` e `ProductFormSection`. `useIsotopeLayout` concentra a montagem, filtros e desmontagem do plugin original, compartilhado pelo catálogo e pela galeria de Extras. O CRM usa o mesmo grupo de ordenação, mantendo seus checkboxes independentes; Pedidos usa radios.

## Fidelidade e comportamento

- Grid de 20 px entre colunas, indicadores em duas colunas a partir de 768 px e quatro em 1200 px; gráfico/progresso divididos em 992 px; formulário e tabelas inferiores divididos em 1200 px.
- Catálogo com duas, três e quatro colunas em 768, 992 e 1200 px. Abaixo de 768 px, os cartões seguem a largura automática do legado, sem impor largura de 100%.
- Mesmos arquivos de produto; as oito imagens grandes copiadas têm SHA-256 idêntico ao legado. Avaliações completas, meias estrelas, preços e estoque foram preservados em cada registro.
- Mesmos Isotope e Magnific Popup, incluindo filtros, transições, todos os doze itens da galeria, setas e Escape. Textos do lightbox estão em pt-BR.
- Mesmo Select2, incluindo agrupamentos, pesquisa, seleção por teclado, estilos e destruição do plugin ao sair da página. Mensagens de busca e acessibilidade estão em pt-BR.
- Gráfico de área com as três séries originais entre 2009 e 2015. Barras com 80%, 50%, 70%, 65% e 40%, cores originais e animação de cinco segundos. Os indicadores numéricos do e-commerce são estáticos no legado.
- Formulário com estrelas de obrigatoriedade visuais, alturas de textarea do legado (mínimo de 90 px), miniaturas fixas de 48 × 48 px, radios de status e grupos independentes de funções das imagens. O seletor de arquivo permanece nativo.
- O editor inicia com status Online e funções das quatro imagens em principal/miniatura/galeria/principal, como no original. Vendedores inicia com a primeira linha destacada e marcada; seleção do cabeçalho é independente das linhas. Dropdown e ações reutilizam os componentes corrigidos na revisão do CRM.
- Badge azul de “Em processamento”, cores dos outros estados, padding das ações, fonte Noto Sans de “Ordenar por”, fundos de hover/focus/active dos botões brancos e dos controles de ordenação extraídos do CSS original.

A tradução pode acrescentar quebras de linha e alterar a largura intrínseca das tabelas em celular. Na primeira carga mobile do legado, o Isotope pode medir tamanhos provisórios e sobrepor os cartões. Em React, a inicialização aguarda a decodificação das imagens; a largura dos cartões e o layout após recalcular os filtros seguem o legado com as imagens estabilizadas.

## Limites do template

As telas de e-commerce são demonstrativas e não possuem API. Busca, ordenação, salvar, excluir, editar e os links de produtos são controles demonstrativos; a migração não inventa persistência, processamento de pedidos ou envio de arquivos. O arquivo pode ser selecionado, sem upload para servidor. O formulário de vendedor reproduz os três campos e a transição do modal já usados no cadastro de pessoas.
