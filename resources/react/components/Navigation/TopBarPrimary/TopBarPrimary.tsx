import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import type { NotificationItem, ProfileLink } from './TopBarPrimary.data';
import { useEffect, useRef, useState } from 'react';
import './TopBarPrimary.css';
import { initWaves } from '@/utils/initWaves';
import RightSidebar from '@/components/Navigation/RightSidebar/RightSidebar';
import TopBarNotificationsDropdown from './TopBarNotificationsDropdown';
import TopBarProfileDropdown from './TopBarProfileDropdown';
import { useFullscreenToggle } from './useFullscreenToggle';

interface TopBarPrimaryProps {
  sidebarCollapsed: boolean;
  onToggleSidebar: () => void;
  smallMenu?: boolean;
  brand?: string;
  compactLogoSrc?: string;
  homeHref?: string;
  leadingContent?: ReactNode;
  user?: { name: string; photo_url: string };
  profileItems?: ProfileLink[];
  notificationItems?: NotificationItem[];
  unreadCount?: number;
  notificationsHref?: string;
  settingsHref?: string;

}

export default function TopBarPrimary({ sidebarCollapsed, onToggleSidebar, smallMenu = false, brand = 'Interface', compactLogoSrc, homeHref = 'index.html', leadingContent, user, profileItems, notificationItems, unreadCount, notificationsHref, settingsHref }: TopBarPrimaryProps): React.JSX.Element {
  const { url } = usePage();
  const [notificationsOpen, setNotificationsOpen] = useState<boolean>(false);
  const [profileOpen, setProfileOpen] = useState<boolean>(false);
  const [rightSidebarOpen, setRightSidebarOpen] = useState<boolean>(false);
  const { isFullscreen, toggleFullscreen } = useFullscreenToggle();
  const notificationsRef = useRef<HTMLLIElement | null>(null);
  const profileRef = useRef<HTMLLIElement | null>(null);

  useEffect(() => {
    initWaves();

    function handleDocumentClick(event: MouseEvent): void {
      const target = event.target as Node;

      if (notificationsRef.current && !notificationsRef.current.contains(target)) {
        setNotificationsOpen(false);
      }

      if (profileRef.current && !profileRef.current.contains(target)) {
        setProfileOpen(false);
      }
    }

    function handleEscape(event: KeyboardEvent): void {
      if (event.key === 'Escape') {
        setNotificationsOpen(false);
        setProfileOpen(false);
        setRightSidebarOpen(false);
      }
    }
    document.addEventListener('keydown', handleEscape);
    document.addEventListener('mousedown', handleDocumentClick);

    return () => {
      document.removeEventListener('mousedown', handleDocumentClick);
      document.removeEventListener('keydown', handleEscape);
    };
  }, []);

  useEffect(() => {
    setNotificationsOpen(false);
    setProfileOpen(false);
    setRightSidebarOpen(false);
  }, [url]);

  function toggleNotifications(event: React.MouseEvent<HTMLAnchorElement>): void {
    event.preventDefault();
    setNotificationsOpen((open: boolean) => !open);
    setProfileOpen(false);
  }

  function toggleProfile(event: React.MouseEvent<HTMLAnchorElement>): void {
    event.preventDefault();
    setProfileOpen((open: boolean) => !open);
    setNotificationsOpen(false);
  }

  function toggleRightSidebar(event: React.MouseEvent<HTMLAnchorElement>): void {
    event.preventDefault();
    setRightSidebarOpen((open: boolean) => !open);
  }

  function preventNavigation(event: React.MouseEvent<HTMLAnchorElement>): void {
    event.preventDefault();
  }

  return (
    <>
      <header className={`topbar ${sidebarCollapsed ? 'topbar--collapsed' : ''} ${smallMenu ? 'topbar--small' : ''}`}>
        <div className="topbar__left">
          <div className="topbar__leftInner">
            <Link href={homeHref} className="topbar__logo" aria-label={`Início - ${brand}`}>
              {compactLogoSrc ? <img src={compactLogoSrc} className="topbar__compactLogo" alt="" /> : <i className="icon-magnet topbar__logoIcon" aria-hidden="true" />}
              <span>{brand}</span>
            </Link>
          </div>
        </div>

        <div className="topbar__navBar" role="navigation" aria-label="Topbar">
          <div className="topbar__container">
            <div className="topbar__leftActions pull-left">
              <button
                type="button"
                className="topbar__menuButton"
                aria-label="Abrir menu lateral"
                aria-expanded={!sidebarCollapsed}
                aria-controls="sidebar-menu"
                onClick={onToggleSidebar}
              >
                <i className="ion-navicon" aria-hidden="true" />
              </button>
              <span className="clearfix" />
            </div>

            {leadingContent ?? <form className="topbar__search navbar-left pull-left hidden-xs" role="search">
              <input type="text" placeholder="Pesquisar..." aria-label="Pesquisar" />
              <a href="/" onClick={preventNavigation} aria-hidden="true">
                <i className="fa fa-search" />
              </a>
            </form>}

            <ul className="topbar__navList nav navbar-nav navbar-right pull-right">
              <TopBarNotificationsDropdown
                items={notificationItems}
                unreadCount={unreadCount}
                allHref={notificationsHref}
                ref={notificationsRef}
                isOpen={notificationsOpen}
                onToggle={toggleNotifications}
                preventNavigation={preventNavigation}
              />

              <li className="topbar__navItem hidden-xs">
              <a
                href="/"
                id="btn-fullscreen"
                className={`topbar__navLink waves-effect ${isFullscreen ? 'is-active' : ''}`}
                onClick={(event) => { event.preventDefault(); void toggleFullscreen(); }}
                aria-label="Alternar tela cheia"
              >
                <i className="icon-size-fullscreen" aria-hidden="true" />
              </a>
              </li>

              <li className="topbar__navItem hidden-xs">
              {settingsHref ? <Link href={settingsHref} className="topbar__navLink waves-effect" aria-label="Configurações da conta">
                <i className="icon-settings" aria-hidden="true" />
              </Link> : <a
                href="/"
                className="topbar__navLink waves-effect"
                onClick={toggleRightSidebar}
                aria-label="Abrir painel de configurações"
              >
                <i className="icon-settings" aria-hidden="true" />
              </a>}
              </li>

              <TopBarProfileDropdown
                user={user}
                links={profileItems}
                ref={profileRef}
                isOpen={profileOpen}
                onToggle={toggleProfile}
                preventNavigation={preventNavigation}
              />
            </ul>
          </div>
        </div>
      </header>

      {!settingsHref ? <RightSidebar isOpen={rightSidebarOpen} /> : null}
    </>
  );
}
