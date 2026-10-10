import { useLayoutEffect, useRef, type RefObject } from "react";

/** Custombox uses a 5% top margin and scrolling when the content exceeds the viewport. */
export function useCrmDialogViewport(
  onOverflowChange: (overflow: boolean) => void,
): RefObject<HTMLFormElement> {
  const ref = useRef<HTMLFormElement>(null);
  useLayoutEffect(() => {
    const content = ref.current?.closest<HTMLElement>(".modalLayer__content");
    if (!content) return;
    const update = (): void =>
      onOverflowChange(content.offsetHeight >= window.innerHeight);
    update();
    const observer = new ResizeObserver(update);
    observer.observe(content);
    window.addEventListener("resize", update);
    return () => {
      observer.disconnect();
      window.removeEventListener("resize", update);
    };
  }, [onOverflowChange]);
  return ref;
}
