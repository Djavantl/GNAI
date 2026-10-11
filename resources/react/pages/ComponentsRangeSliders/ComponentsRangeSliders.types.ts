export interface IonRangeSliderItem {
  id: string;
  title: string;
  description: string;
  titleBold?: boolean;
  options?: Record<string, unknown>;
}

export type BootstrapSliderVariant = 'default' | 'primary' | 'info' | 'success' | 'warning' | 'danger' | 'custom' | 'purple' | 'pink' | 'inverse';

export interface BootstrapSliderItem {
  variant: BootstrapSliderVariant;
  min: number;
  max: number;
  value: number | [number, number];
  orientation?: 'horizontal' | 'vertical';
  selection?: 'before' | 'after' | 'none';
  step?: number;
  tooltip?: 'show' | 'hide' | 'always';
}
