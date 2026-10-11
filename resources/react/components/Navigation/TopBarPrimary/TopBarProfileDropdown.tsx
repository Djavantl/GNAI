import { Link } from '@inertiajs/react';
import { forwardRef } from 'react';
import type { ProfileLink } from './TopBarPrimary.data';
import { profileLinks } from './TopBarPrimary.data';
import { joinClassNames } from '@/utils/joinClassNames';

interface TopBarProfileDropdownProps {
  isOpen: boolean;
  links?: ProfileLink[];
  user?: { name: string; photo_url: string };
  onToggle: (event: React.MouseEvent<HTMLAnchorElement>) => void;
  preventNavigation: (event: React.MouseEvent<HTMLAnchorElement>) => void;
}

const TopBarProfileDropdown = forwardRef<HTMLLIElement, TopBarProfileDropdownProps>(function TopBarProfileDropdown(
  {
    isOpen,
    onToggle,
    preventNavigation,
    links = profileLinks,
    user
  }: TopBarProfileDropdownProps,
  ref
): React.JSX.Element {
  return (
    <li ref={ref} className={joinClassNames('topbar__navItem', 'topbar__navItem--dropdown', 'dropdown', isOpen && 'is-open')}>
      <a
        href="/"
        className="topbar__navLink topbar__profile dropdown-toggle profile waves-effect"
        onClick={onToggle}
        aria-label={user ? `Menu do usuário: ${user.name}` : "Menu do usuário"}
        aria-expanded={isOpen}
        aria-haspopup="menu"
      >
        <img src={user?.photo_url ?? "/assets/react/images/users/avatar-1.jpg"} alt={user ? `Foto de ${user.name}` : "Usuário"} className="topbar__avatar img-circle" />
      </a>

      <ul className="topbarDropdown topbarDropdown--profile dropdown-menu" role="menu">
        {links.map((link: ProfileLink) => (
          <li key={link.label}>
            {link.href ? <Link href={link.href} method={link.method ?? 'get'} as={link.method === 'post' ? 'button' : 'a'}
              role="menuitem" className="topbarDropdown__profileLink">
              <i className={`${link.iconClass} topbarDropdown__profileIcon m-r-5`} aria-hidden="true" />
              {link.label}
            </Link> : <a href="/" onClick={preventNavigation} className="topbarDropdown__profileLink">
              <i className={`${link.iconClass} topbarDropdown__profileIcon m-r-5`} aria-hidden="true" />
              {link.label}
            </a>}
          </li>
        ))}
      </ul>
    </li>
  );
});

export default TopBarProfileDropdown;
