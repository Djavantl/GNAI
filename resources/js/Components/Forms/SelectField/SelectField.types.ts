export interface SelectFieldOption {
  value: string;
  label: string;
}
export interface SelectFieldGroup {
  label: string;
  options: SelectFieldOption[];
}
export interface SelectFieldCollection {
  select2: (
    options: Record<string, unknown> | "destroy",
  ) => SelectFieldCollection;
}
export interface SelectFieldRuntime {
  (element: HTMLElement): SelectFieldCollection;
}
