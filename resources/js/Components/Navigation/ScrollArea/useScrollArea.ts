import { useEffect, useRef, type RefObject } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
interface NiceScrollHandle {
  resize(): void;
  remove(): void;
}
interface NiceScrollCollection {
  niceScroll(options: Record<string, string>): NiceScrollHandle;
}
interface NiceScrollWindow extends Window {
  jQuery?: (element: HTMLElement) => NiceScrollCollection;
}
export function useScrollArea(): RefObject<HTMLDivElement> {
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const element = ref.current;
    let disposed = false;
    let scroll: NiceScrollHandle | undefined;
    let observer: ResizeObserver | undefined;
    void (async () => {
      await loadExternalScript("/assets/gnai/js/jquery.min.js");
      await loadExternalScript("/assets/gnai/js/jquery.nicescroll.js");
      if (disposed || !element) return;
      scroll = (window as NiceScrollWindow)
        .jQuery?.(element)
        .niceScroll({
          cursorcolor: "#98a6ad",
          cursorwidth: "6px",
          cursorborderradius: "5px",
        });
      observer = new ResizeObserver(() => scroll?.resize());
      observer.observe(element);
      if (element.firstElementChild)
        observer.observe(element.firstElementChild);
    })().catch((error: unknown) =>
      console.error("Scroll initialization failed", error),
    );
    return () => {
      disposed = true;
      observer?.disconnect();
      scroll?.remove();
    };
  }, []);
  return ref;
}
