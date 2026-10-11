import { useRef, type KeyboardEvent, type PointerEvent } from 'react';
import { isPageScrollLocked, usePageScrollbar } from './usePageScrollbar';
import type { PageScrollbarProps } from './PageScrollbar.types';
import './PageScrollbar.css';

export default function PageScrollbar({ belowTopBar = true }: PageScrollbarProps): React.JSX.Element | null {
  const metrics = usePageScrollbar(belowTopBar);
  const drag = useRef<{ pointerId: number; startY: number; startScroll: number } | null>(null);

  function scrollTo(top: number): void {
    if (isPageScrollLocked()) return;
    window.scrollTo({ top: Math.max(0, Math.min(top, metrics.maxScroll)), behavior: 'auto' });
  }

  function handlePointerDown(event: PointerEvent<HTMLDivElement>): void {
    if (event.button !== 0 || metrics.locked) return;
    event.preventDefault();
    if ((event.target as HTMLElement).closest('.pageScrollbar__thumb')) {
      drag.current = { pointerId: event.pointerId, startY: event.clientY, startScroll: window.scrollY };
      event.currentTarget.setPointerCapture(event.pointerId);
      event.currentTarget.focus();
    } else {
      const position = event.clientY - metrics.top;
      scrollTo(window.scrollY + (position < metrics.thumbTop ? -metrics.height : metrics.height));
    }
  }

  function handlePointerMove(event: PointerEvent<HTMLDivElement>): void {
    const active = drag.current;
    if (!active || event.pointerId !== active.pointerId) return;
    const travel = metrics.height - metrics.thumbHeight;
    if (travel > 0) scrollTo(active.startScroll + (event.clientY - active.startY) * metrics.maxScroll / travel);
  }

  function stopDrag(): void {
    drag.current = null;
  }

  function handleKeyDown(event: KeyboardEvent<HTMLDivElement>): void {
    const targets: Record<string, number> = {
      ArrowUp: window.scrollY - 40,
      ArrowDown: window.scrollY + 40,
      PageUp: window.scrollY - metrics.height,
      PageDown: window.scrollY + metrics.height,
      Home: 0,
      End: metrics.maxScroll,
    };
    if (targets[event.key] === undefined) return;
    event.preventDefault();
    scrollTo(targets[event.key]);
  }

  if (metrics.thumbHeight <= 0) return null;

  return (
    <div
      className="pageScrollbar"
      style={{ top: metrics.top, height: metrics.height }}
      role="scrollbar"
      aria-label="Rolagem da página"
      aria-controls="root"
      aria-orientation="vertical"
      aria-valuemin={0}
      aria-valuemax={Math.round(metrics.maxScroll)}
      aria-valuenow={Math.round(metrics.scrollTop)}
      aria-disabled={metrics.locked}
      tabIndex={metrics.locked ? -1 : 0}
      onPointerDown={handlePointerDown}
      onPointerMove={handlePointerMove}
      onPointerUp={stopDrag}
      onPointerCancel={stopDrag}
      onLostPointerCapture={stopDrag}
      onKeyDown={handleKeyDown}
    >
      <div className="pageScrollbar__thumb" style={{ height: metrics.thumbHeight, transform: `translateY(${metrics.thumbTop}px)` }} />
    </div>
  );
}
