import type { ReactNode } from 'react';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { joinClassNames } from '@/utils/joinClassNames';

export function PageTitle(): JSX.Element {
  return (
    <div className="componentsNestablePage__row">
      <div className="componentsNestablePage__col componentsNestablePage__col--full">
        <h4 className="page-title componentsNestablePage__pageTitle">Listas aninháveis</h4>
        <PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Listas aninháveis' }]} />
      </div>
    </div>
  );
}

export function Row({ children }: { children: ReactNode }): JSX.Element {
  return <div className="componentsNestablePage__row">{children}</div>;
}

export function Col({ children, half = false }: { children: ReactNode; half?: boolean }): JSX.Element {
  return <div className={joinClassNames('componentsNestablePage__col', half ? 'componentsNestablePage__col--half' : 'componentsNestablePage__col--full')}>{children}</div>;
}

export function SectionTitle({ children }: { children: ReactNode }): JSX.Element {
  return <h4 className="componentsNestablePage__sectionTitle m-t-0"><b>{children}</b></h4>;
}

export function Description(): JSX.Element {
  return <p className="componentsNestablePage__description text-muted m-b-30 font-13">Lista hierárquica com arrastar e soltar, compatível com mouse e toque (plugin jQuery).</p>;
}
