export interface LightboxItem {
  [index: number]: HTMLElement;
  magnificPopup: (options: Record<string, unknown>) => LightboxItem;
  off: (event: string) => LightboxItem;
  removeData: (key: string) => LightboxItem;
}
export interface LightboxRuntime {
  (items: HTMLElement[] | Element): LightboxItem;
  magnificPopup: {
    instance: {
      isOpen: boolean;
      currItem?: { el?: { [index: number]: HTMLElement } };
      close: () => void;
    };
  };
}
