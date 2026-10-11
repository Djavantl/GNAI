import Table from '@/components/Tables/Table/Table';
import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import './ComponentsGrid.css';

type GridRow = number[];

interface GridOptionRow {
  heading: string;
  cells: Array<{
    className?: string;
    colSpan?: number;
    content: ReactNode;
  }>;
}

const gridOptionRows: GridOptionRow[] = [
  {
    heading: 'Comportamento da grade',
    cells: [
      { content: 'Horizontal em todos os tamanhos' },
      { colSpan: 3, content: 'Empilhada inicialmente, horizontal acima dos breakpoints' }
    ]
  },
  {
    heading: 'Largura máxima do container',
    cells: [{ content: 'Nenhuma (auto)' }, { content: '750px' }, { content: '970px' }, { content: '1170px' }]
  },
  {
    heading: 'Prefixo da classe',
    cells: [
      { content: <code>.col-xs-</code> },
      { content: <code>.col-sm-</code> },
      { content: <code>.col-md-</code> },
      { content: <code>.col-lg-</code> }
    ]
  },
  {
    heading: 'Nº de colunas',
    cells: [{ colSpan: 4, content: '12' }]
  },
  {
    heading: 'Largura máxima da coluna',
    cells: [{ className: 'text-muted', content: 'Automática' }, { content: '60px' }, { content: '78px' }, { content: '95px' }]
  },
  {
    heading: 'Largura da calha',
    cells: [{ colSpan: 4, content: '20px (10px em cada lado da coluna)' }]
  },
  {
    heading: 'Aninhável',
    cells: [{ colSpan: 4, content: 'Sim' }]
  },
  {
    heading: 'Deslocamentos',
    cells: [{ className: 'text-muted', content: 'N/D' }, { colSpan: 3, content: 'Sim' }]
  },
  {
    heading: 'Ordenação de colunas',
    cells: [{ className: 'text-muted', content: 'N/D' }, { colSpan: 3, content: 'Sim' }]
  }
];

const gridRows: GridRow[] = [
  [12],
  [11, 1],
  [10, 2],
  [9, 3],
  [8, 4],
  [7, 5],
  [6, 6],
  [5, 7],
  [4, 8],
  [3, 9],
  [2, 10],
  [1, 11],
  [2, 3, 4, 2, 1]
];

export function PageTitle(): React.JSX.Element {
  return (
    <div className="componentsGridPage__pageTitleRow">
      <div className="componentsGridPage__col componentsGridPage__col--full">
        <h4 className="page-title componentsGridPage__pageTitle">Sistema de grade</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Grade' }]} />
      </div>
    </div>
  );
}

function SectionTitle({ children }: { children: ReactNode }): React.JSX.Element {
  return (
    <h4 className="componentsGridPage__sectionTitle header-title m-t-0">
      <b>{children}</b>
    </h4>
  );
}

function Description({ children }: { children: ReactNode }): React.JSX.Element {
  return <p className="componentsGridPage__description text-muted m-b-30 font-13">{children}</p>;
}

export function GridOptionsTable(): React.JSX.Element {
  return (
    <CardBox className="componentsGridPage__card">
      <SectionTitle>Opções da grade</SectionTitle>
      <Description>Veja como os aspectos do sistema de grade do Bootstrap funcionam em vários dispositivos com uma tabela prática.</Description>

      <Table appearance="inherit" className="componentsGridPage__table table table-bordered table-striped">
        <colgroup>
          <col className="componentsGridPage__tableColumn componentsGridPage__tableColumn--label" />
          <col className="componentsGridPage__tableColumn componentsGridPage__tableColumn--extraSmall" />
          <col className="componentsGridPage__tableColumn componentsGridPage__tableColumn--small" />
          <col className="componentsGridPage__tableColumn componentsGridPage__tableColumn--medium" />
          <col className="componentsGridPage__tableColumn componentsGridPage__tableColumn--large" />
        </colgroup>
        <thead>
          <tr>
            <th />
            <th>
              Dispositivos extra pequenos{' '}
              <small>Telefones (&lt;768px)</small>
            </th>
            <th>
              Dispositivos pequenos{' '}
              <small>Tablets (≥768px)</small>
            </th>
            <th>
              Dispositivos médios{' '}
              <small>Desktops (≥992px)</small>
            </th>
            <th>
              Dispositivos grandes{' '}
              <small>Desktops (≥1200px)</small>
            </th>
          </tr>
        </thead>
        <tbody>
          {gridOptionRows.map((row) => (
            <tr key={row.heading}>
              <th>{row.heading}</th>
              {row.cells.map((cell, index) => (
                <td key={`${row.heading}-${index}`} className={cell.className} colSpan={cell.colSpan}>
                  {cell.content}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </Table>
    </CardBox>
  );
}

export function GridStructureExample(): React.JSX.Element {
  return (
    <CardBox className="componentsGridPage__card componentsGridPage__card--last">
      <SectionTitle>Exemplo de grade</SectionTitle>
      <Description>
        Não quer que suas colunas simplesmente empilhem em dispositivos menores? Use as classes de grade de dispositivos extra pequenos e médios adicionando <code>.col-xs-*</code> <code>.col-md-*</code> às suas colunas. Veja o exemplo abaixo para entender melhor como tudo funciona.
      </Description>

      <div className="componentsGridPage__gridStructure grid-structure">
        {gridRows.map((columns, rowIndex) => (
          <div className="componentsGridPage__row" key={`grid-row-${rowIndex}`}>
            {columns.map((columnWidth, columnIndex) => (
              <div className={`componentsGridPage__gridColumn componentsGridPage__gridColumn--${columnWidth}`} key={`grid-row-${rowIndex}-${columnIndex}`}>
                <div className="componentsGridPage__gridContainer grid-container">col-md-{columnWidth}</div>
              </div>
            ))}
          </div>
        ))}
      </div>
    </CardBox>
  );
}

