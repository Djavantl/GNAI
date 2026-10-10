// Narrow runtime boundary for the existing jQuery table engines.
export interface JQueryTable {
  [index: number]: HTMLElement;
  length: number;
  data: (key: string, value?: unknown) => any;
  on: (...args: any[]) => JQueryTable;
  off: (...args: any[]) => JQueryTable;
  find: (selector: string) => JQueryTable;
  trigger: (name: string, data?: unknown) => JQueryTable;
  val: (value?: unknown) => any;
  prop: (key: string, value?: unknown) => any;
  attr: (key: string, value?: unknown) => any;
  closest: (selector: string) => JQueryTable;
  remove: () => JQueryTable;
  DataTable: (options?: Record<string, unknown>) => any;
  bootstrapTable: (...args: any[]) => any;
  footable: (options?: Record<string, unknown>) => JQueryTable;
  responsiveTable: (options?: Record<string, unknown>) => JQueryTable;
}
export interface TableRuntime {
  (element: unknown): JQueryTable;
  fn: Record<string, any>;
}
export type TableInitializer = (
  runtime: TableRuntime,
  host: HTMLDivElement,
  table: HTMLTableElement,
) => void | (() => void);
