export interface TableCellData {
  text: string;
  tag?: string;
  attributes: Record<string, string>;
  badge?: string;
  link?: boolean;
  secondary?: string;
  actions?: boolean;
}
export interface TableRowData {
  className?: string;
  cells: TableCellData[];
}
export interface TableDefinition {
  attributes: Record<string, string>;
  head: TableCellData[][];
  body: TableRowData[];
}
