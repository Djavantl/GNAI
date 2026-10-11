import { router } from '@inertiajs/react';
import { forwardRef, useEffect, useRef, useState } from 'react';
import Badge from '@/components/UI/Badge/Badge';
import Label from '@/components/UI/Label/Label';
import type { NotificationItem } from './TopBarPrimary.data';
import { notifications } from './TopBarPrimary.data';
import { joinClassNames } from '@/utils/joinClassNames';

interface TopBarNotificationsDropdownProps {
  isOpen: boolean;
  items?: NotificationItem[];
  unreadCount?: number;
  allHref?: string;
  onToggle: (event: React.MouseEvent<HTMLAnchorElement>) => void;
  preventNavigation: (event: React.MouseEvent<HTMLAnchorElement>) => void;
  className?: string;
}

const TopBarNotificationsDropdown = forwardRef<HTMLLIElement, TopBarNotificationsDropdownProps>(function TopBarNotificationsDropdown(
  {
    isOpen,
    onToggle,
    preventNavigation,
    className = '',
    items = notifications,
    unreadCount = 3,
    allHref
  }: TopBarNotificationsDropdownProps,
  ref
): React.JSX.Element {
  const scrollRef = useRef<HTMLDivElement | null>(null);
  const hideTimerRef = useRef<number | null>(null);
  const [thumbHeight, setThumbHeight] = useState<number>(0);
  const [thumbTop, setThumbTop] = useState<number>(0);
  const [scrollbarVisible, setScrollbarVisible] = useState<boolean>(false);

  useEffect(() => {
    const scrollElement = scrollRef.current;

    if (!scrollElement || !isOpen) {
      setScrollbarVisible(false);
      return;
    }

    function clearHideTimer(): void {
      if (hideTimerRef.current !== null) {
        window.clearTimeout(hideTimerRef.current);
        hideTimerRef.current = null;
      }
    }

    function scheduleHide(): void {
      clearHideTimer();
      hideTimerRef.current = window.setTimeout(() => {
        setScrollbarVisible(false);
      }, 850);
    }

    const updateScrollbar = (): void => {
      const { clientHeight, scrollHeight, scrollTop } = scrollElement;

      if (scrollHeight <= clientHeight) {
        setThumbHeight(0);
        setThumbTop(0);
        return;
      }

      const nextThumbHeight = Math.max((clientHeight / scrollHeight) * clientHeight, 24);
      const maxThumbTop = clientHeight - nextThumbHeight;
      const nextThumbTop = maxThumbTop * (scrollTop / (scrollHeight - clientHeight));

      setThumbHeight(nextThumbHeight);
      setThumbTop(nextThumbTop);
    };

    function handleScroll(): void {
      updateScrollbar();
      setScrollbarVisible(true);
      scheduleHide();
    }

    const handleMouseEnter = (): void => {
      updateScrollbar();
      if (scrollElement.scrollHeight > scrollElement.clientHeight) {
        setScrollbarVisible(true);
      }
      scheduleHide();
    };

    function handleMouseLeave(): void {
      scheduleHide();
    }

    updateScrollbar();

    scrollElement.addEventListener('scroll', handleScroll, { passive: true });
    scrollElement.addEventListener('mouseenter', handleMouseEnter);
    scrollElement.addEventListener('mouseleave', handleMouseLeave);
    window.addEventListener('resize', updateScrollbar);

    return () => {
      clearHideTimer();
      scrollElement.removeEventListener('scroll', handleScroll);
      scrollElement.removeEventListener('mouseenter', handleMouseEnter);
      scrollElement.removeEventListener('mouseleave', handleMouseLeave);
      window.removeEventListener('resize', updateScrollbar);
    };
  }, [isOpen]);

  return (
    <li
      ref={ref}
      className={joinClassNames('topbar__navItem', 'topbar__navItem--dropdown', 'dropdown', 'hidden-xs', isOpen && 'is-open', className)}
    >
      <a
        href="/"
        className="topbar__navLink dropdown-toggle waves-effect"
        onClick={onToggle}
        aria-label={`Notificações: ${unreadCount} não lidas`}
        aria-expanded={isOpen}
        aria-haspopup="menu"
      >
        <i className="icon-bell" aria-hidden="true" />
      </a>

      {unreadCount > 0 ? <Badge className="topbar__badge" variant="danger" size="xs" shape="circle" ariaHidden>
        {unreadCount > 99 ? '99+' : unreadCount}
      </Badge> : null}

      <ul className="topbarDropdown topbarDropdown--large dropdown-menu dropdown-menu-lg" role="menu">
        <li className="topbarDropdown__title">
          <Label className="topbarDropdown__label" variant="default">
            Novas {unreadCount}
          </Label>
          Notificações
        </li>

        <li className="topbarDropdown__notificationsPane">
          <div
            ref={scrollRef}
            className="topbarDropdown__list topbarDropdown__list--notifications list-group notification-list"
          >
            {items.length === 0 ? <p className="topbarDropdown__listItem">Nenhuma notificação nova.</p> : null}
            {items.map((notification: NotificationItem, index: number) => (
              <a
                key={`${notification.title}-${notification.iconClass}-${index}`}
                href={notification.href ?? '/'}
                onClick={notification.href ? (event) => {
                  if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                  event.preventDefault();
                  router.visit(notification.href!);
                } : preventNavigation}
                className="topbarDropdown__listItem list-group-item"
              >
                <div className="topbarDropdown__media">
                  <div className="topbarDropdown__mediaIcon pull-left p-r-10">
                    <em className={notification.iconClass} />
                  </div>
                  <div className="topbarDropdown__mediaBody">
                    <h5>{notification.title}</h5>
                    <p>{notification.description}</p>
                  </div>
                </div>
              </a>
            ))}
          </div>

          <div
            className={joinClassNames(
              'topbarDropdown__scrollbar',
              scrollbarVisible && thumbHeight > 0 && 'topbarDropdown__scrollbar--visible'
            )}
            aria-hidden="true"
          >
            <div
              className="topbarDropdown__scrollbarThumb"
              style={{
                height: `${thumbHeight}px`,
                transform: `translateY(${thumbTop}px)`
              }}
            />
          </div>
        </li>

        <li>
          <a href={allHref ?? '/'} onClick={allHref ? (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            router.visit(allHref);
          } : preventNavigation} className="topbarDropdown__footer list-group-item text-right">
            <small>Ver todas as notificações</small>
          </a>
        </li>
      </ul>
    </li>
  );
});

export default TopBarNotificationsDropdown;
