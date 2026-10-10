export type InputControlType = 'input' | 'textarea' | 'select' | 'static';

export interface FormOption {
  label: string;
  value?: string;
}

export interface FormFieldConfig {
  label?: string;
  labelFor?: string;
  labelClassName?: string;
  controlClassName?: string;
  groupClassName?: string;
  type?: string;
  controlType?: InputControlType;
  id?: string;
  name?: string;
  placeholder?: string;
  defaultValue?: string;
  rows?: number;
  readOnly?: boolean;
  disabled?: boolean;
  helpText?: string;
  options?: FormOption[];
  multiple?: boolean;
  feedbackIcon?: string;
  state?: 'success' | 'warning' | 'error';
  afterControl?: string;
}

export interface DropdownMenuItem {
  label?: string;
  divider?: boolean;
}
