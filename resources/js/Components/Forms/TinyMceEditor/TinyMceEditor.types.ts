export interface TinyMceStyleFormat {
  title: string;
  inline?: string;
  block?: string;
  selector?: string;
  classes?: string;
  styles?: Record<string, string>;
}
export interface TinyMceEditorProps {
  /** Initial trusted HTML, read when the component is mounted. */
  defaultValue?: string;
  height?: number;
  label?: string;
  onChange?: (html: string) => void;
  toolbar?: string;
  styleFormats?: TinyMceStyleFormat[];
}
export interface TinyMceInstance {
  initialized?: boolean;
  getContent: () => string;
  on: (events: string, handler: () => void) => void;
  remove: () => void;
  getContainer: () => HTMLElement;
  iframeElement?: HTMLIFrameElement;
}
export interface TinyMceRuntime {
  init: (options: Record<string, unknown>) => void;
  addI18n: (locale: string, translations: Record<string, string>) => void;
}
