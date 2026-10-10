import { useLayoutEffect, useRef, useState, type RefObject } from "react";

export const SIDEBAR_SUBMENU_DURATION = 350;

export function useSidebarSubmenuHeight(
  isOpen: boolean,
  collapsed: boolean,
): {
  ref: RefObject<HTMLUListElement>;
  maxHeight: string;
  finishOpening: () => void;
} {
  const ref = useRef<HTMLUListElement>(null);
  const [maxHeight, setMaxHeight] = useState<string>("0px");

  useLayoutEffect(() => {
    const element = ref.current;
    if (!element) return;
    element.inert = collapsed || !isOpen;

    if (collapsed) {
      setMaxHeight(isOpen ? "none" : "0px");
      return;
    }

    if (!isOpen) {
      // Start the closing transition from its current, possibly uncapped height.
      element.style.maxHeight = `${element.getBoundingClientRect().height}px`;
      void element.offsetHeight;
      setMaxHeight("0px");
      return;
    }

    setMaxHeight(`${element.scrollHeight}px`);
    // Like slideDown's cleanup, leave an open branch free to grow with its children.
    const timer = window.setTimeout(
      () => setMaxHeight("none"),
      SIDEBAR_SUBMENU_DURATION,
    );
    return () => window.clearTimeout(timer);
  }, [collapsed, isOpen]);

  function finishOpening(): void {
    if (isOpen && !collapsed) setMaxHeight("none");
  }

  return { ref, maxHeight, finishOpening };
}
