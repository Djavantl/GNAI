import { useMemo, useRef } from 'react';
import { ionRangeSliders } from './ComponentsRangeSliders.data';
import { BootstrapRangeSliderSection, IonRangeSliderSection, PageTitle } from './ComponentsRangeSliders.sections';
import { useRangeSlidersRuntime } from './useRangeSlidersRuntime';
import './ComponentsRangeSliders.css';

export default function ComponentsRangeSliders(): React.JSX.Element {
  const rootRef = useRef<HTMLElement | null>(null);
  const ionSliderItems = useMemo(() => ionRangeSliders, []);

  useRangeSlidersRuntime(rootRef, ionSliderItems);

  return (
    <section className="componentsRangeSlidersPage" ref={rootRef}>
      <div className="componentsRangeSlidersPage__container">
        <PageTitle />
        <IonRangeSliderSection />
        <BootstrapRangeSliderSection />
      </div>
    </section>
  );
}
