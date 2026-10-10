# CRM

As cinco rotas do CRM agora renderizam componentes React com TypeScript. A interface está em pt-BR; nomes próprios, empresas, e-mails, datas, quantidades e dados dos gráficos foram mantidos.

| Rota | Tela | Conteúdo migrado |
| --- | --- | --- |
| `crm-dashboard.html` | Dashboard | Três indicadores com Peity, análise anual, barras, donut, contatos e atividades |
| `crm-contact.html` | Contatos | Busca, formulário, tabela com 14 registros, seleção, ações, ficha de Bill Bertz e oito atividades |
| `crm-opportunities.html` | Oportunidades | Busca, controles de ordenação, cinco cartões, formulário e gráfico de status |
| `crm-leads.html` | Leads | Busca, sete cartões com os logos originais, formulário, barras empilhadas e oito atividades |
| `crm-customers.html` | Clientes | Busca, formulário e tabela com os mesmos 14 registros do legado |

## Organização

`Crm` orquestra as telas e define suas rotas. As seções ficam em `CrmDashboard`, `CrmContacts`, `CrmOpportunities` e `CrmLeads`. Dados e configurações ficam em arquivos `.data.ts`; os tipos compartilhados, em `.types.ts`.

Os elementos reutilizáveis incluem `DealStatCard`, `ChartLegend`, `PeopleTable`, `RecordActions`, `ContactSearch`, `CrmToolbar`, `CrmEntryForm`, `OpportunityCard`, `LeadCard`, `ContactActivityCard` e `ScrollArea`. Foram reutilizados os componentes existentes de botões, inputs, checkboxes, tabelas, labels, imagens, dropdowns, modais e histórico de atividades.

## Detalhes preservados

- Grid do legado, incluindo gutters de 20 px, divisão das colunas em 1200 px e tabelas com rolagem horizontal abaixo de 768 px.
- Primeiro registro marcado e destacado; seleção independente das linhas e do checkbox do cabeçalho, como no template.
- Cores originais dos estados e botões: ações dos leads em azul, e-mail do contato em roxo, edição em verde e ligação em rosa. Ícones de editar/excluir ficam verdes no hover.
- Ordenação demonstrativa com três checkboxes independentes e sombra interna no estado ativo.
- Modais com fadein, overlay de 200 ms, transição de 500 ms, largura de 600 px e margem de 5% por lado quando a largura da janela é de até 660 px. Os campos e suas posições seguem cada formulário original.
- Peity, Morris/Raphael e C3/D3 originais, com os mesmos valores, cores, dimensões, hover, tooltips e interações das legendas. Textos e dias da semana foram traduzidos; percentuais do C3 usam vírgula decimal.
- Histórico compartilhado com os círculos e a linha originais, timestamps em tamanho reduzido e NiceScroll com cursor `#98a6ad`, largura de 6 px e raio de 5 px. O hook limpa o plugin e os observadores na troca de tela.
- Os cinco logos em `public/assets/images/brand/` e `jquery.nicescroll.js` foram copiados do legado; seus hashes SHA-256 são idênticos aos originais.

O template apresenta recorte horizontal dos cartões de negócios em larguras pequenas. Esse comportamento foi preservado. As traduções podem alterar quebras de linha: em 375 px, o subtítulo e o título de vendas por grupo ocupam mais linhas; os cabeçalhos traduzidos também alteram a largura intrínseca das tabelas.

## Comportamento demonstrativo

As telas são demonstrativas e não implementam uma API de CRM. Busca, ordenação, salvar, editar, excluir, atribuir, ligar e enviar e-mail não persistem dados nem executam operações externas. A seleção, os estados dos controles, os modais, as legendas e a rolagem funcionam no frontend. Salvar mantém o formulário aberto; Cancelar, Escape, o botão de fechar e o backdrop permitem fechá-lo.
