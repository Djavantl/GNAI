import type { CSSProperties } from 'react';

export interface SummernoteEditorProps {
  /** Trusted initial HTML, read once when the editor is mounted. */
  defaultValue?: string;
  mode?: 'standard' | 'inline';
  height?: number;
  label?: string;
  onChange?: (html: string) => void;
  className?: string;
  style?: CSSProperties;
}
export interface SummernoteCollection {
  summernote: (options: Record<string, unknown>) => SummernoteCollection;
  code: () => string;
  destroy: () => void;
  data: (key: string) => unknown;
  on: (events: string, callback: () => void) => SummernoteCollection;
  off: (events: string, handler?: (...args: unknown[]) => unknown) => SummernoteCollection;
  modal: (action: string) => void;
  tooltip: (action: string) => void;
}
export interface SummernoteRuntime {
  (element: Element | Document): SummernoteCollection;
  _data: (element: Document, key: 'events') => Record<string, Array<{ handler: (...args: unknown[]) => unknown }>> | undefined;
  summernote: { lang: Record<string, Record<string, Record<string, string>>> };
}
