import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/components/Navigation/PageBreadcrumb/PageBreadcrumb';
import RangeSliderInput from './RangeSliderInput';
import { ionRangeSliders, rangeSliderItems, sliderColorItems, verticalSliderItems } from './ComponentsRangeSliders.data';
import type { BootstrapSliderItem, IonRangeSliderItem } from './ComponentsRangeSliders.types';

export function PageTitle(): React.JSX.Element {
  return (
    <div className="componentsRangeSlidersPage__pageTitleRow">
      <div className="componentsRangeSlidersPage__col componentsRangeSlidersPage__col--full">
        <h4 className="page-title componentsRangeSlidersPage__pageTitle">Sliders de intervalo</h4>
        <PageBreadcrumb items={[{ label: 'Interface', href: '#' }, { label: 'Componentes', href: '#' }, { label: 'Slider de intervalo' }]} />
      </div>
    </div>
  );
}

function SectionTitle({ children }: { children: ReactNode }): React.JSX.Element {
  return (
    <h4 className="componentsRangeSlidersPage__sectionTitle header-title m-t-0">
      <b>{children}</b>
    </h4>
  );
}

function Description({ children }: { children: ReactNode }): React.JSX.Element {
  return <p className="componentsRangeSlidersPage__description text-muted m-b-30 font-13">{children}</p>;
}

function IonSliderRow({ item }: { item: IonRangeSliderItem }): React.JSX.Element {
  const title = item.titleBold ? <b>{item.title}</b> : item.title;

  return (
    <div className="componentsRangeSlidersPage__formGroup form-group">
      <label htmlFor={item.id} className="componentsRangeSlidersPage__label control-label">
        {title}
        <span className="componentsRangeSlidersPage__labelDescription font-normal text-muted f-s-12 clearfix">{item.description}</span>
      </label>
      <div className="componentsRangeSlidersPage__inputCol">
        <input type="text" id={item.id} name={item.id} defaultValue="" />
      </div>
    </div>
  );
}

export function IonRangeSliderSection(): React.JSX.Element {
  return (
    <div className="componentsRangeSlidersPage__row">
      <div className="componentsRangeSlidersPage__col componentsRangeSlidersPage__col--full">
        <CardBox className="componentsRangeSlidersPage__card">
          <SectionTitle>Ion Range Slider</SectionTitle>
          <Description>Slider de intervalo confortável, responsivo e facilmente personalizável.</Description>

          <form className="componentsRangeSlidersPage__form form-horizontal">
            {ionRangeSliders.map((item: IonRangeSliderItem) => <IonSliderRow item={item} key={item.id} />)}
          </form>
        </CardBox>
      </div>
    </div>
  );
}

function BootstrapColumn({ children, title }: { children: ReactNode; title: string }): React.JSX.Element {
  return (
    <div className="componentsRangeSlidersPage__col componentsRangeSlidersPage__col--third">
      <h4 className="componentsRangeSlidersPage__bootstrapColumnTitle">{title}</h4>
      {children}
    </div>
  );
}

function SliderColorList(): React.JSX.Element {
  return (
    <BootstrapColumn title="Cores dos sliders">
      <div className="componentsRangeSlidersPage__sliderColorList">
        {sliderColorItems.map((item: BootstrapSliderItem) => (
          <div className="componentsRangeSlidersPage__sliderColorItem" key={`color-${item.variant}`}>
            <RangeSliderInput item={item} name={`slider-color-${item.variant}`} />
          </div>
        ))}
      </div>
    </BootstrapColumn>
  );
}

function VerticalSliderList(): React.JSX.Element {
  return (
    <BootstrapColumn title="Sliders verticais">
      <div className="componentsRangeSlidersPage__verticalSliders">
        {verticalSliderItems.map((item: BootstrapSliderItem, index: number) => <RangeSliderInput item={item} name={`slider-vertical-${item.variant}-${index}`} key={`vertical-${item.variant}-${index}`} />)}
      </div>
    </BootstrapColumn>
  );
}

function RangeSliderList(): React.JSX.Element {
  return (
    <BootstrapColumn title="Intervalo do slider">
      <div className="componentsRangeSlidersPage__rangeSliders">
        {rangeSliderItems.map((item: BootstrapSliderItem, index: number) => (
          <div className="componentsRangeSlidersPage__rangeSliderRow" key={`range-${item.variant}-${index}`}>
            <RangeSliderInput item={item} name={`slider-range-${item.variant}-${index}`} />
          </div>
        ))}
      </div>
    </BootstrapColumn>
  );
}

export function BootstrapRangeSliderSection(): React.JSX.Element {
  return (
    <div className="componentsRangeSlidersPage__row">
      <div className="componentsRangeSlidersPage__col componentsRangeSlidersPage__col--full">
        <CardBox className="componentsRangeSlidersPage__card componentsRangeSlidersPage__card--last">
          <SectionTitle>Slider de intervalo Bootstrap</SectionTitle>
          <Description>Exemplos do componente bootstrap-slider.</Description>

          <div className="componentsRangeSlidersPage__row componentsRangeSlidersPage__bootstrapRows">
            <SliderColorList />
            <VerticalSliderList />
            <RangeSliderList />
          </div>
        </CardBox>
      </div>
    </div>
  );
}
