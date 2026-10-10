import { useEffect, useRef } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import type { TextAnimation, TextAnimationRuntime } from "./RotatingText.types";

export default function RotatingText({
  texts,
  interval = 3000,
}: {
  texts: readonly string[];
  interval?: number;
}): JSX.Element {
  const host = useRef<HTMLSpanElement>(null);
  useEffect(() => {
    let disposed = false;
    let timer: number | undefined;
    let animation: TextAnimation | undefined;
    const element = host.current;
    async function initialize(): Promise<void> {
      await loadExternalScript("/assets/gnai/js/jquery.min.js");
      if (disposed || !element || !texts.length) return;
      const runtime = (window as unknown as { jQuery: TextAnimationRuntime })
        .jQuery;
      animation = runtime(element).text(texts[0]);
      // Preserve the original rotator's queue, easing and fade timings.
      timer = window.setInterval(() => {
        animation?.fadeOut(interval, () => {
          if (disposed) return;
          const index = texts.indexOf(element.textContent ?? "");
          animation?.text(texts[(index + 1) % texts.length]).fadeIn(interval);
        });
      }, interval);
    }
    void initialize().catch(console.error);
    return () => {
      disposed = true;
      window.clearInterval(timer);
      animation?.stop(true, false);
      element?.style.removeProperty("opacity");
      element?.style.removeProperty("display");
    };
  }, [texts, interval]);
  return <span ref={host}>{texts[0]}</span>;
}
