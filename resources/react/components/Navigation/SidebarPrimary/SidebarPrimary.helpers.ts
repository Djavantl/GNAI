import type { SidebarEntry, SidebarItem, SidebarSectionTitle } from './SidebarPrimary.types';

export function isSectionTitle(entry: SidebarEntry): entry is SidebarSectionTitle {
  return 'title' in entry;
}

export function collectInitiallyOpenKeys(entries: SidebarEntry[], currentItemKey?: string): string[] {
  const keys: string[] = [];

  function walk(items: SidebarEntry[]): boolean {
    let hasActive = false;

    items.forEach((entry: SidebarEntry) => {
      if (isSectionTitle(entry)) {
        return;
      }

      const childHasActive = entry.children ? walk(entry.children) : false;
      const isActive = entry.key === currentItemKey || childHasActive;

      if (childHasActive) {
        keys.push(entry.key);
      }

      if (isActive) {
        hasActive = true;
      }
    });

    return hasActive;
  }

  walk(entries);
  return keys;
}

export function hasActiveDescendant(item: SidebarItem, currentItemKey?: string): boolean {
  if (item.key === currentItemKey) {
    return true;
  }

  return item.children?.some((child: SidebarItem) => hasActiveDescendant(child, currentItemKey)) ?? false;
}

export function collectBranchKeys(item: SidebarItem): string[] {
  return [item.key, ...(item.children?.flatMap((child: SidebarItem) => collectBranchKeys(child)) ?? [])];
}

export function findSiblingBranchKeys(entries: SidebarEntry[], targetKey: string): string[] | null {
  const items = entries.filter((entry: SidebarEntry): entry is SidebarItem => !isSectionTitle(entry));

  if (items.some((entry: SidebarItem) => entry.key === targetKey)) {
    return items.flatMap((entry: SidebarItem) => collectBranchKeys(entry));
  }

  for (const item of items) {
    if (!item.children?.length) {
      continue;
    }

    const nestedKeys = findSiblingBranchKeys(item.children, targetKey);

    if (nestedKeys) {
      return nestedKeys;
    }
  }

  return null;
}

export function toggleSidebarBranch(currentKeys: ReadonlySet<string>, entries: SidebarEntry[], item: SidebarItem): Set<string> {
  const nextKeys = new Set(currentKeys);
  const closing = nextKeys.has(item.key);
  const keysToClose = closing ? collectBranchKeys(item) : findSiblingBranchKeys(entries, item.key) ?? [];
  keysToClose.forEach((key) => nextKeys.delete(key));
  if (!closing) nextKeys.add(item.key);
  return nextKeys;
}
