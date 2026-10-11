import type { ReactNode } from 'react';
import { Head } from '@inertiajs/react';
import PageBreadcrumb, { type PageBreadcrumbItem } from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import PageTitle from '@/components/Navigation/PageTitle/PageTitle';
import FlashNotifications from '@/components/UI/FlashNotifications/FlashNotifications';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import { studentsBase } from './Students.utils';
import './Students.css';

export default function StudentPage({ title, subtitle, current, actions, banner = false, error, children }: {
  title: string; subtitle?: string; current?: string; actions?: ReactNode; banner?: boolean;
  error?: string | null; children: ReactNode;
}): React.JSX.Element {
  const crumbs: PageBreadcrumbItem[] = [{ label: 'Home', href: '/auth/dashboard' }, { label: 'Alunos', href: current ? studentsBase : undefined }];
  if (current) crumbs.push({ label: current });
  return <main className="studentsPage">
    <Head title={`${title} - Gnai`} />
    <nav aria-label="Localização"><PageBreadcrumb items={crumbs} /></nav>
    <PageTitle title={title} subtitle={subtitle} actions={actions}
      className={banner ? 'studentsPage__header--banner' : undefined} />
    <FlashNotifications />
    {error ? <div role="alert"><InlineAlert variant="danger">{error}</InlineAlert></div> : null}
    {children}
  </main>;
}
