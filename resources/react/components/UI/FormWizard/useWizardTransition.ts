import { useLayoutEffect, type RefObject } from 'react';
import type { WizardTransition } from './FormWizard.types';

/** Match jQuery Steps' 200ms swing easing; fade is 200ms out, then 200ms in. */
export function useWizardTransition(
  rootRef: RefObject<HTMLDivElement | null>, current: number, previous: number | null,
  effect: WizardTransition, duration: number, onComplete: () => void
): void {
  useLayoutEffect(() => {
    if (previous === null) return;
    const panels = rootRef.current?.querySelectorAll<HTMLElement>(':scope > .formWizard__content > .formWizard__panel');
    const outgoing = panels?.[previous];
    const incoming = panels?.[current];
    if (!outgoing || !incoming) return;
    const width = outgoing.offsetWidth;
    const initialLeft = outgoing.offsetLeft;
    const direction = current > previous ? 1 : -1;
    const total = effect === 'fade' ? duration * 2 : duration;
    const started = performance.now();
    let frame = 0;
    const swing = (progress: number): number => (1 - Math.cos(Math.PI * progress)) / 2;
    function render(now: number): void {
      const elapsed = Math.max(0, now - started);
      const progress = duration === 0 ? 1 : Math.min(elapsed / duration, 1);
      if (effect === 'slideLeft') {
        outgoing!.style.left = `${initialLeft + (-direction * width - initialLeft) * swing(progress)}px`;
        incoming!.style.left = `${direction * width * (1 - swing(progress))}px`;
      } else {
        outgoing!.style.opacity = `${1 - swing(progress)}`;
        outgoing!.style.display = elapsed < duration ? 'block' : 'none';
        incoming!.style.display = elapsed < duration ? 'none' : 'block';
        incoming!.style.opacity = `${swing(duration === 0 ? 1 : Math.min(Math.max((elapsed - duration) / duration, 0), 1))}`;
      }
      if (elapsed < total) frame = requestAnimationFrame(render);
      else {
        outgoing!.style.opacity = '';
        outgoing!.style.display = 'none';
        incoming!.style.opacity = '';
        incoming!.style.display = 'block';
        onComplete();
      }
    }
    render(started);
    return () => cancelAnimationFrame(frame);
  }, [rootRef, current, previous, effect, duration, onComplete]);
}
