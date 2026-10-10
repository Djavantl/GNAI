export interface NestableItem {
  id: number;
  children?: NestableItem[];
}

export type NestableVariant = 'default' | 'empty';
