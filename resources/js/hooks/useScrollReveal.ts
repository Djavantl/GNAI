import { useLayoutEffect, type RefObject } from "react";

/** Reveal items once, at the same viewport threshold as the legacy timeline. */
export function useScrollReveal(
  host: RefObject<HTMLElement>,
  itemSelector: string,
  revealSelector: string,
  threshold = 0.75,
  viewportInset = 0,
  enabled = true,
): void {
  useLayoutEffect(() => {
    const element = host.current;
    if (!element || !enabled) return;
    const items = Array.from(
      element.querySelectorAll<HTMLElement>(itemSelector),
    );
    let frame = 0;

    items.forEach((item) => {
      if (
        item.getBoundingClientRect().top >
        window.innerHeight * threshold - viewportInset
      ) {
        item
          .querySelectorAll(revealSelector)
          .forEach((child) => child.classList.add("is-hidden"));
      }
    });

    const reveal = (): void => {
      frame = 0;
      items.forEach((item) => {
        if (
          item.getBoundingClientRect().top <=
            window.innerHeight * threshold - viewportInset &&
          item.querySelector(".is-hidden")
        ) {
          item.querySelectorAll(revealSelector).forEach((child) => {
            child.classList.remove("is-hidden");
            child.classList.add("bounce-in");
          });
        }
      });
    };
    const onScroll = (): void => {
      if (!frame) frame = window.requestAnimationFrame(reveal);
    };
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => {
      window.removeEventListener("scroll", onScroll);
      window.cancelAnimationFrame(frame);
      items.forEach((item) =>
        item
          .querySelectorAll(revealSelector)
          .forEach((child) => child.classList.remove("is-hidden", "bounce-in")),
      );
    };
  }, [host, itemSelector, revealSelector, threshold, viewportInset, enabled]);
}
