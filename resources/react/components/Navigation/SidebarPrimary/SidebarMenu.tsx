import SidebarMenuItem from './SidebarMenuItem';
import SidebarSubmenu from '@/components/Navigation/SidebarSubmenu/SidebarSubmenu';
import type { SidebarEntry, SidebarItem, SidebarSectionTitle } from './SidebarPrimary.types';
import { joinClassNames } from '@/utils/joinClassNames';

interface SidebarMenuProps {
  entries: SidebarEntry[];
  depth?: number;
  className?: string;
  wrap?: boolean;
  collapsed: boolean;
  openKeyList: string[];
  activeCollapsedFlyoutKey?: string;
  onItemClick: (event: React.MouseEvent<HTMLAnchorElement>, item: SidebarItem) => void;
  onTopLevelHoverStart?: (item: SidebarItem, element: HTMLLIElement) => void;
  onTopLevelHoverEnd?: () => void;
  isSectionTitle: (entry: SidebarEntry) => entry is SidebarSectionTitle;
  hasActiveDescendant: (item: SidebarItem) => boolean;
}

export default function SidebarMenu({
  entries,
  depth = 0,
  className,
  wrap = true,
  collapsed,
  openKeyList,
  activeCollapsedFlyoutKey,
  onItemClick,
  onTopLevelHoverStart,
  onTopLevelHoverEnd,
  isSectionTitle,
  hasActiveDescendant
}: SidebarMenuProps): React.JSX.Element {
  const content = (
    <>
      {entries.map((entry: SidebarEntry) => {
        if (isSectionTitle(entry)) {
          return (
            <li key={entry.key} className="text-muted menu-title">
              {entry.title}
            </li>
          );
        }

        const isOpen = openKeyList.includes(entry.key);
        const isActive = hasActiveDescendant(entry);

        return (
          <SidebarMenuItem
            key={entry.key}
            item={entry}
            depth={depth}
            collapsed={collapsed}
            isOpen={isOpen}
            isActive={isActive}
            isCollapsedFlyoutActive={depth === 0 && activeCollapsedFlyoutKey === entry.key}
            onItemClick={onItemClick}
            onTopLevelHoverStart={depth === 0 ? onTopLevelHoverStart : undefined}
            onTopLevelHoverEnd={depth === 0 ? onTopLevelHoverEnd : undefined}
          >
            {entry.children?.length ? (
              <SidebarSubmenu id={`sidebar-submenu-${entry.key}`} collapsed={collapsed} isOpen={isOpen}>
                <SidebarMenu
                  entries={entry.children}
                  depth={depth + 1}
                  wrap={false}
                  collapsed={collapsed}
                  openKeyList={openKeyList}
                  activeCollapsedFlyoutKey={activeCollapsedFlyoutKey}
                  onItemClick={onItemClick}
                  onTopLevelHoverStart={onTopLevelHoverStart}
                  onTopLevelHoverEnd={onTopLevelHoverEnd}
                  isSectionTitle={isSectionTitle}
                  hasActiveDescendant={hasActiveDescendant}
                />
              </SidebarSubmenu>
            ) : null}
          </SidebarMenuItem>
        );
      })}
    </>
  );

  if (!wrap) {
    return <>{content}</>;
  }

  return <ul className={joinClassNames(depth > 0 && 'list-unstyled', className)}>{content}</ul>;
}
