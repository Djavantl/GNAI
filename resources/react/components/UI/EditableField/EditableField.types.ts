export interface EditableOption { value: string | number; text: string; color?: string; }
export interface EditableFieldProps {
  id: string;
  type?: 'text' | 'select' | 'textarea' | 'combodate';
  mode?: 'inline' | 'popup';
  defaultValue?: string | number;
  initialText?: string;
  title: string;
  placeholder?: string;
  required?: boolean;
  options?: EditableOption[];
  sourceUrl?: string;
  placement?: 'top' | 'right';
  onChange?: (value: string | number) => void;
}
export interface EditableCollection {
  editable: (options: Record<string, unknown> | string) => EditableCollection;
  on: (event: string, callback: (event: Event, params: { newValue?: unknown }) => void) => EditableCollection;
  off: (events: string) => EditableCollection;
}
export interface EditableRuntime {
  (element: Element): EditableCollection;
  fn: { editableform: { buttons: string } };
}
