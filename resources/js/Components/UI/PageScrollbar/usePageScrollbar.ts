import { useLayoutEffect, useState } from 'react';
import type { PageScrollMetrics } from './PageScrollbar.types';

const initialMetrics: PageScrollMetrics = {
  top: 0,
  height: 0,
  scrollTop: 0,
  maxScroll: 0,
  thumbHeight: 0,
  thumbTop: 0,
  locked: false,
};

export function isPageScrollLocked(): boolean {
  return [document.documentElement, document.body].some((element) =>
    ['hidden', 'clip'].includes(getComputedStyle(element).overflowY),
  );
}

/** Keep native document scrolling, including existing plugins and window listeners. */
export function usePageScrollbar(belowTopBar: boolean): PageScrollMetrics {
  const [metrics, setMetrics] = useState<PageScrollMetrics>(initialMetrics);

  useLayoutEffect(() => {
    let frame = 0;
    let header: HTMLElement | null = null;
    const resizeObserver = new ResizeObserver(scheduleUpdate);

    function update(): void {
      frame = 0;
      const nextHeader = belowTopBar ? document.querySelector<HTMLElement>('.topbar') : null;
      if (nextHeader !== header) {
        if (header) resizeObserver.unobserve(header);
        header = nextHeader;
        if (header) resizeObserver.observe(header);
      }
      const top = Math.max(0, header?.getBoundingClientRect().bottom ?? 0);
      const height = Math.max(0, window.innerHeight - top);
      const documentHeight = document.documentElement.scrollHeight;
      const maxScroll = Math.max(0, documentHeight - window.innerHeight);
      const scrollTop = Math.max(0, Math.min(window.scrollY, maxScroll));
      const thumbHeight = maxScroll > 0
        ? Math.min(height, Math.max(24, height * height / (documentHeight - top)))
        : 0;
      const thumbTop = maxScroll > 0 ? (height - thumbHeight) * scrollTop / maxScroll : 0;
      const next: PageScrollMetrics = {
        top, height, maxScroll, scrollTop, thumbHeight, thumbTop,
        locked: isPageScrollLocked(),
      };
      setMetrics((previous) =>
        (Object.keys(next) as (keyof PageScrollMetrics)[]).every((key) => previous[key] === next[key])
          ? previous
          : next,
      );
    }

    function scheduleUpdate(): void {
      if (!frame) frame = window.requestAnimationFrame(update);
    }

    // React routes and plugin portals can replace the header or resize the page.
    const mutationObserver = new MutationObserver(scheduleUpdate);
    mutationObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'style'] });
    mutationObserver.observe(document.body, { attributes: true, attributeFilter: ['class', 'style'] });
    const root = document.getElementById('root');
    if (root) mutationObserver.observe(root, { childList: true, subtree: true });
    resizeObserver.observe(document.documentElement);
    resizeObserver.observe(document.body);
    window.addEventListener('scroll', scheduleUpdate, { passive: true });
    window.addEventListener('resize', scheduleUpdate);
    update();

    return () => {
      window.cancelAnimationFrame(frame);
      mutationObserver.disconnect();
      resizeObserver.disconnect();
      window.removeEventListener('scroll', scheduleUpdate);
      window.removeEventListener('resize', scheduleUpdate);
    };
  }, [belowTopBar]);

  return metrics;
}
