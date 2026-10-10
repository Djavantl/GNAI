import type { ReactNode } from 'react';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import { joinClassNames } from '@/utils/joinClassNames';
import type { ColumnSize, Tone } from './ComponentsWidgets.types';

export function PageTitle(): JSX.Element {
  return (
    <div className="componentsWidgetsPage__row componentsWidgetsPage__titleRow">
      <div className="componentsWidgetsPage__col componentsWidgetsPage__col--full">
        <h4 className="page-title componentsWidgetsPage__pageTitle">Widgets</h4>
        <PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Widgets' }]} />
      </div>
    </div>
  );
}

export function Row({ children }: { children: ReactNode }): JSX.Element {
  return <div className="componentsWidgetsPage__row">{children}</div>;
}

export function Col({ children, size }: { children: ReactNode; size: ColumnSize }): JSX.Element {
  return <div className={joinClassNames('componentsWidgetsPage__col', `componentsWidgetsPage__col--${size}`)}>{children}</div>;
}

export function ToneIcon({ className, tone }: { className: string; tone: Tone | 'dark' | 'white' }): JSX.Element {
  return <i className={joinClassNames(className, `text-${tone}`)} />;
}

export function SectionHeader({ children }: { children: ReactNode }): JSX.Element {
  return (
    <Row>
      <Col size="full">
        <h4 className="page-header header-title componentsWidgetsPage__sectionHeader">{children}</h4>
      </Col>
    </Row>
  );
}

export function CustomScrollRail({ className, height, isVisible, thumbHeight, thumbTop }: { className: string; height: number; isVisible: boolean; thumbHeight: number; thumbTop: number }): JSX.Element {
  return (
    <div className={joinClassNames(className, isVisible && thumbHeight > 0 ? `${className}--visible` : undefined)} style={{ height }} aria-hidden="true">
      <div className={`${className}Thumb`} style={{ height: `${thumbHeight}px`, transform: `translateY(${thumbTop}px)` }} />
    </div>
  );
}
