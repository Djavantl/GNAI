export interface SelectFieldOption {
  value: string;
  label: string;
}
export interface SelectFieldGroup {
  label: string;
  options: SelectFieldOption[];
}
export interface SelectFieldCollection {
  select2: (options: Record<string, unknown> | string, ...args: unknown[]) => SelectFieldCollection;
  on: (event: string, handler: (event: { originalEvent?: Event }) => void) => SelectFieldCollection;
  off: (event: string) => SelectFieldCollection;
  data: (key: "select2") => { dropdown: { 0?: HTMLElement } } | undefined;
}
export interface SelectFieldRuntime {
  (element: HTMLElement): SelectFieldCollection;
}
