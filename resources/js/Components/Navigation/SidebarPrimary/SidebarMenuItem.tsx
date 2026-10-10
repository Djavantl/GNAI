import Label from '@/Components/UI/Label/Label';
import type { SidebarItem } from './SidebarPrimary.types';
import { joinClassNames } from '@/utils/joinClassNames';

interface SidebarMenuItemProps {
  item: SidebarItem;
  depth: number;
  collapsed: boolean;
  isOpen: boolean;
  isActive: boolean;
  isCollapsedFlyoutActive?: boolean;
  onItemClick: (event: React.MouseEvent<HTMLAnchorElement>, item: SidebarItem) => void;
  onTopLevelHoverStart?: (item: SidebarItem, element: HTMLLIElement) => void;
  onTopLevelHoverEnd?: () => void;
  children?: React.ReactNode;
}

export default function SidebarMenuItem({
  item,
  depth,
  collapsed,
  isOpen,
  isActive,
  isCollapsedFlyoutActive,
  onItemClick,
  onTopLevelHoverStart,
  onTopLevelHoverEnd,
  children
}: SidebarMenuItemProps): JSX.Element {
  const hasChildren = Boolean(item.children?.length);

  return (
    <li
      className={joinClassNames(
        hasChildren && 'has_sub',
        isActive && 'active',
        isOpen && 'is-open',
        isCollapsedFlyoutActive && 'sidebarPrimary__item--collapsedFlyoutActive'
      )}
      onMouseEnter={(event: React.MouseEvent<HTMLLIElement>) => {
        if (collapsed && depth === 0 && hasChildren) {
          onTopLevelHoverStart?.(item, event.currentTarget);
        }
      }}
      onMouseLeave={() => {
        if (collapsed && depth === 0 && hasChildren) {
          onTopLevelHoverEnd?.();
        }
      }}
    >
      <a
        href={item.href ?? '#'}
        className={joinClassNames((depth === 0 || item.waves) && 'waves-effect waves-light', isActive && depth === 0 && 'active', isOpen && 'subdrop')}
        aria-label={collapsed && depth === 0 ? item.label : undefined}
        aria-expanded={hasChildren ? (collapsed ? Boolean(isCollapsedFlyoutActive) : isOpen) : undefined}
        aria-controls={hasChildren ? `sidebar-${collapsed ? 'flyout' : 'submenu'}-${item.key}` : undefined}
        onClick={(event: React.MouseEvent<HTMLAnchorElement>) => onItemClick(event, item)}
      >
        {item.iconClass ? <i className={item.iconClass} aria-hidden="true" /> : null}

        {item.labelBadge ? (
          <Label className="pull-right sidebarPrimary__label" variant={item.labelBadge.variant} ariaHidden>
            {item.labelBadge.text}
          </Label>
        ) : null}

        <span>{depth === 0 ? ` ${item.label} ` : item.label}</span>
      </a>

      {hasChildren ? children : null}
    </li>
  );
}
