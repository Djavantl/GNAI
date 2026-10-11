import type { ReactNode } from 'react';

export interface SelectOption {
  label: string;
  value: string;
}

export interface SelectOptionGroup {
  label: string;
  options: SelectOption[];
}

export interface SwitcheryItem {
  color: string;
  secondaryColor?: string;
  size?: 'small' | 'large';
  checked?: boolean;
}

export interface BootstrapSelectDemo {
  className?: string;
  dataStyle: string;
  disabled?: boolean;
  liveSearch?: boolean;
  maxOptions?: number;
  multiple?: boolean;
  options: Array<SelectOption & { icon?: string; title?: string }>;
  optgroups?: Array<{
    label: string;
    options: SelectOption[];
  }>;
  selectedTextFormat?: string;
  showTick?: boolean;
}

export interface FileStyleDemo {
  label: string;
  attributes: Record<string, string | boolean>;
}

export interface TouchSpinInputConfig {
  id?: string;
  label: string;
  name: string;
  value?: string;
  className?: string;
  attributes?: Record<string, string | number | boolean>;
  afterInput?: ReactNode;
}

export interface MaxLengthInputConfig {
  id: string;
  label: string;
  description: ReactNode;
  multiline?: boolean;
  placeholder?: string;
}
