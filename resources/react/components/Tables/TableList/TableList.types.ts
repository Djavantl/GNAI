import type { ReactNode } from 'react';
import type { TableProps } from '@/components/Tables/Table/Table';

export interface TableListColumn {
  id: string;
  label: string;
  width?: number | string;
  minWidth?: number;
  hideable?: boolean;
  fixed?: 'start' | 'end';
}

export interface TableListTableProps
  extends Omit<TableProps, 'children' | 'width'> {
  columns: TableListColumn[];
  summary: ReactNode;
  children: ReactNode;
}
