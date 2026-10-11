export interface GalleryLayout {
  arrange: (options: { filter: string }) => void;
  destroy: () => void;
}
export interface GalleryLayoutConstructor {
  new (element: HTMLElement, options: Record<string, unknown>): GalleryLayout;
}
