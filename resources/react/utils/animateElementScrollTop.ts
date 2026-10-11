export function getSwingProgress(progress: number): number {
  return 0.5 - Math.cos(progress * Math.PI) / 2;
}

export function animateElementScrollTop(element: HTMLElement, targetScrollTop: number, duration = 300): () => void {
  const startScrollTop = element.scrollTop;
  const scrollDistance = targetScrollTop - startScrollTop;
  const startTime = window.performance.now();
  let animationFrame = 0;
  let cancelled = false;

  function step(currentTime: number): void {
    if (cancelled) {
      return;
    }

    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const easedProgress = getSwingProgress(progress);

    element.scrollTop = startScrollTop + scrollDistance * easedProgress;

    if (progress < 1) {
      animationFrame = window.requestAnimationFrame(step);
    }
  }

  animationFrame = window.requestAnimationFrame(step);

  return () => {
    cancelled = true;
    window.cancelAnimationFrame(animationFrame);
  };
}
