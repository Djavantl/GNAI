import { forwardRef } from 'react';
import type { ProfileLink } from './TopBarPrimary.data';
import { profileLinks } from './TopBarPrimary.data';
import { joinClassNames } from '@/utils/joinClassNames';

interface TopBarProfileDropdownProps {
  isOpen: boolean;
  onToggle: (event: React.MouseEvent<HTMLAnchorElement>) => void;
  preventNavigation: (event: React.MouseEvent<HTMLAnchorElement>) => void;
}

const TopBarProfileDropdown = forwardRef<HTMLLIElement, TopBarProfileDropdownProps>(function TopBarProfileDropdown(
  {
    isOpen,
    onToggle,
    preventNavigation
  }: TopBarProfileDropdownProps,
  ref
): JSX.Element {
  return (
    <li ref={ref} className={joinClassNames('topbar__navItem', 'topbar__navItem--dropdown', 'dropdown', isOpen && 'is-open')}>
      <a
        href="/"
        className="topbar__navLink topbar__profile dropdown-toggle profile waves-effect"
        onClick={onToggle}
        aria-expanded={isOpen}
        aria-haspopup="menu"
      >
        <img src="/assets/gnai/images/users/avatar-1.jpg" alt="user-img" className="topbar__avatar img-circle" />
      </a>

      <ul className="topbarDropdown topbarDropdown--profile dropdown-menu" role="menu">
        {profileLinks.map((link: ProfileLink) => (
          <li key={link.label}>
            <a href="/" onClick={preventNavigation} className="topbarDropdown__profileLink">
              <i className={`${link.iconClass} topbarDropdown__profileIcon m-r-5`} aria-hidden="true" />
              {link.label}
            </a>
          </li>
        ))}
      </ul>
    </li>
  );
});

export default TopBarProfileDropdown;
