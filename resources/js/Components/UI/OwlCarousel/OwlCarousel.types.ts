export interface OwlCarouselOptions {
  loop: boolean;
  autoplay: boolean;
  autoplayTimeout: number;
  autoplayHoverPause?: boolean;
  animateOut?: string | false;
  items?: number;
  margin?: number;
  nav?: boolean;
  dots?: boolean;
  responsive?: Record<number, { items: number }>;
}
export interface OwlCarouselInstance {
  current(): number;
  relative(index: number): number;
  settings: { items: number };
  destroy(): void;
}
export interface OwlCollection {
  owlCarousel(options: OwlCarouselOptions): OwlCollection;
  data(key: string): OwlCarouselInstance | undefined;
  on(events: string, handler: () => void): OwlCollection;
  off(events: string): OwlCollection;
}
