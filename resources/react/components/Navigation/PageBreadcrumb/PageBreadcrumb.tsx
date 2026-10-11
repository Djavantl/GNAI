import './PageBreadcrumb.css';

export interface PageBreadcrumbItem {
  label: string;
  href?: string;
}

interface PageBreadcrumbProps {
  items: PageBreadcrumbItem[];
}

export default function PageBreadcrumb({ items }: PageBreadcrumbProps): React.JSX.Element {
  return (
    <ol className="pageBreadcrumb breadcrumb">
      {items.map((item: PageBreadcrumbItem, index: number) => {
        const isActive = index === items.length - 1;

        return (
          <li key={`${item.label}-${index}`} className={isActive ? 'active' : undefined}>
            {isActive || !item.href ? (
              item.label
            ) : (
              <a
                href={item.href}
                onClick={item.href === '#' ? (event) => event.preventDefault() : undefined}
              >
                {item.label}
              </a>
            )}
          </li>
        );
      })}
    </ol>
  );
}
