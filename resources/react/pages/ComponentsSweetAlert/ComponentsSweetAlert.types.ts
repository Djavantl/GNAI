export type SweetAlertKind = 'basic' | 'title' | 'success' | 'warning' | 'params' | 'image' | 'timer';

export interface SweetAlertExample {
  id: SweetAlertKind;
  label: string;
  buttonLabel: string;
}
