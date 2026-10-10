import { useEffect, useRef, useState } from 'react';
import './TopBarPrimary.css';
import { initWaves } from '@/utils/initWaves';
import RightSidebar from '@/Components/Navigation/RightSidebar/RightSidebar';
import TopBarNotificationsDropdown from './TopBarNotificationsDropdown';
import TopBarProfileDropdown from './TopBarProfileDropdown';
import { useFullscreenToggle } from './useFullscreenToggle';

interface TopBarPrimaryProps {
  sidebarCollapsed: boolean;
  onToggleSidebar: () => void;
  smallMenu?: boolean;
}

export default function TopBarPrimary({ sidebarCollapsed, onToggleSidebar, smallMenu = false }: TopBarPrimaryProps): JSX.Element {
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

    document.addEventListener('mousedown', handleDocumentClick);

    return () => {
      document.removeEventListener('mousedown', handleDocumentClick);
    };
  }, []);

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
            <a href="/" className="topbar__logo" aria-label="Bold home">
              <i className="icon-magnet topbar__logoIcon" aria-hidden="true" />
              <span>
                Ub<i className="md md-album" aria-hidden="true" />
                ld
              </span>
            </a>
          </div>
        </div>

        <div className="topbar__navBar" role="navigation" aria-label="Topbar">
          <div className="topbar__container">
            <div className="topbar__leftActions pull-left">
              <button
                type="button"
                className="topbar__menuButton"
                aria-label="Abrir menu lateral"
                onClick={onToggleSidebar}
              >
                <i className="ion-navicon" aria-hidden="true" />
              </button>
              <span className="clearfix" />
            </div>

            <form className="topbar__search navbar-left pull-left hidden-xs" role="search">
              <input type="text" placeholder="Pesquisar..." aria-label="Pesquisar" />
              <a href="/" onClick={preventNavigation} aria-hidden="true">
                <i className="fa fa-search" />
              </a>
            </form>

            <ul className="topbar__navList nav navbar-nav navbar-right pull-right">
              <TopBarNotificationsDropdown
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
              <a
                href="/"
                className="topbar__navLink waves-effect"
                onClick={toggleRightSidebar}
                aria-label="Abrir painel de configurações"
              >
                <i className="icon-settings" aria-hidden="true" />
              </a>
              </li>

              <TopBarProfileDropdown
                ref={profileRef}
                isOpen={profileOpen}
                onToggle={toggleProfile}
                preventNavigation={preventNavigation}
              />
            </ul>
          </div>
        </div>
      </header>

      <RightSidebar isOpen={rightSidebarOpen} />
    </>
  );
}
