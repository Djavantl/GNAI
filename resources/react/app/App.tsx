import { Suspense, useEffect } from 'react';
import TopBarPrimary from '@/components/Navigation/TopBarPrimary/TopBarPrimary';
import SidebarPrimary from '@/components/Navigation/SidebarPrimary/SidebarPrimary';
import AppFooter from '@/components/Navigation/AppFooter/AppFooter';
import PageScrollbar from '@/components/UI/PageScrollbar/PageScrollbar';
import { sidebarUser } from '@/components/Navigation/SidebarUser/SidebarUser.data';
import { usePathname } from '@/hooks/usePathname';
import { useLayoutSidebar } from '@/pages/Layouts/useLayoutSidebar';
import { resolveRoute } from './routes';
import '@/styles/app-shell.css';

export default function App(): React.JSX.Element {
  const pathname = usePathname();
  const filename = pathname.split('/').pop() || 'index.html';
  const route = resolveRoute(filename);
  const sidebar = useLayoutSidebar(route?.layout);
  const smallMenu = route?.layout?.sidebar === 'small';

  useEffect(() => {
    window.scrollTo(0, 0);
    document.title = 'Componentes React';
    if (window.matchMedia('(max-width: 767px)').matches && !sidebar.collapsed) {
      sidebar.toggle();
    }
  }, [pathname]);

  const content = (
    <Suspense fallback={<p role="status">Carregando…</p>}>
      {route?.content ?? <section><h1>Página não encontrada</h1><a href="index.html">Voltar ao início</a></section>}
    </Suspense>
  );

  if (route?.standalone) return <>{content}<PageScrollbar belowTopBar={false} /></>;

  return (
    <div className={`app-page${sidebar.collapsed ? ' app-page--sidebarCollapsed' : ''}${smallMenu ? ' app-page--menuSmall' : ''}${route?.layout ? ' app-page--layout' : ''}`}>
      <TopBarPrimary sidebarCollapsed={sidebar.collapsed} onToggleSidebar={sidebar.toggle} smallMenu={smallMenu} />
      <SidebarPrimary collapsed={sidebar.collapsed} currentItemKey={route?.key} smallMenu={smallMenu}
        user={route?.layout?.sidebar === 'user' ? sidebarUser : undefined} />
      <main className="app-content-placeholder">
        <div className="app-content-placeholder__inner" key={pathname}>{content}</div>
        <AppFooter />
      </main>
      <PageScrollbar />
    </div>
  );
}
