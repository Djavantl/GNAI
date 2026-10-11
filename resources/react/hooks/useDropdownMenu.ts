import {
  useEffect,
  useRef,
  useState,
  type KeyboardEvent,
  type RefObject,
} from "react";

interface DropdownBehavior {
  host: RefObject<HTMLDivElement | null>;
  open: boolean;
  toggle: () => void;
  close: () => void;
  onKeyDown: (event: KeyboardEvent<HTMLDivElement>) => void;
}

export function useDropdownMenu(): DropdownBehavior {
  const [open, setOpen] = useState<boolean>(false);
  const host = useRef<HTMLDivElement>(null);

  function links(): HTMLAnchorElement[] {
    return Array.from(
      host.current?.querySelectorAll<HTMLAnchorElement>('[role="menuitem"]') ??
        [],
    );
  }

  useEffect(() => {
    if (!open) return;
    function outside(event: MouseEvent): void {
      if (!host.current?.contains(event.target as Node)) setOpen(false);
    }
    document.addEventListener("mousedown", outside);
    return () => document.removeEventListener("mousedown", outside);
  }, [open]);

  function onKeyDown(event: KeyboardEvent<HTMLDivElement>): void {
    if (event.key === "Escape") {
      if (!open) return;
      event.preventDefault();
      event.stopPropagation();
      setOpen(false);
      host.current
        ?.querySelector<HTMLElement>('[aria-haspopup="menu"]')
        ?.focus();
      return;
    }
    if (
      event.key !== "ArrowDown" &&
      event.key !== "ArrowUp" &&
      event.key !== " "
    )
      return;
    event.preventDefault();
    event.stopPropagation();
    if (!open) {
      setOpen(true);
      return;
    }
    const items = links();
    const current = items.indexOf(document.activeElement as HTMLAnchorElement);
    const next =
      event.key === "ArrowDown"
        ? Math.min(current + 1, items.length - 1)
        : event.key === "ArrowUp"
          ? Math.max(current - 1, 0)
          : Math.max(current, 0);
    items[next]?.focus();
  }

  return {
    host,
    open,
    toggle: () => setOpen((value) => !value),
    close: () => setOpen(false),
    onKeyDown,
  };
}
