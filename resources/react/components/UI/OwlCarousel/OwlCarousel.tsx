import { useEffect, useRef, type ReactNode } from 'react';
import { renderToStaticMarkup } from 'react-dom/server';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { OwlCarouselOptions, OwlCollection } from './OwlCarousel.types';
import './OwlCarousel.plugin.css';
import './OwlCarousel.theme.css';
import './OwlCarousel.css';

interface OwlCarouselProps {
  children: ReactNode;
  options: OwlCarouselOptions;
  theme?: boolean;
  label: string;
}

/** React owns the host; Owl owns only its generated track, clones and drag events. */
export default function OwlCarousel({
  children,
  options,
  theme = false,
  label,
}: OwlCarouselProps): React.JSX.Element {
  const host = useRef<HTMLDivElement>(null);
  const markup = renderToStaticMarkup(<>{children}</>);
  useEffect(() => {
    let cancelled = false;
    let collection: OwlCollection | undefined;
    let carousel: HTMLDivElement | undefined;
    let observer: ResizeObserver | undefined;
    let lastWidth = 0;
    async function initialize(): Promise<void> {
      await loadExternalScript('/assets/react/js/jquery.min.js');
      await loadExternalScript(
        '/assets/react/plugins/owl.carousel/owl.carousel.min.js',
      );
      if (cancelled || !host.current) return;
      carousel = document.createElement('div');
      carousel.className = `owl-carousel${theme ? ' owl-theme' : ''}`;
      carousel.innerHTML = markup;
      host.current.append(carousel);
      const runtime = (
        window as unknown as { jQuery: (element: Element) => OwlCollection }
      ).jQuery;
      collection = runtime(carousel);
      const update = (): void => {
        const instance = collection?.data('owl.carousel');
        if (!instance || !host.current || !carousel) return;
        host.current.dataset.activeIndex = String(
          instance.relative(instance.current()),
        );
        host.current.dataset.visibleItems = String(instance.settings.items);
        carousel
          .querySelectorAll<HTMLElement>('.owl-dot')
          .forEach((dot, index) => {
            dot.setAttribute('role', 'button');
            dot.setAttribute('tabindex', '0');
            dot.setAttribute('aria-label', `Página ${index + 1}`);
            dot.setAttribute(
              'aria-current',
              dot.classList.contains('active') ? 'true' : 'false',
            );
          });
      };
      collection.on(
        'initialized.owl.carousel.react changed.owl.carousel.react refreshed.owl.carousel.react translated.owl.carousel.react',
        update,
      );
      collection.owlCarousel(options);
      update();
      // The original engine listens to window resize; also cover sidebar/container changes.
      lastWidth = host.current.clientWidth;
      observer = new ResizeObserver(() => {
        const width = host.current?.clientWidth ?? 0;
        if (width > 0 && width !== lastWidth) {
          lastWidth = width;
          const instance = collection?.data('owl.carousel') as
            | { refresh?: () => void }
            | undefined;
          instance?.refresh?.();
        }
      });
      observer.observe(host.current);
    }
    void initialize().catch(console.error);
    return () => {
      cancelled = true;
      observer?.disconnect();
      collection?.off('.react');
      collection?.data('owl.carousel')?.destroy();
      carousel?.remove();
    };
  }, [markup, options, theme]);
  return (
    <div
      className={`owlCarouselRuntime${theme ? ' owlCarouselRuntime--themed' : ''}`}
      ref={host}
      role="region"
      aria-label={label}
      onClick={(event) => {
        if ((event.target as Element).closest('a[href="#"]'))
          event.preventDefault();
      }}
      onKeyDown={(event) => {
        const dot = (event.target as Element).closest<HTMLElement>('.owl-dot');
        if (dot && (event.key === 'Enter' || event.key === ' ')) {
          event.preventDefault();
          dot.click();
        }
      }}
    />
  );
}
