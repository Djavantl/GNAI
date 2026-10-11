import { useEffect, useMemo, useState } from "react";
import type { SidebarEntry, SidebarItem } from "./SidebarPrimary.types";
import {
  collectInitiallyOpenKeys,
  toggleSidebarBranch,
} from "./SidebarPrimary.helpers";

export function useSidebarBranches(
  entries: SidebarEntry[],
  currentItemKey?: string,
): {
  openKeyList: string[];
  toggleItem: (item: SidebarItem) => void;
} {
  const [openKeys, setOpenKeys] = useState<Set<string>>(
    () => new Set(collectInitiallyOpenKeys(entries, currentItemKey)),
  );
  const openKeyList = useMemo(() => Array.from(openKeys), [openKeys]);

  useEffect(() => {
    setOpenKeys(
      (current) =>
        new Set([
          ...current,
          ...collectInitiallyOpenKeys(entries, currentItemKey),
        ]),
    );
  }, [entries, currentItemKey]);

  function toggleItem(item: SidebarItem): void {
    setOpenKeys((current) => toggleSidebarBranch(current, entries, item));
  }

  return { openKeyList, toggleItem };
}
