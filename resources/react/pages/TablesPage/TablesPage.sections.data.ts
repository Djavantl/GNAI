export const tablePageTitles: Record<string, string> = {
  basic: 'Tabelas básicas',
  datatable: 'DataTables',
  editable: 'Tabelas editáveis',
  responsive: 'Tabela responsiva',
  'foo-tables': 'FooTable',
  bootstrap: 'Bootstrap Table',
};
export const basicSections = [
  {
    title: 'Exemplo básico',
    description:
      'Para um estilo básico, com espaçamento leve e divisórias horizontais, adicione `.table` a qualquer `<table>`.',
  },
  {
    title: 'Linhas alternadas',
    description:
      'Use `.table-striped` para alternar as cores das linhas em `<tbody>`.',
  },
  {
    title: 'Tabela com bordas',
    description:
      'Adicione `.table-bordered` para bordas em todos os lados da tabela e das células.',
  },
  {
    title: 'Destaque ao passar o mouse',
    description:
      'Adicione `.table-hover` para destacar as linhas de `<tbody>` ao passar o mouse.',
  },
  {
    title: 'Tabelas responsivas',
    description:
      'Envolva qualquer `.table` em `.table-responsive` para rolar horizontalmente em dispositivos pequenos (abaixo de 768 px).',
  },
  {
    title: 'Tabela compacta',
    description:
      'Adicione `.table-condensed` para compactar a tabela, reduzindo o espaçamento das células pela metade.',
  },
  {
    title: 'Classes contextuais',
    description:
      'Use cores contextuais para destacar linhas ou células individuais.',
  },
];
export const bootstrapSections = [
  ['Colunas básicas', 'Exemplo de colunas básicas.'],
  ['Seleção múltipla', 'Selecione linhas com caixas de seleção.'],
  ['Seleção única', 'Selecione uma linha por vez.'],
  [
    'Ordenação e formatação',
    'Ordene os dados e personalize a apresentação das colunas.',
  ],
  [
    'Barra de ferramentas',
    'Busque registros, atualize a tabela e escolha as colunas visíveis.',
  ],
  ['Barra personalizada', 'Selecione registros para removê-los da tabela.'],
];
export const fooSections = [
  ['Filtragem', 'Inclua filtros na sua FooTable.'],
  [
    'Adicionar e remover linhas',
    'Adicione novas linhas ou remova registros da tabela.',
  ],
  [
    'Expansão de linhas',
    'Clique em uma linha para exibir suas informações ocultas.',
  ],
  ['Paginação', 'Inclua paginação na sua FooTable.'],
  ['Acordeão', 'Expanda uma linha por vez para visualizar os detalhes.'],
];
