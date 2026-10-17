import type { ReactNode } from 'react';
import { Head } from '@inertiajs/react';
import PageBreadcrumb, {
  type PageBreadcrumbItem,
} from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import PageTitle from '@/components/Navigation/PageTitle/PageTitle';
import FlashNotifications from '@/components/UI/FlashNotifications/FlashNotifications';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import { joinClassNames } from '@/utils/joinClassNames';
import './BusinessPage.css';

export default function BusinessPage({
  title,
  subtitle,
  breadcrumbItems,
  actions,
  pageTitleClassName,
  className,
  error,
  children,
}: {
  title: string;
  subtitle?: string;
  breadcrumbItems: PageBreadcrumbItem[];
  actions?: ReactNode;
  pageTitleClassName?: string;
  className?: string;
  error?: string | null;
  children: ReactNode;
}): React.JSX.Element {
  return (
    <main className={joinClassNames('businessPage', className)}>
      <Head title={`${title} - Gnai`} />
      <nav aria-label="Localização">
        <PageBreadcrumb items={breadcrumbItems} />
      </nav>
      <PageTitle
        title={title}
        subtitle={subtitle}
        actions={actions}
        className={pageTitleClassName}
      />
      <FlashNotifications />
      {error ? (
        <div role="alert">
          <InlineAlert variant="danger">{error}</InlineAlert>
        </div>
      ) : null}
      {children}
    </main>
  );
}
