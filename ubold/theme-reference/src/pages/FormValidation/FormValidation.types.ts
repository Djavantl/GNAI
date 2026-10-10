import type { ReactNode } from 'react';

export interface BasicFieldConfig {
  id: string;
  label: string;
  name?: string;
  placeholder: string;
  type: string;
  required?: boolean;
  parsleyTrigger?: string;
  parsleyEqualTo?: string;
  parsleyType?: string;
}

export interface HorizontalFieldConfig extends BasicFieldConfig {
  labelCol?: 3 | 4;
  fieldCol?: 3 | 6 | 7;
}

export interface ValidationFieldConfig {
  label: string;
  placeholder?: string;
  type?: string;
  id?: string;
  required?: boolean;
  parsleyType?: string;
  parsleyEqualTo?: string;
  parsleyMinLength?: number;
  parsleyMaxLength?: number;
  parsleyLength?: string;
  parsleyMin?: number;
  parsleyMax?: number;
  parsleyPattern?: string;
  multiline?: boolean;
}

export interface CheckboxOptionConfig {
  id: string;
  label: string;
  parsleyMultiple: string;
  parsleyMinCheck?: number;
  parsleyMaxCheck?: number;
  required?: boolean;
}

export interface ValidationColumnConfig {
  title: string;
  description: ReactNode;
  fields: ValidationFieldConfig[];
  minCheckOptions?: CheckboxOptionConfig[];
  maxCheckOptions?: CheckboxOptionConfig[];
}
