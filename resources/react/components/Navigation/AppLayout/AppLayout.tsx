import type { ReactNode } from 'react';
import { usePage } from '@inertiajs/react';
import TopBarPrimary from '@/components/Navigation/TopBarPrimary/TopBarPrimary';
import SidebarPrimary from '@/components/Navigation/SidebarPrimary/SidebarPrimary';
import AppFooter from '@/components/Navigation/AppFooter/AppFooter';
import PageScrollbar from '@/components/UI/PageScrollbar/PageScrollbar';
import InstitutionBrand from '@/components/Navigation/InstitutionBrand/InstitutionBrand';
import ImpersonationBanner from '@/components/Navigation/ImpersonationBanner/ImpersonationBanner';
import type { ProfileLink } from '@/components/Navigation/TopBarPrimary/TopBarPrimary.data';
import type { AppLayoutProps } from './AppLayout.types';
import { useAppSidebar } from './useAppSidebar';
import '@/styles/app-shell.css';
import './AppLayout.css';

export default function AppLayout({ children }: { children: ReactNode }): React.JSX.Element {
  const { props, url } = usePage<AppLayoutProps>();
  const sidebar = useAppSidebar(url);
  const { auth, navigation, topbar, impersonating } = props;
  if (!auth.user) return <>{children}</>;

  const profileItems: ProfileLink[] = [
    { label: 'Perfil', iconClass: 'ti-user', href: '/auth/perfil' },
    ...(impersonating ? [{ label: 'Voltar para Admin', iconClass: 'ti-back-left', href: '/auth/impersonate/leave', method: 'post' as const }] : []),
    { label: 'Sair', iconClass: 'ti-power-off', href: '/auth/logout', method: 'post' },
  ];

  return <div className={`app-page gnaiApp${sidebar.collapsed ? ' app-page--sidebarCollapsed' : ''}`}>
    <TopBarPrimary brand="Gnai" compactLogoSrc="/images/logo2.png" homeHref="/auth/dashboard" sidebarCollapsed={sidebar.collapsed}
      onToggleSidebar={sidebar.toggle} user={auth.user} profileItems={profileItems}
      notificationItems={topbar?.notifications ?? []} unreadCount={topbar?.unreadCount ?? 0}
      notificationsHref="/notifications" settingsHref="/auth/perfil"
      leadingContent={<InstitutionBrand name={topbar?.institutionName ?? 'Gnai'}
        href={navigation.entries.some(item => item.key === 'institutions') ? '/inclusive-radar/institutions' : undefined} />} />
    <SidebarPrimary collapsed={sidebar.collapsed} entries={navigation.entries}
      currentItemKey={navigation.currentItemKey ?? undefined} />
    {!sidebar.collapsed ? <button className="gnaiApp__overlay" type="button" onClick={sidebar.closeOnMobile} aria-label="Fechar menu lateral" /> : null}
    <div className="app-content-placeholder">
      <div className="app-content-placeholder__inner">
        {impersonating ? <ImpersonationBanner name={auth.user.name} /> : null}
        {children}
      </div>
      <AppFooter>Gnai &copy; {new Date().getFullYear()} | Gestão NAIs</AppFooter>
    </div>
    <PageScrollbar />
  </div>;
}
