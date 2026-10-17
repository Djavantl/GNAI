# Listagens React

As listagens de negócio usam os componentes de `resources/react/components/Tables/TableList/` para manter o mesmo card, densidade de tabela, estado vazio e rodapé. As telas de Alunos e Tecnologias Assistivas são as referências integradas atuais.

A moldura da página usa `Navigation/BusinessPage`, que reúne título do documento, breadcrumb, `PageTitle`, ações, flash e erro geral. Wrappers do domínio, como `StudentPage` e `AssistiveTechnologyPage`, montam apenas os breadcrumbs e particularidades da funcionalidade.

Use `TableListCreateButton` para a ação superior “+ Cadastrar”, `TableListCard` como container da listagem, `TableListProgress` para quantidade ou processamento, `TableListScroll` para a região rolável, `TableListTable` para a tabela, `TableListEmpty` para o estado vazio e `TableListFooter` para resumo e paginação. Ações compactas de linha usam `Tables/TableActions`.

O CSS do `TableList` concentra borda do card, espaçamentos comuns, cabeçalho e células, alinhamento das ações, tamanho da contagem, paginação e comportamento responsivo. Mantenha no CSS da tela apenas diferenças reais, como o grid dos filtros, larguras mínimas e proporcionais das colunas, avatar ou conteúdo específico.

Filtros que recarregam automaticamente a listagem usam `hooks/useAutoListFilters`, não exibem botão Filtrar e usam `TableListFilterActions` com `TableListClearButton`. O hook centraliza valores, sincronização, debounce, limpeza e cancelamento da busca agendada; cada domínio continua responsável pelo request Inertia, mensagens e contratos. Use `compactListFilters` para retirar valores vazios antes do request.

Ações destrutivas usam `UI/ConfirmActionModal`, que centraliza a apresentação custombox do Ubold, com cabeçalho grafite e conteúdo centralizado, além de foco, mensagens de erro, estado de processamento e ações do rodapé. A funcionalidade mantém apenas o estado de abertura e a requisição do domínio; não use `window.confirm` nem replique o markup do modal na página.

CSS de campos, larguras de coluna e conteúdo específico fica junto de `StudentFilters`, `StudentList` ou do equivalente do domínio. Não concentre novamente estilos de indexação em um arquivo geral que também atende formulários e prontuários.
