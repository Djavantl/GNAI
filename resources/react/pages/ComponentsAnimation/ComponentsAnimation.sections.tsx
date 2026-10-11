import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import AnimationSandbox from './AnimationSandbox';
import { animationGroups } from './ComponentsAnimation.data';

export function PageTitle(): React.JSX.Element {
  return (
    <div className="componentsAnimationPage__pageTitleRow">
      <div className="componentsAnimationPage__col componentsAnimationPage__col--full">
        <h4 className="page-title componentsAnimationPage__pageTitle">Animação</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Animação' }]} />
      </div>
    </div>
  );
}

function SectionTitle({ children }: { children: ReactNode }): React.JSX.Element {
  return (
    <h4 className="componentsAnimationPage__sectionTitle header-title m-t-0">
      <b>{children}</b>
    </h4>
  );
}

function Description({ children }: { children: ReactNode }): React.JSX.Element {
  return <p className="componentsAnimationPage__description text-muted m-b-30 font-13">{children}</p>;
}

export function CssAnimationSection(): React.JSX.Element {
  return (
    <div className="componentsAnimationPage__row componentsAnimationPage__row--content">
      <div className="componentsAnimationPage__col componentsAnimationPage__col--full">
        <CardBox className="componentsAnimationPage__card">
          <SectionTitle>Animação CSS3</SectionTitle>
          <Description>Animações CSS prontas para usar.</Description>
          <AnimationSandbox groups={animationGroups} />
        </CardBox>
      </div>
    </div>
  );
}
