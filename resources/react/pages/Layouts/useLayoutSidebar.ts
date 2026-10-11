import { useEffect, useLayoutEffect, useRef, useState } from "react";
import type { LayoutDefinition, LayoutKey } from "./Layouts.types";

interface LayoutSidebarState {
  key?: LayoutKey;
  collapsed: boolean;
  forced: boolean;
}

function initialState(page?: LayoutDefinition): LayoutSidebarState {
  return {
    key: page?.key,
    collapsed:
      Boolean(page?.initiallyCollapsed) ||
      window.matchMedia("(max-width: 990px)").matches,
    forced: Boolean(page?.initiallyCollapsed),
  };
}

export function useLayoutSidebar(page?: LayoutDefinition): {
  collapsed: boolean;
  toggle: () => void;
} {
  const [state, setState] = useState<LayoutSidebarState>(() =>
    initialState(page),
  );
  const current = state.key === page?.key ? state : initialState(page);
  const previousKey = useRef<LayoutKey | undefined>(page?.key);

  useLayoutEffect(() => {
    if (page || previousKey.current) window.scrollTo(0, 0);
    previousKey.current = page?.key;
  }, [page]);

  useEffect(() => {
    setState(initialState(page));
    const media = window.matchMedia("(max-width: 990px)");
    function resize(event: MediaQueryListEvent): void {
      setState((value) =>
        value.forced ? value : { ...value, collapsed: event.matches },
      );
    }
    media.addEventListener("change", resize);
    return () => media.removeEventListener("change", resize);
  }, [page]);

  function toggle(): void {
    setState({ key: page?.key, collapsed: !current.collapsed, forced: true });
  }

  return { collapsed: current.collapsed, toggle };
}
