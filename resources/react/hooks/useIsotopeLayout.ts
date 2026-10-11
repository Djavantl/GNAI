import { useEffect, useRef, type RefObject } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import type {
  GalleryLayout,
  GalleryLayoutConstructor,
} from "./useIsotopeLayout.types";

/** Uses the original Isotope runtime, including its masonry and filter transitions. */
export function useIsotopeLayout(
  filter: string,
  itemSelector: string,
  items: readonly unknown[],
): RefObject<HTMLDivElement | null> {
  const host = useRef<HTMLDivElement>(null);
  const layout = useRef<GalleryLayout | null>(null);
  const selected = useRef(filter);
  selected.current = filter;
  useEffect(() => {
    let disposed = false;
    const element = host.current;
    if (!element) return;
    const initialize = async (): Promise<void> => {
      await loadExternalScript(
        "/assets/react/plugins/isotope/dist/isotope.pkgd.min.js",
      );
      await Promise.all(
        Array.from(element.querySelectorAll("img"), (img) =>
          img.decode().catch(() => undefined),
        ),
      );
      if (disposed) return;
      const Isotope = (
        window as unknown as { Isotope: GalleryLayoutConstructor }
      ).Isotope;
      layout.current = new Isotope(element, {
        itemSelector,
        filter: selected.current,
        animationOptions: { duration: 750, easing: "linear", queue: false },
      });
    };
    void initialize().catch(console.error);
    return () => {
      disposed = true;
      layout.current?.destroy();
      layout.current = null;
    };
  }, [itemSelector, items]);
  useEffect(() => {
    layout.current?.arrange({ filter });
  }, [filter]);
  return host;
}
