import type { ReactNode } from 'react';
import type { TableProps } from '@/components/Tables/Table/Table';

export interface TableListColumn {
  id: string;
  label: string;
  width?: number | string;
  /** @deprecated Mantido por compatibilidade; não limita o redimensionamento. */
  minWidth?: number;
  hideable?: boolean;
  /** Colunas complementares começam desmarcadas; fixas continuam sempre visíveis. */
  defaultVisible?: boolean;
  /** Mantém a coluna visível na extremidade; end também bloqueia o redimensionamento. */
  fixed?: 'start' | 'end';
}

/** Intervalo exibido por uma listagem paginada (campos do paginador do Laravel). */
export interface TableListRange {
  from: number | null;
  to: number | null;
  total: number;
}

/** Paginador do Laravel consumido pelo rodapé da listagem. */
export interface TableListPage extends TableListRange {
  current_page: number;
  last_page: number;
}

export interface TableListTableProps
  extends Omit<TableProps, 'children' | 'width' | 'summary'> {
  columns: TableListColumn[];
  /** Paginador da listagem: a barra mostra "1 a 10 de 14 registros". */
  page?: TableListRange;
  /** Texto livre, só para coleções sem paginação (ex.: abas do prontuário). */
  summary?: ReactNode;
  children: ReactNode;
}
