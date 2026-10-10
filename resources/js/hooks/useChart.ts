import { useEffect, useRef, type RefObject } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";

import type { ChartHandle, ChartMount } from './useChart.types';

/** A library owns only its plot; React owns the surrounding card and lifecycle. */
export function useChart(scripts: readonly string[], mount: ChartMount): RefObject<HTMLDivElement> {
  const ref = useRef<HTMLDivElement>(null);
  useEffect(() => {
    const element = ref.current;
    if (!element) return;
    let disposed = false;
    let chart: ChartHandle | undefined;
    let observer: ResizeObserver | undefined;
    let width = element.clientWidth;
    void (async () => {
      for (const script of scripts) await loadExternalScript(script);
      if (disposed) return;
      chart = mount(element);
      element.dataset.chartReady = "true";
      observer = new ResizeObserver(() => {
        if (element.clientWidth > 0 && element.clientWidth !== width) {
          width = element.clientWidth;
          chart?.resize?.();
        }
      });
      observer.observe(element);
    })().catch((error: unknown) => {
      console.error("Chart initialization failed", error);
    });
    return () => {
      disposed = true;
      observer?.disconnect();
      chart?.destroy();
      element.replaceChildren();
      delete element.dataset.chartReady;
    };
  }, [scripts, mount]);
  return ref;
}
