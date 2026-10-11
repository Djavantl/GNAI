import './PageHeader.css';
import PageBreadcrumb, { type PageBreadcrumbItem } from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';

interface PageHeaderProps {
  title: string;
  titleAs?: 'h1' | 'h2' | 'h3' | 'h4';
  subtitle?: string;
  breadcrumbItems?: PageBreadcrumbItem[];
  variant?: 'banner';
}

export default function PageHeader({ title, titleAs: Heading = 'h4', subtitle, breadcrumbItems, variant }: PageHeaderProps): React.JSX.Element {
  return (
    <div className={`pageHeader ${variant === 'banner' ? 'pageHeader--banner' : ''} ${breadcrumbItems ? 'pageHeader--breadcrumb' : ''}`}>
      <Heading className="pageHeader__title">{title}</Heading>
      {subtitle !== undefined ? <p className="pageHeader__subtitle">{subtitle}</p> : null}
      {breadcrumbItems ? <PageBreadcrumb items={breadcrumbItems} /> : null}
    </div>
  );
}
