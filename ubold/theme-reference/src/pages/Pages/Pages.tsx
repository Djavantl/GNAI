import AccountPages from '@/pages/AccountPages/AccountPages';
import AccountLayout from '@/Components/Navigation/AccountLayout/AccountLayout';
import ErrorState from '@/Components/UI/ErrorState/ErrorState';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { starterCopy as copy } from './Pages.data';
import type { PageDefinition } from './Pages.types';
import './Pages.css';

export default function Pages({ page }: { page: PageDefinition }): JSX.Element {
  if (page.kind === 'account') return <AccountPages kind={page.account} />;
  if (page.kind === 'error') {
    const error = <ErrorState code={page.code} />;
    return page.standalone ? <AccountLayout>{error}</AccountLayout>
      : <div className="pages__errorContainer"><div className="pages__errorWrapper">{error}</div></div>;
  }
  return <section className="pagesStarter" aria-label={copy.title}>
    <h4>{copy.title}</h4>
    <PageBreadcrumb items={[{ label: copy.brand, href: '#' }, { label: copy.section, href: '#' }, { label: copy.title }]} />
  </section>;
}
