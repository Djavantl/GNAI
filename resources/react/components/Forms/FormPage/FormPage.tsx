import type { ReactNode } from 'react';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import './FormPage.css';

interface FormPageProps { title: string; children: ReactNode; }
export default function FormPage({ title, children }: FormPageProps): React.JSX.Element {
  return <section className="formPage" aria-label={title}>
    <h4 className="formPage__title">{title}</h4>
    <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Formulários', href: '#' }, { label: title }]} />
    {children}
  </section>;
}
