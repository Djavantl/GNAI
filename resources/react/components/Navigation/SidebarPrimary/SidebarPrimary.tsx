import { useEffect, useRef, useState } from 'react';
import './SidebarPrimary.css';
import './SidebarPrimary.small.css';
import SidebarUser from '@/components/Navigation/SidebarUser/SidebarUser';
import type { SidebarUserDetails } from '@/components/Navigation/SidebarUser/SidebarUser.types';
import type { SidebarItem } from './SidebarPrimary.types';
import { useSidebarBranches } from './useSidebarBranches';
import { sidebarEntries } from './SidebarPrimary.data';
import { hasActiveDescendant, isSectionTitle } from './SidebarPrimary.helpers';
import SidebarMenu from './SidebarMenu';
import SidebarCollapsedFlyout from './SidebarCollapsedFlyout';
import SidebarScrollbar from './SidebarScrollbar';
import { startAppPageTransition } from '@/utils/appPageTransition';

interface SidebarPrimaryProps {
  collapsed: boolean;
  currentItemKey?: string;
  smallMenu?: boolean;
  user?: SidebarUserDetails;
}

export default function SidebarPrimary({ collapsed, currentItemKey, smallMenu = false, user }: SidebarPrimaryProps): React.JSX.Element {
  const { openKeyList, toggleItem } = useSidebarBranches(sidebarEntries, currentItemKey);
  const [scrollbarVisible, setScrollbarVisible] = useState<boolean>(false);
  const [thumbHeight, setThumbHeight] = useState<number>(0);
  const [thumbTop, setThumbTop] = useState<number>(0);
  const [hoveredCollapsedItem, setHoveredCollapsedItem] = useState<SidebarItem | null>(null);
  const [hoveredCollapsedItemTop, setHoveredCollapsedItemTop] = useState<number>(0);
  const [hoveredCollapsedItemHeight, setHoveredCollapsedItemHeight] = useState<number>(0);
  const [hoveredCollapsedFlyoutWidth, setHoveredCollapsedFlyoutWidth] = useState<number>(190);
  const sidebarRef = useRef<HTMLElement | null>(null);
  const innerRef = useRef<HTMLDivElement | null>(null);
  const collapsedFlyoutRef = useRef<HTMLDivElement | null>(null);
  const hideTimerRef = useRef<number | null>(null);
  const collapsedFlyoutHideTimerRef = useRef<number | null>(null);

  useEffect(() => {
    if (!collapsed) {
      if (collapsedFlyoutHideTimerRef.current !== null) {
        window.clearTimeout(collapsedFlyoutHideTimerRef.current);
        collapsedFlyoutHideTimerRef.current = null;
      }

      setHoveredCollapsedItem(null);
    }
  }, [collapsed]);

  useEffect(() => {
    if (!collapsed || !hoveredCollapsedItem || !collapsedFlyoutRef.current) {
      return;
    }

    const element = collapsedFlyoutRef.current;

    function updateWidth(): void {
      const nextWidth = Math.max(190, element.getBoundingClientRect().width);
      setHoveredCollapsedFlyoutWidth(nextWidth);
    }

    updateWidth();

    if (typeof ResizeObserver === 'undefined') {
      return;
    }

    const observer = new ResizeObserver(() => {
      updateWidth();
    });

    observer.observe(element);

    return () => {
      observer.disconnect();
    };
  }, [collapsed, hoveredCollapsedItem]);

  useEffect(() => {
    const element = innerRef.current;

    if (!element) {
      return;
    }

    function clearHideTimer(): void {
      if (hideTimerRef.current !== null) {
        window.clearTimeout(hideTimerRef.current);
        hideTimerRef.current = null;
      }
    }

    function queueHide(): void {
      clearHideTimer();
      hideTimerRef.current = window.setTimeout(() => {
        setScrollbarVisible(false);
      }, 1000);
    }

    const updateThumb = (): void => {
      const visibleHeight = element.clientHeight;
      const scrollHeight = element.scrollHeight;

      if (scrollHeight <= visibleHeight) {
        setThumbHeight(0);
        setThumbTop(0);
        setScrollbarVisible(false);
        return;
      }

      const nextThumbHeight = Math.max((visibleHeight / scrollHeight) * visibleHeight, 30);
      const maxThumbTop = visibleHeight - nextThumbHeight;
      const scrollRatio = element.scrollTop / (scrollHeight - visibleHeight);
      const nextThumbTop = maxThumbTop * scrollRatio;

      setThumbHeight(nextThumbHeight);
      setThumbTop(nextThumbTop);
    };

    function handleScroll(): void {
      updateThumb();
      setScrollbarVisible(true);
      queueHide();
    }

    const handleMouseEnter = (): void => {
      updateThumb();
      if (thumbHeight > 0 || element.scrollHeight > element.clientHeight) {
        setScrollbarVisible(true);
      }
      queueHide();
    };

    function handleMouseLeave(): void {
      queueHide();
    }

    updateThumb();
    element.addEventListener('scroll', handleScroll, { passive: true });
    element.addEventListener('mouseenter', handleMouseEnter);
    element.addEventListener('mouseleave', handleMouseLeave);
    window.addEventListener('resize', updateThumb);

    return () => {
      clearHideTimer();
      element.removeEventListener('scroll', handleScroll);
      element.removeEventListener('mouseenter', handleMouseEnter);
      element.removeEventListener('mouseleave', handleMouseLeave);
      window.removeEventListener('resize', updateThumb);
    };
  }, [thumbHeight]);

  function handleItemClick(event: React.MouseEvent<HTMLAnchorElement>, item: SidebarItem): void {
    event.currentTarget.blur();

    if (item.children?.length) {
      event.preventDefault();

      if (!collapsed) {
        toggleItem(item);
      }

      return;
    }

    if (!item.href || item.href === '#' || item.href.startsWith('javascript:')) {
      event.preventDefault();
      if (!collapsed) toggleItem(item);
      return;
    }

    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
      return;
    }

    event.preventDefault();

    const targetUrl = new URL(event.currentTarget.href);

    if (targetUrl.origin !== window.location.origin) {
      window.setTimeout(() => {
        window.location.href = targetUrl.href;
      }, 180);
      return;
    }

    const nextPath = `${targetUrl.pathname}${targetUrl.search}${targetUrl.hash}`;
    const currentPath = `${window.location.pathname}${window.location.search}${window.location.hash}`;

    if (nextPath === currentPath) {
      return;
    }

    startAppPageTransition();

    window.setTimeout(() => {
      window.history.pushState({}, '', nextPath);
      window.dispatchEvent(new PopStateEvent('popstate'));
    }, 180);
  }

  function clearCollapsedFlyoutHideTimer(): void {
    if (collapsedFlyoutHideTimerRef.current !== null) {
      window.clearTimeout(collapsedFlyoutHideTimerRef.current);
      collapsedFlyoutHideTimerRef.current = null;
    }
  }

  function showCollapsedFlyout(item: SidebarItem, element: HTMLLIElement): void {
    if (!collapsed || !item.children?.length) {
      return;
    }

    clearCollapsedFlyoutHideTimer();

    const sidebarElement = sidebarRef.current;

    if (!sidebarElement) {
      setHoveredCollapsedItem(item);
      setHoveredCollapsedItemTop(0);
      return;
    }

    const itemRect = element.getBoundingClientRect();
    const sidebarRect = sidebarElement.getBoundingClientRect();

    setHoveredCollapsedItem(item);
    setHoveredCollapsedItemTop(itemRect.top - sidebarRect.top + itemRect.height);
    setHoveredCollapsedItemHeight(itemRect.height);
  }

  function queueCollapsedFlyoutHide(): void {
    clearCollapsedFlyoutHideTimer();
    collapsedFlyoutHideTimerRef.current = window.setTimeout(() => {
      setHoveredCollapsedItem(null);
    }, 120);
  }

  return (
    <aside ref={sidebarRef} className={`sidebarPrimary left side-menu ${collapsed ? 'sidebarPrimary--collapsed' : ''} ${smallMenu ? 'sidebarPrimary--small' : ''}`} aria-label="Menu lateral principal">
      <div ref={innerRef} className="sidebarPrimary__inner sidebar-inner">
        {user && !collapsed ? <SidebarUser user={user} /> : null}
        <div id="sidebar-menu" className="sidebarPrimary__menu">
          <SidebarMenu
            entries={sidebarEntries}
            collapsed={collapsed}
            openKeyList={openKeyList}
            activeCollapsedFlyoutKey={hoveredCollapsedItem?.key}
            onItemClick={handleItemClick}
            onTopLevelHoverStart={showCollapsedFlyout}
            onTopLevelHoverEnd={queueCollapsedFlyoutHide}
            isSectionTitle={isSectionTitle}
            hasActiveDescendant={(item: SidebarItem) => hasActiveDescendant(item, currentItemKey)}
          />
          <div className="clearfix" />
        </div>
        <div className="clearfix" />
      </div>
      {collapsed && hoveredCollapsedItem ? (
        <div
          className="sidebarPrimary__collapsedProjection"
          style={{
            top: `${hoveredCollapsedItemTop - hoveredCollapsedItemHeight}px`,
            height: `${hoveredCollapsedItemHeight}px`,
            width: `${hoveredCollapsedFlyoutWidth}px`
          }}
          aria-hidden="true"
          onMouseEnter={clearCollapsedFlyoutHideTimer}
          onMouseLeave={queueCollapsedFlyoutHide}
        >
          <span>{hoveredCollapsedItem.label}</span>
        </div>
      ) : null}
      {collapsed && hoveredCollapsedItem?.children?.length ? (
        <SidebarCollapsedFlyout
          item={hoveredCollapsedItem}
          currentItemKey={currentItemKey}
          top={hoveredCollapsedItemTop}
          openKeyList={openKeyList}
          bodyRef={collapsedFlyoutRef}
          onItemClick={handleItemClick}
          onMouseEnter={clearCollapsedFlyoutHideTimer}
          onMouseLeave={queueCollapsedFlyoutHide}
        />
      ) : null}
      <SidebarScrollbar visible={scrollbarVisible} thumbHeight={thumbHeight} thumbTop={thumbTop} />
    </aside>
  );
}
