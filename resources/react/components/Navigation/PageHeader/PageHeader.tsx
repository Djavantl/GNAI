import './PageHeader.css';
import PageBreadcrumb, { type PageBreadcrumbItem } from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';

interface PageHeaderProps {
  title: string;
  subtitle?: string;
  breadcrumbItems?: PageBreadcrumbItem[];
  variant?: 'banner';
}

export default function PageHeader({ title, subtitle, breadcrumbItems, variant }: PageHeaderProps): React.JSX.Element {
  return (
    <div className={`pageHeader ${variant === 'banner' ? 'pageHeader--banner' : ''} ${breadcrumbItems ? 'pageHeader--breadcrumb' : ''}`}>
      <h4 className="pageHeader__title">{title}</h4>
      {subtitle !== undefined ? <p className="pageHeader__subtitle">{subtitle}</p> : null}
      {breadcrumbItems ? <PageBreadcrumb items={breadcrumbItems} /> : null}
    </div>
  );
}
