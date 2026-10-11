import type { ReactNode } from 'react';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { joinClassNames } from '@/utils/joinClassNames';

export function PageTitle(): React.JSX.Element {
  return (
    <div className="componentsNestablePage__row">
      <div className="componentsNestablePage__col componentsNestablePage__col--full">
        <h4 className="page-title componentsNestablePage__pageTitle">Listas aninháveis</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Listas aninháveis' }]} />
      </div>
    </div>
  );
}

export function Row({ children }: { children: ReactNode }): React.JSX.Element {
  return <div className="componentsNestablePage__row">{children}</div>;
}

export function Col({ children, half = false }: { children: ReactNode; half?: boolean }): React.JSX.Element {
  return <div className={joinClassNames('componentsNestablePage__col', half ? 'componentsNestablePage__col--half' : 'componentsNestablePage__col--full')}>{children}</div>;
}

export function SectionTitle({ children }: { children: ReactNode }): React.JSX.Element {
  return <h4 className="componentsNestablePage__sectionTitle m-t-0"><b>{children}</b></h4>;
}

export function Description(): React.JSX.Element {
  return <p className="componentsNestablePage__description text-muted m-b-30 font-13">Lista hierárquica com arrastar e soltar, compatível com mouse e toque (plugin jQuery).</p>;
}
