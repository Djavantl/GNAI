import {
  BasicAndStripedProgressSection,
  EntranceProgressSection,
  LabeledAndStackedProgressSection,
  ProgressBarsPageTitle,
  SizedAndActiveProgressSection,
  VerticalProgressSection
} from './ProgressBars.sections';
import './ProgressBars.css';

export default function ProgressBars(): JSX.Element {
  return (
    <section className="progressBarsPage" aria-label="Barras de progresso">
      <ProgressBarsPageTitle />
      <BasicAndStripedProgressSection />
      <SizedAndActiveProgressSection />
      <LabeledAndStackedProgressSection />
      <EntranceProgressSection />
      <VerticalProgressSection />
    </section>
  );
}
