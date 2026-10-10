import { useRef } from 'react';
import {
  ClockPickerSection,
  DatePickerSection,
  DateRangePickerSection,
  FormPickersPageTitle,
  TimeAndColorPickersSection
} from './FormPickers.sections';
import { useFormPickersRuntime } from './useFormPickersRuntime';
import './FormPickers.css';

export default function FormPickers(): JSX.Element {
  const rootRef = useRef<HTMLElement | null>(null);
  useFormPickersRuntime(rootRef);

  return (
    <section ref={rootRef} className="formPickersPage" aria-label="Form pickers">
      <FormPickersPageTitle />
      <TimeAndColorPickersSection />
      <DatePickerSection />
      <DateRangePickerSection />
      <ClockPickerSection />
    </section>
  );
}
