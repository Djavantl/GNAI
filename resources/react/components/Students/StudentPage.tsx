import type { ReactNode } from 'react';
import BusinessPage from '@/components/Navigation/BusinessPage/BusinessPage';
import type { PageBreadcrumbItem } from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { studentsBase } from './Students.utils';
import './Students.css';

interface StudentPageProps {
  title: string;
  subtitle?: string;
  current?: string;
  actions?: ReactNode;
  banner?: boolean;
  error?: string | null;
  children: ReactNode;
}

export default function StudentPage({
  title,
  subtitle,
  current,
  actions,
  banner = false,
  error,
  children,
}: StudentPageProps): React.JSX.Element {
  const crumbs: PageBreadcrumbItem[] = [
    { label: 'Home', href: '/auth/dashboard' },
    { label: 'Alunos', href: current ? studentsBase : undefined },
  ];

  if (current) {
    crumbs.push({ label: current });
  }

  return (
    <BusinessPage
      title={title}
      subtitle={subtitle}
      breadcrumbItems={crumbs}
      actions={actions}
      pageTitleClassName={
        banner ? 'studentsPage__header--banner' : undefined
      }
      className="studentsPage"
      error={error}
    >
      {children}
    </BusinessPage>
  );
}
