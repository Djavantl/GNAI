import type { SidebarItem } from './SidebarPrimary.types';
import { hasActiveDescendant } from './SidebarPrimary.helpers';
import { joinClassNames } from '@/utils/joinClassNames';

interface SidebarCollapsedFlyoutProps {
  item: SidebarItem;
  currentItemKey?: string;
  openKeyList: string[];
  top: number;
  bodyRef?: React.Ref<HTMLDivElement>;
  onItemClick: (event: React.MouseEvent<HTMLAnchorElement>, item: SidebarItem) => void;
  onMouseEnter: () => void;
  onMouseLeave: () => void;
}

function FlyoutItems({
  entries,
  currentItemKey,
  openKeyList,
  onItemClick
}: {
  entries: SidebarItem[];
  currentItemKey?: string;
  openKeyList: string[];
  onItemClick: (event: React.MouseEvent<HTMLAnchorElement>, item: SidebarItem) => void;
}): JSX.Element {
  return (
    <>
      {entries.map((entry: SidebarItem) => {
        const hasChildren = Boolean(entry.children?.length);

        return (
          <li key={entry.key} className={joinClassNames(hasChildren && 'has_sub', hasActiveDescendant(entry, currentItemKey) && 'active')}>
            <a className={joinClassNames(entry.waves && 'waves-effect waves-light', openKeyList.includes(entry.key) && 'subdrop')} aria-current={entry.key === currentItemKey ? 'page' : undefined} href={entry.href ?? '#'} onClick={(event: React.MouseEvent<HTMLAnchorElement>) => onItemClick(event, entry)}>
              <span>{entry.label}</span>
            </a>

            {hasChildren ? (
              <ul className="list-unstyled sidebarPrimary__collapsedNestedMenu">
                <FlyoutItems currentItemKey={currentItemKey} openKeyList={openKeyList} entries={entry.children ?? []} onItemClick={onItemClick} />
              </ul>
            ) : null}
          </li>
        );
      })}
    </>
  );
}

export default function SidebarCollapsedFlyout({
  item,
  currentItemKey,
  openKeyList,
  top,
  bodyRef,
  onItemClick,
  onMouseEnter,
  onMouseLeave
}: SidebarCollapsedFlyoutProps): JSX.Element {
  if (!item.children?.length) {
    return <></>;
  }

  return (
    <div
      id={`sidebar-flyout-${item.key}`}
      className={joinClassNames('sidebarPrimary__collapsedFlyout', item.children.some((entry) => Boolean(entry.children?.length)) && 'sidebarPrimary__collapsedFlyout--nested')}
      style={{ top: `${top}px` }}
      onMouseEnter={onMouseEnter}
      onMouseLeave={onMouseLeave}
    >
      <div ref={bodyRef} className="sidebarPrimary__collapsedFlyoutBody">
        <ul className="list-unstyled sidebarPrimary__collapsedFlyoutMenu">
          <FlyoutItems currentItemKey={currentItemKey} openKeyList={openKeyList} entries={item.children} onItemClick={onItemClick} />
        </ul>
      </div>
    </div>
  );
}
