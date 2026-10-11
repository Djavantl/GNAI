import type { TableDefinition } from '@/components/Tables/Table/Table.types';
export const tableExamples: Record<string, TableDefinition[]> = {
  basic: [
    {
      attributes: { class: 'table m-0' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Nome', attributes: {} },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Usuário', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Mark', attributes: {} },
            { tag: 'td', text: 'Otto', attributes: {} },
            { tag: 'td', text: '@mdo', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Jacob', attributes: {} },
            { tag: 'td', text: 'Thornton', attributes: {} },
            { tag: 'td', text: '@fat', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Larry', attributes: {} },
            { tag: 'td', text: 'the Bird', attributes: {} },
            { tag: 'td', text: '@twitter', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: { class: 'table table-striped m-0' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Nome', attributes: {} },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Usuário', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Mark', attributes: {} },
            { tag: 'td', text: 'Otto', attributes: {} },
            { tag: 'td', text: '@mdo', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Jacob', attributes: {} },
            { tag: 'td', text: 'Thornton', attributes: {} },
            { tag: 'td', text: '@fat', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Larry', attributes: {} },
            { tag: 'td', text: 'the Bird', attributes: {} },
            { tag: 'td', text: '@twitter', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: { class: 'table table-bordered m-0' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Nome', attributes: {} },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Usuário', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Mark', attributes: {} },
            { tag: 'td', text: 'Otto', attributes: {} },
            { tag: 'td', text: '@mdo', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Jacob', attributes: {} },
            { tag: 'td', text: 'Thornton', attributes: {} },
            { tag: 'td', text: '@fat', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Larry', attributes: {} },
            { tag: 'td', text: 'the Bird', attributes: {} },
            { tag: 'td', text: '@twitter', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: { class: 'table table table-hover m-0' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Nome', attributes: {} },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Usuário', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Mark', attributes: {} },
            { tag: 'td', text: 'Otto', attributes: {} },
            { tag: 'td', text: '@mdo', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Jacob', attributes: {} },
            { tag: 'td', text: 'Thornton', attributes: {} },
            { tag: 'td', text: '@fat', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Larry', attributes: {} },
            { tag: 'td', text: 'the Bird', attributes: {} },
            { tag: 'td', text: '@twitter', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: { class: 'table m-0' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Cabeçalho', attributes: {} },
          { text: 'Cabeçalho', attributes: {} },
          { text: 'Cabeçalho', attributes: {} },
          { text: 'Cabeçalho', attributes: {} },
          { text: 'Cabeçalho', attributes: {} },
          { text: 'Cabeçalho', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
            { tag: 'td', text: 'Célula', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: { class: 'table table-condensed m-0' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Nome', attributes: {} },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Usuário', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Mark', attributes: {} },
            { tag: 'td', text: 'Otto', attributes: {} },
            { tag: 'td', text: '@mdo', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Jacob', attributes: {} },
            { tag: 'td', text: 'Thornton', attributes: {} },
            { tag: 'td', text: '@fat', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Larry', attributes: {} },
            { tag: 'td', text: 'the Bird', attributes: {} },
            { tag: 'td', text: '@twitter', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '4', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Mark', attributes: {} },
            { tag: 'td', text: 'Otto', attributes: {} },
            { tag: 'td', text: '@mdo', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'th', text: '5', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Jacob', attributes: {} },
            { tag: 'td', text: 'Thornton', attributes: {} },
            { tag: 'td', text: '@fat', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: { class: 'table' },
      head: [
        [
          { text: '#', attributes: {} },
          { text: 'Cabeçalho da coluna', attributes: {} },
          { text: 'Cabeçalho da coluna', attributes: {} },
          { text: 'Cabeçalho da coluna', attributes: {} },
        ],
      ],
      body: [
        {
          className: 'active',
          cells: [
            { tag: 'th', text: '1', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
          ],
        },
        {
          className: 'success',
          cells: [
            { tag: 'th', text: '2', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
          ],
        },
        {
          className: 'info',
          cells: [
            { tag: 'th', text: '3', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
          ],
        },
        {
          className: 'warning',
          cells: [
            { tag: 'th', text: '4', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
          ],
        },
        {
          className: 'danger',
          cells: [
            { tag: 'th', text: '5', attributes: { scope: 'row' } },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
            { tag: 'td', text: 'Conteúdo da coluna', attributes: {} },
          ],
        },
      ],
    },
  ],
  datatable: [
    {
      attributes: {
        id: 'datatable',
        class: 'table table-striped table-bordered',
      },
      head: [
        [
          { text: 'Nome', attributes: {} },
          { text: 'Cargo', attributes: {} },
          { text: 'Escritório', attributes: {} },
          { text: 'Idade', attributes: {} },
          { text: 'Admissão', attributes: {} },
          { text: 'Salário', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Tiger Nixon', attributes: {} },
            { tag: 'td', text: 'Arquiteto de sistemas', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '61', attributes: {} },
            { tag: 'td', text: '2011/04/25', attributes: {} },
            { tag: 'td', text: '$320,800', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Garrett Winters', attributes: {} },
            { tag: 'td', text: 'Contador', attributes: {} },
            { tag: 'td', text: 'Tokyo', attributes: {} },
            { tag: 'td', text: '63', attributes: {} },
            { tag: 'td', text: '2011/07/25', attributes: {} },
            { tag: 'td', text: '$170,750', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Ashton Cox', attributes: {} },
            { tag: 'td', text: 'Redator técnico júnior', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '66', attributes: {} },
            { tag: 'td', text: '2009/01/12', attributes: {} },
            { tag: 'td', text: '$86,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Cedric Kelly', attributes: {} },
            {
              tag: 'td',
              text: 'Desenvolvedor JavaScript sênior',
              attributes: {},
            },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '22', attributes: {} },
            { tag: 'td', text: '2012/03/29', attributes: {} },
            { tag: 'td', text: '$433,060', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Airi Satou', attributes: {} },
            { tag: 'td', text: 'Contador', attributes: {} },
            { tag: 'td', text: 'Tokyo', attributes: {} },
            { tag: 'td', text: '33', attributes: {} },
            { tag: 'td', text: '2008/11/28', attributes: {} },
            { tag: 'td', text: '$162,700', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Brielle Williamson', attributes: {} },
            { tag: 'td', text: 'Especialista em integração', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '61', attributes: {} },
            { tag: 'td', text: '2012/12/02', attributes: {} },
            { tag: 'td', text: '$372,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Herrod Chandler', attributes: {} },
            { tag: 'td', text: 'Assistente de vendas', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '59', attributes: {} },
            { tag: 'td', text: '2012/08/06', attributes: {} },
            { tag: 'td', text: '$137,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Rhona Davidson', attributes: {} },
            { tag: 'td', text: 'Especialista em integração', attributes: {} },
            { tag: 'td', text: 'Tokyo', attributes: {} },
            { tag: 'td', text: '55', attributes: {} },
            { tag: 'td', text: '2010/10/14', attributes: {} },
            { tag: 'td', text: '$327,900', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Colleen Hurst', attributes: {} },
            { tag: 'td', text: 'Desenvolvedor JavaScript', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '39', attributes: {} },
            { tag: 'td', text: '2009/09/15', attributes: {} },
            { tag: 'td', text: '$205,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Sonya Frost', attributes: {} },
            { tag: 'td', text: 'Engenheiro de software', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '23', attributes: {} },
            { tag: 'td', text: '2008/12/13', attributes: {} },
            { tag: 'td', text: '$103,600', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Jena Gaines', attributes: {} },
            { tag: 'td', text: 'Gerente de escritório', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '30', attributes: {} },
            { tag: 'td', text: '2008/12/19', attributes: {} },
            { tag: 'td', text: '$90,560', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Quinn Flynn', attributes: {} },
            { tag: 'td', text: 'Líder de suporte', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '22', attributes: {} },
            { tag: 'td', text: '2013/03/03', attributes: {} },
            { tag: 'td', text: '$342,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Charde Marshall', attributes: {} },
            { tag: 'td', text: 'Diretor regional', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '36', attributes: {} },
            { tag: 'td', text: '2008/10/16', attributes: {} },
            { tag: 'td', text: '$470,600', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Haley Kennedy', attributes: {} },
            { tag: 'td', text: 'Designer de marketing sênior', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '43', attributes: {} },
            { tag: 'td', text: '2012/12/18', attributes: {} },
            { tag: 'td', text: '$313,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Tatyana Fitzpatrick', attributes: {} },
            { tag: 'td', text: 'Diretor regional', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '19', attributes: {} },
            { tag: 'td', text: '2010/03/17', attributes: {} },
            { tag: 'td', text: '$385,750', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Michael Silva', attributes: {} },
            { tag: 'td', text: 'Designer de marketing', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '66', attributes: {} },
            { tag: 'td', text: '2012/11/27', attributes: {} },
            { tag: 'td', text: '$198,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Paul Byrd', attributes: {} },
            { tag: 'td', text: 'Diretor financeiro (CFO)', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '64', attributes: {} },
            { tag: 'td', text: '2010/06/09', attributes: {} },
            { tag: 'td', text: '$725,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Gloria Little', attributes: {} },
            { tag: 'td', text: 'Administrador de sistemas', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '59', attributes: {} },
            { tag: 'td', text: '2009/04/10', attributes: {} },
            { tag: 'td', text: '$237,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Bradley Greer', attributes: {} },
            { tag: 'td', text: 'Engenheiro de software', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '41', attributes: {} },
            { tag: 'td', text: '2012/10/13', attributes: {} },
            { tag: 'td', text: '$132,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Dai Rios', attributes: {} },
            { tag: 'td', text: 'Líder de pessoal', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '35', attributes: {} },
            { tag: 'td', text: '2012/09/26', attributes: {} },
            { tag: 'td', text: '$217,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Jenette Caldwell', attributes: {} },
            { tag: 'td', text: 'Líder de desenvolvimento', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '30', attributes: {} },
            { tag: 'td', text: '2011/09/03', attributes: {} },
            { tag: 'td', text: '$345,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Yuri Berry', attributes: {} },
            { tag: 'td', text: 'Diretor de marketing (CMO)', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '40', attributes: {} },
            { tag: 'td', text: '2009/06/25', attributes: {} },
            { tag: 'td', text: '$675,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Caesar Vance', attributes: {} },
            { tag: 'td', text: 'Suporte pré-venda', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '21', attributes: {} },
            { tag: 'td', text: '2011/12/12', attributes: {} },
            { tag: 'td', text: '$106,450', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Doris Wilder', attributes: {} },
            { tag: 'td', text: 'Assistente de vendas', attributes: {} },
            { tag: 'td', text: 'Sidney', attributes: {} },
            { tag: 'td', text: '23', attributes: {} },
            { tag: 'td', text: '2010/09/20', attributes: {} },
            { tag: 'td', text: '$85,600', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Angelica Ramos', attributes: {} },
            { tag: 'td', text: 'Diretor executivo (CEO)', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '47', attributes: {} },
            { tag: 'td', text: '2009/10/09', attributes: {} },
            { tag: 'td', text: '$1,200,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Gavin Joyce', attributes: {} },
            { tag: 'td', text: 'Desenvolvedor', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '42', attributes: {} },
            { tag: 'td', text: '2010/12/22', attributes: {} },
            { tag: 'td', text: '$92,575', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Jennifer Chang', attributes: {} },
            { tag: 'td', text: 'Diretor regional', attributes: {} },
            { tag: 'td', text: 'Singapore', attributes: {} },
            { tag: 'td', text: '28', attributes: {} },
            { tag: 'td', text: '2010/11/14', attributes: {} },
            { tag: 'td', text: '$357,650', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Brenden Wagner', attributes: {} },
            { tag: 'td', text: 'Engenheiro de software', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '28', attributes: {} },
            { tag: 'td', text: '2011/06/07', attributes: {} },
            { tag: 'td', text: '$206,850', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Fiona Green', attributes: {} },
            { tag: 'td', text: 'Diretor de operações (COO)', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '48', attributes: {} },
            { tag: 'td', text: '2010/03/11', attributes: {} },
            { tag: 'td', text: '$850,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shou Itou', attributes: {} },
            { tag: 'td', text: 'Marketing regional', attributes: {} },
            { tag: 'td', text: 'Tokyo', attributes: {} },
            { tag: 'td', text: '20', attributes: {} },
            { tag: 'td', text: '2011/08/14', attributes: {} },
            { tag: 'td', text: '$163,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Michelle House', attributes: {} },
            { tag: 'td', text: 'Especialista em integração', attributes: {} },
            { tag: 'td', text: 'Sidney', attributes: {} },
            { tag: 'td', text: '37', attributes: {} },
            { tag: 'td', text: '2011/06/02', attributes: {} },
            { tag: 'td', text: '$95,400', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Suki Burks', attributes: {} },
            { tag: 'td', text: 'Desenvolvedor', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '53', attributes: {} },
            { tag: 'td', text: '2009/10/22', attributes: {} },
            { tag: 'td', text: '$114,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Prescott Bartlett', attributes: {} },
            { tag: 'td', text: 'Redator técnico', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '27', attributes: {} },
            { tag: 'td', text: '2011/05/07', attributes: {} },
            { tag: 'td', text: '$145,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Gavin Cortez', attributes: {} },
            { tag: 'td', text: 'Líder de equipe', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '22', attributes: {} },
            { tag: 'td', text: '2008/10/26', attributes: {} },
            { tag: 'td', text: '$235,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Martena Mccray', attributes: {} },
            { tag: 'td', text: 'Suporte pós-venda', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '46', attributes: {} },
            { tag: 'td', text: '2011/03/09', attributes: {} },
            { tag: 'td', text: '$324,050', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Unity Butler', attributes: {} },
            { tag: 'td', text: 'Designer de marketing', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '47', attributes: {} },
            { tag: 'td', text: '2009/12/09', attributes: {} },
            { tag: 'td', text: '$85,675', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Howard Hatfield', attributes: {} },
            { tag: 'td', text: 'Gerente de escritório', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '51', attributes: {} },
            { tag: 'td', text: '2008/12/16', attributes: {} },
            { tag: 'td', text: '$164,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Hope Fuentes', attributes: {} },
            { tag: 'td', text: 'Secretário', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '41', attributes: {} },
            { tag: 'td', text: '2010/02/12', attributes: {} },
            { tag: 'td', text: '$109,850', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Vivian Harrell', attributes: {} },
            { tag: 'td', text: 'Controlador financeiro', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '62', attributes: {} },
            { tag: 'td', text: '2009/02/14', attributes: {} },
            { tag: 'td', text: '$452,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Timothy Mooney', attributes: {} },
            { tag: 'td', text: 'Gerente de escritório', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '37', attributes: {} },
            { tag: 'td', text: '2008/12/11', attributes: {} },
            { tag: 'td', text: '$136,200', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Jackson Bradshaw', attributes: {} },
            { tag: 'td', text: 'Diretor', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '65', attributes: {} },
            { tag: 'td', text: '2008/09/26', attributes: {} },
            { tag: 'td', text: '$645,750', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Olivia Liang', attributes: {} },
            { tag: 'td', text: 'Engenheiro de suporte', attributes: {} },
            { tag: 'td', text: 'Singapore', attributes: {} },
            { tag: 'td', text: '64', attributes: {} },
            { tag: 'td', text: '2011/02/03', attributes: {} },
            { tag: 'td', text: '$234,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Bruno Nash', attributes: {} },
            { tag: 'td', text: 'Engenheiro de software', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '38', attributes: {} },
            { tag: 'td', text: '2011/05/03', attributes: {} },
            { tag: 'td', text: '$163,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Sakura Yamamoto', attributes: {} },
            { tag: 'td', text: 'Engenheiro de suporte', attributes: {} },
            { tag: 'td', text: 'Tokyo', attributes: {} },
            { tag: 'td', text: '37', attributes: {} },
            { tag: 'td', text: '2009/08/19', attributes: {} },
            { tag: 'td', text: '$139,575', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Thor Walton', attributes: {} },
            { tag: 'td', text: 'Desenvolvedor', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '61', attributes: {} },
            { tag: 'td', text: '2013/08/11', attributes: {} },
            { tag: 'td', text: '$98,540', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Finn Camacho', attributes: {} },
            { tag: 'td', text: 'Engenheiro de suporte', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '47', attributes: {} },
            { tag: 'td', text: '2009/07/07', attributes: {} },
            { tag: 'td', text: '$87,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Serge Baldwin', attributes: {} },
            { tag: 'td', text: 'Coordenador de dados', attributes: {} },
            { tag: 'td', text: 'Singapore', attributes: {} },
            { tag: 'td', text: '64', attributes: {} },
            { tag: 'td', text: '2012/04/09', attributes: {} },
            { tag: 'td', text: '$138,575', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Zenaida Frank', attributes: {} },
            { tag: 'td', text: 'Engenheiro de software', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '63', attributes: {} },
            { tag: 'td', text: '2010/01/04', attributes: {} },
            { tag: 'td', text: '$125,250', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Zorita Serrano', attributes: {} },
            { tag: 'td', text: 'Engenheiro de software', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '56', attributes: {} },
            { tag: 'td', text: '2012/06/01', attributes: {} },
            { tag: 'td', text: '$115,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Jennifer Acosta', attributes: {} },
            {
              tag: 'td',
              text: 'Desenvolvedor JavaScript júnior',
              attributes: {},
            },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '43', attributes: {} },
            { tag: 'td', text: '2013/02/01', attributes: {} },
            { tag: 'td', text: '$75,650', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Cara Stevens', attributes: {} },
            { tag: 'td', text: 'Assistente de vendas', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '46', attributes: {} },
            { tag: 'td', text: '2011/12/06', attributes: {} },
            { tag: 'td', text: '$145,600', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Hermione Butler', attributes: {} },
            { tag: 'td', text: 'Diretor regional', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '47', attributes: {} },
            { tag: 'td', text: '2011/03/21', attributes: {} },
            { tag: 'td', text: '$356,250', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lael Greer', attributes: {} },
            { tag: 'td', text: 'Administrador de sistemas', attributes: {} },
            { tag: 'td', text: 'London', attributes: {} },
            { tag: 'td', text: '21', attributes: {} },
            { tag: 'td', text: '2009/02/27', attributes: {} },
            { tag: 'td', text: '$103,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Jonas Alexander', attributes: {} },
            { tag: 'td', text: 'Desenvolvedor', attributes: {} },
            { tag: 'td', text: 'San Francisco', attributes: {} },
            { tag: 'td', text: '30', attributes: {} },
            { tag: 'td', text: '2010/07/14', attributes: {} },
            { tag: 'td', text: '$86,500', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shad Decker', attributes: {} },
            { tag: 'td', text: 'Diretor regional', attributes: {} },
            { tag: 'td', text: 'Edinburgh', attributes: {} },
            { tag: 'td', text: '51', attributes: {} },
            { tag: 'td', text: '2008/11/13', attributes: {} },
            { tag: 'td', text: '$183,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Michael Bruce', attributes: {} },
            { tag: 'td', text: 'Desenvolvedor JavaScript', attributes: {} },
            { tag: 'td', text: 'Singapore', attributes: {} },
            { tag: 'td', text: '29', attributes: {} },
            { tag: 'td', text: '2011/06/27', attributes: {} },
            { tag: 'td', text: '$183,000', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Donna Snider', attributes: {} },
            { tag: 'td', text: 'Suporte ao cliente', attributes: {} },
            { tag: 'td', text: 'New York', attributes: {} },
            { tag: 'td', text: '27', attributes: {} },
            { tag: 'td', text: '2011/01/25', attributes: {} },
            { tag: 'td', text: '$112,000', attributes: {} },
          ],
        },
      ],
    },
  ],
  editable: [
    {
      attributes: { class: 'table table-striped', id: 'datatable-editable' },
      head: [
        [
          { text: 'Motor de renderização', attributes: {} },
          { text: 'Navegador', attributes: {} },
          { text: 'Plataforma(s)', attributes: {} },
          { text: 'Ações', attributes: {} },
        ],
      ],
      body: [
        {
          className: 'gradeX',
          cells: [
            { tag: 'td', text: 'Trident', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 4.0', attributes: {} },
            { tag: 'td', text: 'Win 95+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeC',
          cells: [
            { tag: 'td', text: 'Trident', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 5.0', attributes: {} },
            { tag: 'td', text: 'Win 95+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Trident', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 5.5', attributes: {} },
            { tag: 'td', text: 'Win 95+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Trident', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 6', attributes: {} },
            { tag: 'td', text: 'Win 98+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Trident', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 7', attributes: {} },
            { tag: 'td', text: 'Win XP SP2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Trident', attributes: {} },
            { tag: 'td', text: 'AOL browser (AOL desktop)', attributes: {} },
            { tag: 'td', text: 'Win XP', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Firefox 1.0', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Firefox 1.5', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Firefox 2.0', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Firefox 3.0', attributes: {} },
            { tag: 'td', text: 'Win 2k+ / OSX.3+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Camino 1.0', attributes: {} },
            { tag: 'td', text: 'OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Camino 1.5', attributes: {} },
            { tag: 'td', text: 'OSX.3+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Netscape 7.2', attributes: {} },
            { tag: 'td', text: 'Win 95+ / Mac OS 8.6-9.2', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Netscape Browser 8', attributes: {} },
            { tag: 'td', text: 'Win 98SE+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Netscape Navigator 9', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.0', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.1', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.2', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.3', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.4', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.5', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.6', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.7', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Mozilla 1.8', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Seamonkey 1.1', attributes: {} },
            { tag: 'td', text: 'Win 98+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Gecko', attributes: {} },
            { tag: 'td', text: 'Epiphany 2.20', attributes: {} },
            { tag: 'td', text: 'Gnome', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'Safari 1.2', attributes: {} },
            { tag: 'td', text: 'OSX.3', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'Safari 1.3', attributes: {} },
            { tag: 'td', text: 'OSX.3', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'Safari 2.0', attributes: {} },
            { tag: 'td', text: 'OSX.4+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'Safari 3.0', attributes: {} },
            { tag: 'td', text: 'OSX.4+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'OmniWeb 5.5', attributes: {} },
            { tag: 'td', text: 'OSX.4+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'iPod Touch / iPhone', attributes: {} },
            { tag: 'td', text: 'iPod', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Webkit', attributes: {} },
            { tag: 'td', text: 'S60', attributes: {} },
            { tag: 'td', text: 'S60', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 7.0', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.1+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 7.5', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 8.0', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 8.5', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.2+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 9.0', attributes: {} },
            { tag: 'td', text: 'Win 95+ / OSX.3+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 9.2', attributes: {} },
            { tag: 'td', text: 'Win 88+ / OSX.3+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera 9.5', attributes: {} },
            { tag: 'td', text: 'Win 88+ / OSX.3+', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Opera for Wii', attributes: {} },
            { tag: 'td', text: 'Wii', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Nokia N800', attributes: {} },
            { tag: 'td', text: 'N800', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Presto', attributes: {} },
            { tag: 'td', text: 'Nintendo DS browser', attributes: {} },
            { tag: 'td', text: 'Nintendo DS', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeC',
          cells: [
            { tag: 'td', text: 'KHTML', attributes: {} },
            { tag: 'td', text: 'Konqureror 3.1', attributes: {} },
            { tag: 'td', text: 'KDE 3.1', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'KHTML', attributes: {} },
            { tag: 'td', text: 'Konqureror 3.3', attributes: {} },
            { tag: 'td', text: 'KDE 3.3', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'KHTML', attributes: {} },
            { tag: 'td', text: 'Konqureror 3.5', attributes: {} },
            { tag: 'td', text: 'KDE 3.5', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeX',
          cells: [
            { tag: 'td', text: 'Tasman', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 4.5', attributes: {} },
            { tag: 'td', text: 'Mac OS 8-9', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeC',
          cells: [
            { tag: 'td', text: 'Tasman', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 5.1', attributes: {} },
            { tag: 'td', text: 'Mac OS 7.6-9', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeC',
          cells: [
            { tag: 'td', text: 'Tasman', attributes: {} },
            { tag: 'td', text: 'Internet Explorer 5.2', attributes: {} },
            { tag: 'td', text: 'Mac OS 8-X', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'NetFront 3.1', attributes: {} },
            { tag: 'td', text: 'Embedded devices', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeA',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'NetFront 3.4', attributes: {} },
            { tag: 'td', text: 'Embedded devices', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeX',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'Dillo 0.8', attributes: {} },
            { tag: 'td', text: 'Embedded devices', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeX',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'Links', attributes: {} },
            { tag: 'td', text: 'Text only', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeX',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'Lynx', attributes: {} },
            { tag: 'td', text: 'Text only', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeC',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'IE Mobile', attributes: {} },
            { tag: 'td', text: 'Windows Mobile 6', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeC',
          cells: [
            { tag: 'td', text: 'Misc', attributes: {} },
            { tag: 'td', text: 'PSP browser', attributes: {} },
            { tag: 'td', text: 'PSP', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
        {
          className: 'gradeU',
          cells: [
            { tag: 'td', text: 'Outros navegadores', attributes: {} },
            { tag: 'td', text: 'Todos os demais', attributes: {} },
            { tag: 'td', text: '-', attributes: {} },
            {
              tag: 'td',
              text: '',
              attributes: { class: 'actions' },
              actions: true,
            },
          ],
        },
      ],
    },
    {
      attributes: { id: 'mainTable', class: 'table table-striped m-b-0' },
      head: [
        [
          { text: 'Nome', attributes: {} },
          { text: 'Custo', attributes: {} },
          { text: 'Lucro', attributes: {} },
          { text: 'Diversão', attributes: {} },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Carro', attributes: {} },
            { tag: 'td', text: '100', attributes: {} },
            { tag: 'td', text: '200', attributes: {} },
            { tag: 'td', text: '0', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Bicicleta', attributes: {} },
            { tag: 'td', text: '330', attributes: {} },
            { tag: 'td', text: '240', attributes: {} },
            { tag: 'td', text: '1', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Avião', attributes: {} },
            { tag: 'td', text: '430', attributes: {} },
            { tag: 'td', text: '540', attributes: {} },
            { tag: 'td', text: '3', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Iate', attributes: {} },
            { tag: 'td', text: '100', attributes: {} },
            { tag: 'td', text: '200', attributes: {} },
            { tag: 'td', text: '0', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Segway', attributes: {} },
            { tag: 'td', text: '330', attributes: {} },
            { tag: 'td', text: '240', attributes: {} },
            { tag: 'td', text: '1', attributes: {} },
          ],
        },
      ],
    },
  ],
  responsive: [
    {
      attributes: { id: 'tech-companies-1', class: 'table  table-striped' },
      head: [
        [
          { text: 'Empresa', attributes: {} },
          { text: 'Última cotação', attributes: { 'data-priority': '1' } },
          { text: 'Horário', attributes: { 'data-priority': '3' } },
          { text: 'Variação', attributes: { 'data-priority': '1' } },
          { text: 'Fechamento anterior', attributes: { 'data-priority': '3' } },
          { text: 'Abertura', attributes: { 'data-priority': '3' } },
          { text: 'Compra', attributes: { 'data-priority': '6' } },
          { text: 'Venda', attributes: { 'data-priority': '6' } },
          { text: 'Meta em 1 ano', attributes: { 'data-priority': '6' } },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'GOOG',
              attributes: {},
              secondary: 'Google Inc.',
            },
            { tag: 'td', text: '597.74', attributes: {} },
            { tag: 'td', text: '12:12PM', attributes: {} },
            { tag: 'td', text: '14.81 (2.54%)', attributes: {} },
            { tag: 'td', text: '582.93', attributes: {} },
            { tag: 'td', text: '597.95', attributes: {} },
            { tag: 'td', text: '597.73 x 100', attributes: {} },
            { tag: 'td', text: '597.91 x 300', attributes: {} },
            { tag: 'td', text: '731.10', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AAPL',
              attributes: {},
              secondary: 'Apple Inc.',
            },
            { tag: 'td', text: '378.94', attributes: {} },
            { tag: 'td', text: '12:22PM', attributes: {} },
            { tag: 'td', text: '5.74 (1.54%)', attributes: {} },
            { tag: 'td', text: '373.20', attributes: {} },
            { tag: 'td', text: '381.02', attributes: {} },
            { tag: 'td', text: '378.92 x 300', attributes: {} },
            { tag: 'td', text: '378.99 x 100', attributes: {} },
            { tag: 'td', text: '505.94', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AMZN',
              attributes: {},
              secondary: 'Amazon.com Inc.',
            },
            { tag: 'td', text: '191.55', attributes: {} },
            { tag: 'td', text: '12:23PM', attributes: {} },
            { tag: 'td', text: '3.16 (1.68%)', attributes: {} },
            { tag: 'td', text: '188.39', attributes: {} },
            { tag: 'td', text: '194.99', attributes: {} },
            { tag: 'td', text: '191.52 x 300', attributes: {} },
            { tag: 'td', text: '191.58 x 100', attributes: {} },
            { tag: 'td', text: '240.32', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'ORCL',
              attributes: {},
              secondary: 'Oracle Corporation',
            },
            { tag: 'td', text: '31.15', attributes: {} },
            { tag: 'td', text: '12:44PM', attributes: {} },
            { tag: 'td', text: '1.41 (4.72%)', attributes: {} },
            { tag: 'td', text: '29.74', attributes: {} },
            { tag: 'td', text: '30.67', attributes: {} },
            { tag: 'td', text: '31.14 x 6500', attributes: {} },
            { tag: 'td', text: '31.15 x 3200', attributes: {} },
            { tag: 'td', text: '36.11', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'MSFT',
              attributes: {},
              secondary: 'Microsoft Corporation',
            },
            { tag: 'td', text: '25.50', attributes: {} },
            { tag: 'td', text: '12:27PM', attributes: {} },
            { tag: 'td', text: '0.66 (2.67%)', attributes: {} },
            { tag: 'td', text: '24.84', attributes: {} },
            { tag: 'td', text: '25.37', attributes: {} },
            { tag: 'td', text: '25.50 x 71100', attributes: {} },
            { tag: 'td', text: '25.51 x 17800', attributes: {} },
            { tag: 'td', text: '31.50', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'CSCO',
              attributes: {},
              secondary: 'Cisco Systems, Inc.',
            },
            { tag: 'td', text: '18.65', attributes: {} },
            { tag: 'td', text: '12:45PM', attributes: {} },
            { tag: 'td', text: '0.97 (5.49%)', attributes: {} },
            { tag: 'td', text: '17.68', attributes: {} },
            { tag: 'td', text: '18.23', attributes: {} },
            { tag: 'td', text: '18.65 x 10300', attributes: {} },
            { tag: 'td', text: '18.66 x 24000', attributes: {} },
            { tag: 'td', text: '21.12', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'YHOO',
              attributes: {},
              secondary: 'Yahoo! Inc.',
            },
            { tag: 'td', text: '15.81', attributes: {} },
            { tag: 'td', text: '12:25PM', attributes: {} },
            { tag: 'td', text: '0.11 (0.67%)', attributes: {} },
            { tag: 'td', text: '15.70', attributes: {} },
            { tag: 'td', text: '15.94', attributes: {} },
            { tag: 'td', text: '15.79 x 6100', attributes: {} },
            { tag: 'td', text: '15.80 x 17000', attributes: {} },
            { tag: 'td', text: '18.16', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'GOOG',
              attributes: {},
              secondary: 'Google Inc.',
            },
            { tag: 'td', text: '597.74', attributes: {} },
            { tag: 'td', text: '12:12PM', attributes: {} },
            { tag: 'td', text: '14.81 (2.54%)', attributes: {} },
            { tag: 'td', text: '582.93', attributes: {} },
            { tag: 'td', text: '597.95', attributes: {} },
            { tag: 'td', text: '597.73 x 100', attributes: {} },
            { tag: 'td', text: '597.91 x 300', attributes: {} },
            { tag: 'td', text: '731.10', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AAPL',
              attributes: {},
              secondary: 'Apple Inc.',
            },
            { tag: 'td', text: '378.94', attributes: {} },
            { tag: 'td', text: '12:22PM', attributes: {} },
            { tag: 'td', text: '5.74 (1.54%)', attributes: {} },
            { tag: 'td', text: '373.20', attributes: {} },
            { tag: 'td', text: '381.02', attributes: {} },
            { tag: 'td', text: '378.92 x 300', attributes: {} },
            { tag: 'td', text: '378.99 x 100', attributes: {} },
            { tag: 'td', text: '505.94', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AMZN',
              attributes: {},
              secondary: 'Amazon.com Inc.',
            },
            { tag: 'td', text: '191.55', attributes: {} },
            { tag: 'td', text: '12:23PM', attributes: {} },
            { tag: 'td', text: '3.16 (1.68%)', attributes: {} },
            { tag: 'td', text: '188.39', attributes: {} },
            { tag: 'td', text: '194.99', attributes: {} },
            { tag: 'td', text: '191.52 x 300', attributes: {} },
            { tag: 'td', text: '191.58 x 100', attributes: {} },
            { tag: 'td', text: '240.32', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'ORCL',
              attributes: {},
              secondary: 'Oracle Corporation',
            },
            { tag: 'td', text: '31.15', attributes: {} },
            { tag: 'td', text: '12:44PM', attributes: {} },
            { tag: 'td', text: '1.41 (4.72%)', attributes: {} },
            { tag: 'td', text: '29.74', attributes: {} },
            { tag: 'td', text: '30.67', attributes: {} },
            { tag: 'td', text: '31.14 x 6500', attributes: {} },
            { tag: 'td', text: '31.15 x 3200', attributes: {} },
            { tag: 'td', text: '36.11', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'MSFT',
              attributes: {},
              secondary: 'Microsoft Corporation',
            },
            { tag: 'td', text: '25.50', attributes: {} },
            { tag: 'td', text: '12:27PM', attributes: {} },
            { tag: 'td', text: '0.66 (2.67%)', attributes: {} },
            { tag: 'td', text: '24.84', attributes: {} },
            { tag: 'td', text: '25.37', attributes: {} },
            { tag: 'td', text: '25.50 x 71100', attributes: {} },
            { tag: 'td', text: '25.51 x 17800', attributes: {} },
            { tag: 'td', text: '31.50', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'CSCO',
              attributes: {},
              secondary: 'Cisco Systems, Inc.',
            },
            { tag: 'td', text: '18.65', attributes: {} },
            { tag: 'td', text: '12:45PM', attributes: {} },
            { tag: 'td', text: '0.97 (5.49%)', attributes: {} },
            { tag: 'td', text: '17.68', attributes: {} },
            { tag: 'td', text: '18.23', attributes: {} },
            { tag: 'td', text: '18.65 x 10300', attributes: {} },
            { tag: 'td', text: '18.66 x 24000', attributes: {} },
            { tag: 'td', text: '21.12', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'YHOO',
              attributes: {},
              secondary: 'Yahoo! Inc.',
            },
            { tag: 'td', text: '15.81', attributes: {} },
            { tag: 'td', text: '12:25PM', attributes: {} },
            { tag: 'td', text: '0.11 (0.67%)', attributes: {} },
            { tag: 'td', text: '15.70', attributes: {} },
            { tag: 'td', text: '15.94', attributes: {} },
            { tag: 'td', text: '15.79 x 6100', attributes: {} },
            { tag: 'td', text: '15.80 x 17000', attributes: {} },
            { tag: 'td', text: '18.16', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'GOOG',
              attributes: {},
              secondary: 'Google Inc.',
            },
            { tag: 'td', text: '597.74', attributes: {} },
            { tag: 'td', text: '12:12PM', attributes: {} },
            { tag: 'td', text: '14.81 (2.54%)', attributes: {} },
            { tag: 'td', text: '582.93', attributes: {} },
            { tag: 'td', text: '597.95', attributes: {} },
            { tag: 'td', text: '597.73 x 100', attributes: {} },
            { tag: 'td', text: '597.91 x 300', attributes: {} },
            { tag: 'td', text: '731.10', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AAPL',
              attributes: {},
              secondary: 'Apple Inc.',
            },
            { tag: 'td', text: '378.94', attributes: {} },
            { tag: 'td', text: '12:22PM', attributes: {} },
            { tag: 'td', text: '5.74 (1.54%)', attributes: {} },
            { tag: 'td', text: '373.20', attributes: {} },
            { tag: 'td', text: '381.02', attributes: {} },
            { tag: 'td', text: '378.92 x 300', attributes: {} },
            { tag: 'td', text: '378.99 x 100', attributes: {} },
            { tag: 'td', text: '505.94', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AMZN',
              attributes: {},
              secondary: 'Amazon.com Inc.',
            },
            { tag: 'td', text: '191.55', attributes: {} },
            { tag: 'td', text: '12:23PM', attributes: {} },
            { tag: 'td', text: '3.16 (1.68%)', attributes: {} },
            { tag: 'td', text: '188.39', attributes: {} },
            { tag: 'td', text: '194.99', attributes: {} },
            { tag: 'td', text: '191.52 x 300', attributes: {} },
            { tag: 'td', text: '191.58 x 100', attributes: {} },
            { tag: 'td', text: '240.32', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'ORCL',
              attributes: {},
              secondary: 'Oracle Corporation',
            },
            { tag: 'td', text: '31.15', attributes: {} },
            { tag: 'td', text: '12:44PM', attributes: {} },
            { tag: 'td', text: '1.41 (4.72%)', attributes: {} },
            { tag: 'td', text: '29.74', attributes: {} },
            { tag: 'td', text: '30.67', attributes: {} },
            { tag: 'td', text: '31.14 x 6500', attributes: {} },
            { tag: 'td', text: '31.15 x 3200', attributes: {} },
            { tag: 'td', text: '36.11', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'MSFT',
              attributes: {},
              secondary: 'Microsoft Corporation',
            },
            { tag: 'td', text: '25.50', attributes: {} },
            { tag: 'td', text: '12:27PM', attributes: {} },
            { tag: 'td', text: '0.66 (2.67%)', attributes: {} },
            { tag: 'td', text: '24.84', attributes: {} },
            { tag: 'td', text: '25.37', attributes: {} },
            { tag: 'td', text: '25.50 x 71100', attributes: {} },
            { tag: 'td', text: '25.51 x 17800', attributes: {} },
            { tag: 'td', text: '31.50', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'CSCO',
              attributes: {},
              secondary: 'Cisco Systems, Inc.',
            },
            { tag: 'td', text: '18.65', attributes: {} },
            { tag: 'td', text: '12:45PM', attributes: {} },
            { tag: 'td', text: '0.97 (5.49%)', attributes: {} },
            { tag: 'td', text: '17.68', attributes: {} },
            { tag: 'td', text: '18.23', attributes: {} },
            { tag: 'td', text: '18.65 x 10300', attributes: {} },
            { tag: 'td', text: '18.66 x 24000', attributes: {} },
            { tag: 'td', text: '21.12', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'YHOO',
              attributes: {},
              secondary: 'Yahoo! Inc.',
            },
            { tag: 'td', text: '15.81', attributes: {} },
            { tag: 'td', text: '12:25PM', attributes: {} },
            { tag: 'td', text: '0.11 (0.67%)', attributes: {} },
            { tag: 'td', text: '15.70', attributes: {} },
            { tag: 'td', text: '15.94', attributes: {} },
            { tag: 'td', text: '15.79 x 6100', attributes: {} },
            { tag: 'td', text: '15.80 x 17000', attributes: {} },
            { tag: 'td', text: '18.16', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'GOOG',
              attributes: {},
              secondary: 'Google Inc.',
            },
            { tag: 'td', text: '597.74', attributes: {} },
            { tag: 'td', text: '12:12PM', attributes: {} },
            { tag: 'td', text: '14.81 (2.54%)', attributes: {} },
            { tag: 'td', text: '582.93', attributes: {} },
            { tag: 'td', text: '597.95', attributes: {} },
            { tag: 'td', text: '597.73 x 100', attributes: {} },
            { tag: 'td', text: '597.91 x 300', attributes: {} },
            { tag: 'td', text: '731.10', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AAPL',
              attributes: {},
              secondary: 'Apple Inc.',
            },
            { tag: 'td', text: '378.94', attributes: {} },
            { tag: 'td', text: '12:22PM', attributes: {} },
            { tag: 'td', text: '5.74 (1.54%)', attributes: {} },
            { tag: 'td', text: '373.20', attributes: {} },
            { tag: 'td', text: '381.02', attributes: {} },
            { tag: 'td', text: '378.92 x 300', attributes: {} },
            { tag: 'td', text: '378.99 x 100', attributes: {} },
            { tag: 'td', text: '505.94', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AMZN',
              attributes: {},
              secondary: 'Amazon.com Inc.',
            },
            { tag: 'td', text: '191.55', attributes: {} },
            { tag: 'td', text: '12:23PM', attributes: {} },
            { tag: 'td', text: '3.16 (1.68%)', attributes: {} },
            { tag: 'td', text: '188.39', attributes: {} },
            { tag: 'td', text: '194.99', attributes: {} },
            { tag: 'td', text: '191.52 x 300', attributes: {} },
            { tag: 'td', text: '191.58 x 100', attributes: {} },
            { tag: 'td', text: '240.32', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'ORCL',
              attributes: {},
              secondary: 'Oracle Corporation',
            },
            { tag: 'td', text: '31.15', attributes: {} },
            { tag: 'td', text: '12:44PM', attributes: {} },
            { tag: 'td', text: '1.41 (4.72%)', attributes: {} },
            { tag: 'td', text: '29.74', attributes: {} },
            { tag: 'td', text: '30.67', attributes: {} },
            { tag: 'td', text: '31.14 x 6500', attributes: {} },
            { tag: 'td', text: '31.15 x 3200', attributes: {} },
            { tag: 'td', text: '36.11', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'MSFT',
              attributes: {},
              secondary: 'Microsoft Corporation',
            },
            { tag: 'td', text: '25.50', attributes: {} },
            { tag: 'td', text: '12:27PM', attributes: {} },
            { tag: 'td', text: '0.66 (2.67%)', attributes: {} },
            { tag: 'td', text: '24.84', attributes: {} },
            { tag: 'td', text: '25.37', attributes: {} },
            { tag: 'td', text: '25.50 x 71100', attributes: {} },
            { tag: 'td', text: '25.51 x 17800', attributes: {} },
            { tag: 'td', text: '31.50', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'CSCO',
              attributes: {},
              secondary: 'Cisco Systems, Inc.',
            },
            { tag: 'td', text: '18.65', attributes: {} },
            { tag: 'td', text: '12:45PM', attributes: {} },
            { tag: 'td', text: '0.97 (5.49%)', attributes: {} },
            { tag: 'td', text: '17.68', attributes: {} },
            { tag: 'td', text: '18.23', attributes: {} },
            { tag: 'td', text: '18.65 x 10300', attributes: {} },
            { tag: 'td', text: '18.66 x 24000', attributes: {} },
            { tag: 'td', text: '21.12', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'YHOO',
              attributes: {},
              secondary: 'Yahoo! Inc.',
            },
            { tag: 'td', text: '15.81', attributes: {} },
            { tag: 'td', text: '12:25PM', attributes: {} },
            { tag: 'td', text: '0.11 (0.67%)', attributes: {} },
            { tag: 'td', text: '15.70', attributes: {} },
            { tag: 'td', text: '15.94', attributes: {} },
            { tag: 'td', text: '15.79 x 6100', attributes: {} },
            { tag: 'td', text: '15.80 x 17000', attributes: {} },
            { tag: 'td', text: '18.16', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'GOOG',
              attributes: {},
              secondary: 'Google Inc.',
            },
            { tag: 'td', text: '597.74', attributes: {} },
            { tag: 'td', text: '12:12PM', attributes: {} },
            { tag: 'td', text: '14.81 (2.54%)', attributes: {} },
            { tag: 'td', text: '582.93', attributes: {} },
            { tag: 'td', text: '597.95', attributes: {} },
            { tag: 'td', text: '597.73 x 100', attributes: {} },
            { tag: 'td', text: '597.91 x 300', attributes: {} },
            { tag: 'td', text: '731.10', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AAPL',
              attributes: {},
              secondary: 'Apple Inc.',
            },
            { tag: 'td', text: '378.94', attributes: {} },
            { tag: 'td', text: '12:22PM', attributes: {} },
            { tag: 'td', text: '5.74 (1.54%)', attributes: {} },
            { tag: 'td', text: '373.20', attributes: {} },
            { tag: 'td', text: '381.02', attributes: {} },
            { tag: 'td', text: '378.92 x 300', attributes: {} },
            { tag: 'td', text: '378.99 x 100', attributes: {} },
            { tag: 'td', text: '505.94', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'AMZN',
              attributes: {},
              secondary: 'Amazon.com Inc.',
            },
            { tag: 'td', text: '191.55', attributes: {} },
            { tag: 'td', text: '12:23PM', attributes: {} },
            { tag: 'td', text: '3.16 (1.68%)', attributes: {} },
            { tag: 'td', text: '188.39', attributes: {} },
            { tag: 'td', text: '194.99', attributes: {} },
            { tag: 'td', text: '191.52 x 300', attributes: {} },
            { tag: 'td', text: '191.58 x 100', attributes: {} },
            { tag: 'td', text: '240.32', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'ORCL',
              attributes: {},
              secondary: 'Oracle Corporation',
            },
            { tag: 'td', text: '31.15', attributes: {} },
            { tag: 'td', text: '12:44PM', attributes: {} },
            { tag: 'td', text: '1.41 (4.72%)', attributes: {} },
            { tag: 'td', text: '29.74', attributes: {} },
            { tag: 'td', text: '30.67', attributes: {} },
            { tag: 'td', text: '31.14 x 6500', attributes: {} },
            { tag: 'td', text: '31.15 x 3200', attributes: {} },
            { tag: 'td', text: '36.11', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'MSFT',
              attributes: {},
              secondary: 'Microsoft Corporation',
            },
            { tag: 'td', text: '25.50', attributes: {} },
            { tag: 'td', text: '12:27PM', attributes: {} },
            { tag: 'td', text: '0.66 (2.67%)', attributes: {} },
            { tag: 'td', text: '24.84', attributes: {} },
            { tag: 'td', text: '25.37', attributes: {} },
            { tag: 'td', text: '25.50 x 71100', attributes: {} },
            { tag: 'td', text: '25.51 x 17800', attributes: {} },
            { tag: 'td', text: '31.50', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'CSCO',
              attributes: {},
              secondary: 'Cisco Systems, Inc.',
            },
            { tag: 'td', text: '18.65', attributes: {} },
            { tag: 'td', text: '12:45PM', attributes: {} },
            { tag: 'td', text: '0.97 (5.49%)', attributes: {} },
            { tag: 'td', text: '17.68', attributes: {} },
            { tag: 'td', text: '18.23', attributes: {} },
            { tag: 'td', text: '18.65 x 10300', attributes: {} },
            { tag: 'td', text: '18.66 x 24000', attributes: {} },
            { tag: 'td', text: '21.12', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'th',
              text: 'YHOO',
              attributes: {},
              secondary: 'Yahoo! Inc.',
            },
            { tag: 'td', text: '15.81', attributes: {} },
            { tag: 'td', text: '12:25PM', attributes: {} },
            { tag: 'td', text: '0.11 (0.67%)', attributes: {} },
            { tag: 'td', text: '15.70', attributes: {} },
            { tag: 'td', text: '15.94', attributes: {} },
            { tag: 'td', text: '15.79 x 6100', attributes: {} },
            { tag: 'td', text: '15.80 x 17000', attributes: {} },
            { tag: 'td', text: '18.16', attributes: {} },
          ],
        },
      ],
    },
  ],
  'foo-tables': [
    {
      attributes: {
        id: 'demo-foo-filtering',
        class: 'table table-striped toggle-circle m-b-0',
        'data-page-size': '7',
      },
      head: [
        [
          { text: 'Nome', attributes: { 'data-toggle': 'true' } },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Cargo', attributes: { 'data-hide': 'phone' } },
          { text: 'Nascimento', attributes: { 'data-hide': 'phone, tablet' } },
          { text: 'Status', attributes: { 'data-hide': 'phone, tablet' } },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        id: 'demo-foo-addrow',
        class: 'table table-striped m-b-0 table-hover toggle-circle',
        'data-page-size': '7',
      },
      head: [
        [
          {
            text: '',
            attributes: { 'data-sort-ignore': 'true', class: 'min-width' },
          },
          {
            text: 'Nome',
            attributes: { 'data-sort-initial': 'true', 'data-toggle': 'true' },
          },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Cargo', attributes: { 'data-hide': 'phone' } },
          { text: 'Nascimento', attributes: { 'data-hide': 'phone, tablet' } },
          { text: 'Status', attributes: { 'data-hide': 'phone, tablet' } },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            {
              tag: 'td',
              text: '',
              attributes: { style: 'text-align: center;' },
              actions: true,
            },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        id: 'demo-foo-row-toggler',
        class: 'table toggle-circle table-hover',
      },
      head: [
        [
          { text: 'Nome', attributes: { 'data-toggle': 'true' } },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Cargo', attributes: { 'data-hide': 'phone' } },
          { text: 'Nascimento', attributes: { 'data-hide': 'all' } },
          { text: 'Status', attributes: { 'data-hide': 'all' } },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        id: 'demo-foo-pagination',
        class: 'table m-b-0 toggle-arrow-tiny',
        'data-page-size': '5',
      },
      head: [
        [
          { text: 'Nome', attributes: { 'data-toggle': 'true' } },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Cargo', attributes: { 'data-hide': 'phone' } },
          { text: 'Nascimento', attributes: { 'data-hide': 'all' } },
          { text: 'Status', attributes: { 'data-hide': 'all' } },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        id: 'demo-foo-accordion',
        class: 'table m-b-0 toggle-arrow-tiny',
      },
      head: [
        [
          { text: 'Nome', attributes: { 'data-toggle': 'true' } },
          { text: 'Sobrenome', attributes: {} },
          { text: 'Cargo', attributes: { 'data-hide': 'phone' } },
          { text: 'Nascimento', attributes: { 'data-hide': 'all' } },
          { text: 'Status', attributes: { 'data-hide': 'all' } },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
  ],
  bootstrap: [
    {
      attributes: {
        'data-toggle': 'table',
        'data-show-columns': 'false',
        'data-page-list': '[5, 10, 20]',
        'data-page-size': '5',
        'data-pagination': 'true',
        'data-show-pagination-switch': 'true',
        class: 'table-bordered ',
      },
      head: [
        [
          {
            text: 'Nome',
            attributes: { 'data-field': 'id', 'data-switchable': 'false' },
          },
          { text: 'Sobrenome', attributes: { 'data-field': 'name' } },
          { text: 'Cargo', attributes: { 'data-field': 'date' } },
          { text: 'Nascimento', attributes: { 'data-field': 'amount' } },
          {
            text: 'Status',
            attributes: { 'data-field': 'user-status', class: 'text-center' },
          },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        'data-toggle': 'table',
        'data-page-size': '10',
        'data-pagination': 'true',
        class: 'table-bordered ',
      },
      head: [
        [
          {
            text: '',
            attributes: { 'data-field': 'state', 'data-checkbox': 'true' },
          },
          {
            text: 'Nome',
            attributes: { 'data-field': 'id', 'data-switchable': 'false' },
          },
          { text: 'Sobrenome', attributes: { 'data-field': 'name' } },
          { text: 'Cargo', attributes: { 'data-field': 'date' } },
          { text: 'Nascimento', attributes: { 'data-field': 'amount' } },
          {
            text: 'Status',
            attributes: { 'data-field': 'user-status', class: 'text-center' },
          },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        'data-toggle': 'table',
        'data-page-size': '10',
        'data-pagination': 'true',
        class: 'table-bordered ',
      },
      head: [
        [
          {
            text: '',
            attributes: { 'data-field': 'state', 'data-radio': 'true' },
          },
          {
            text: 'Nome',
            attributes: { 'data-field': 'id', 'data-switchable': 'false' },
          },
          { text: 'Sobrenome', attributes: { 'data-field': 'name' } },
          { text: 'Cargo', attributes: { 'data-field': 'date' } },
          { text: 'Nascimento', attributes: { 'data-field': 'amount' } },
          {
            text: 'Status',
            attributes: { 'data-field': 'user-status', class: 'text-center' },
          },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Isidra', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: 'Árbitro de trânsito', attributes: {} },
            { tag: 'td', text: '22 Jun 1972', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Shona', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: 'Piloto de transporte aéreo', attributes: {} },
            { tag: 'td', text: '3 Oct 1981', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Granville', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: 'Representante de vendas', attributes: {} },
            { tag: 'td', text: '19 Apr 1969', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Easer', attributes: {} },
            { tag: 'td', text: 'Dragoo', attributes: {} },
            { tag: 'td', text: 'Instalador de drywall', attributes: {} },
            { tag: 'td', text: '13 Dec 1977', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maple', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: 'Oficial de aviação', attributes: {} },
            { tag: 'td', text: '30 Dec 1991', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Maxine', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {}, link: true },
            {
              tag: 'td',
              text: 'Representante de vendas',
              attributes: {},
              link: true,
            },
            { tag: 'td', text: '17 Oct 1987', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lorraine', attributes: {} },
            { tag: 'td', text: 'Mcgaughy', attributes: {} },
            { tag: 'td', text: 'Técnico em hemodiálise', attributes: {} },
            { tag: 'td', text: '11 Nov 1983', attributes: {} },
            { tag: 'td', text: 'Desativado', attributes: {}, badge: 'inverse' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lizzee', attributes: {} },
            { tag: 'td', text: 'Goodlow', attributes: {}, link: true },
            { tag: 'td', text: 'Bibliotecário técnico', attributes: {} },
            { tag: 'td', text: '1 Nov 1961', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Judi', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: 'Eletricista de rede', attributes: {} },
            { tag: 'td', text: '23 Jun 1981', attributes: {} },
            { tag: 'td', text: 'Ativo', attributes: {}, badge: 'success' },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'Lauri', attributes: {} },
            { tag: 'td', text: 'Hyland', attributes: {} },
            { tag: 'td', text: 'Supervisor de blackjack', attributes: {} },
            { tag: 'td', text: '15 Nov 1985', attributes: {} },
            { tag: 'td', text: 'Suspenso', attributes: {}, badge: 'danger' },
          ],
        },
      ],
    },
    {
      attributes: {
        'data-toggle': 'table',
        'data-sort-name': 'id',
        'data-page-list': '[5, 10, 20]',
        'data-page-size': '5',
        'data-pagination': 'true',
        'data-show-pagination-switch': 'true',
        class: 'table-bordered ',
      },
      head: [
        [
          {
            text: 'Nº do pedido',
            attributes: {
              'data-field': 'id',
              'data-sortable': 'true',
              'data-formatter': 'invoiceFormatter',
            },
          },
          {
            text: 'Nome',
            attributes: { 'data-field': 'name', 'data-sortable': 'true' },
          },
          {
            text: 'Data do pedido',
            attributes: {
              'data-field': 'date',
              'data-sortable': 'true',
              'data-formatter': 'dateFormatter',
            },
          },
          {
            text: 'Preço',
            attributes: {
              'data-field': 'amount',
              'data-align': 'center',
              'data-sortable': 'true',
              'data-sorter': 'priceSorter',
            },
          },
          {
            text: 'Status',
            attributes: {
              'data-field': 'status',
              'data-align': 'center',
              'data-sortable': 'true',
              'data-formatter': 'statusFormatter',
            },
          },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1609', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1610', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: '24 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 15.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1611', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: '25 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1612', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: '27 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1613', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: '28 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 95.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1614', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '29 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1615', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1616', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1617', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1618', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1619', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1620', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1621', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1622', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1623', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1624', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: {
        'data-toggle': 'table',
        'data-search': 'true',
        'data-show-refresh': 'true',
        'data-show-toggle': 'true',
        'data-show-columns': 'true',
        'data-sort-name': 'id',
        'data-page-list': '[5, 10, 20]',
        'data-page-size': '5',
        'data-pagination': 'true',
        'data-show-pagination-switch': 'true',
        class: 'table-bordered ',
      },
      head: [
        [
          {
            text: 'Nº do pedido',
            attributes: {
              'data-field': 'id',
              'data-sortable': 'true',
              'data-formatter': 'invoiceFormatter',
            },
          },
          {
            text: 'Nome',
            attributes: { 'data-field': 'name', 'data-sortable': 'true' },
          },
          {
            text: 'Data do pedido',
            attributes: {
              'data-field': 'date',
              'data-sortable': 'true',
              'data-formatter': 'dateFormatter',
            },
          },
          {
            text: 'Preço',
            attributes: {
              'data-field': 'amount',
              'data-align': 'center',
              'data-sortable': 'true',
              'data-sorter': 'priceSorter',
            },
          },
          {
            text: 'Status',
            attributes: {
              'data-field': 'status',
              'data-align': 'center',
              'data-sortable': 'true',
              'data-formatter': 'statusFormatter',
            },
          },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1609', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1610', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: '24 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 15.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1611', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: '25 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1612', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: '27 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1613', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: '28 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 95.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1614', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '29 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1615', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1616', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1617', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1618', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1619', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1620', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1621', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1622', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1623', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: 'UB-1624', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
      ],
    },
    {
      attributes: {
        id: 'demo-custom-toolbar',
        'data-toggle': 'table',
        'data-toolbar': '#demo-delete-row',
        'data-search': 'true',
        'data-show-refresh': 'true',
        'data-show-toggle': 'true',
        'data-show-columns': 'true',
        'data-sort-name': 'id',
        'data-page-list': '[5, 10, 20]',
        'data-page-size': '5',
        'data-pagination': 'true',
        'data-show-pagination-switch': 'true',
        class: 'table-bordered ',
      },
      head: [
        [
          {
            text: '',
            attributes: { 'data-field': 'state', 'data-checkbox': 'true' },
          },
          {
            text: 'Nº do pedido',
            attributes: {
              'data-field': 'id',
              'data-sortable': 'true',
              'data-formatter': 'invoiceFormatter',
            },
          },
          {
            text: 'Nome',
            attributes: { 'data-field': 'name', 'data-sortable': 'true' },
          },
          {
            text: 'Data do pedido',
            attributes: {
              'data-field': 'date',
              'data-sortable': 'true',
              'data-formatter': 'dateFormatter',
            },
          },
          {
            text: 'Preço',
            attributes: {
              'data-field': 'amount',
              'data-align': 'center',
              'data-sortable': 'true',
              'data-sorter': 'priceSorter',
            },
          },
          {
            text: 'Status',
            attributes: {
              'data-field': 'status',
              'data-align': 'center',
              'data-sortable': 'true',
              'data-formatter': 'statusFormatter',
            },
          },
        ],
      ],
      body: [
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1609', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1610', attributes: {} },
            { tag: 'td', text: 'Woldt', attributes: {} },
            { tag: 'td', text: '24 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 15.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1611', attributes: {} },
            { tag: 'td', text: 'Leonardo', attributes: {} },
            { tag: 'td', text: '25 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1612', attributes: {} },
            { tag: 'td', text: 'Halladay', attributes: {} },
            { tag: 'td', text: '27 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1613', attributes: {} },
            { tag: 'td', text: 'Badgett', attributes: {} },
            { tag: 'td', text: '28 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 95.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1614', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '29 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1615', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1616', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1617', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1618', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1619', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1620', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1621', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Não pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1622', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Pago', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1623', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Enviado', attributes: {} },
          ],
        },
        {
          className: '',
          cells: [
            { tag: 'td', text: '', attributes: {} },
            { tag: 'td', text: 'UB-1624', attributes: {} },
            { tag: 'td', text: 'Boudreaux', attributes: {} },
            { tag: 'td', text: '22 Jun 2015', attributes: {} },
            { tag: 'td', text: '$ 35.00', attributes: {} },
            { tag: 'td', text: 'Reembolsado', attributes: {} },
          ],
        },
      ],
    },
  ],
};
