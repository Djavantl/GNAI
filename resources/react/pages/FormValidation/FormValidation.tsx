import { useRef } from 'react';
import { FieldsValidationSection, FormValidationExamplesSection, FormValidationPageTitle } from './FormValidation.sections';
import { useFormValidationRuntime } from './useFormValidationRuntime';
import './FormValidation.css';

export default function FormValidation(): React.JSX.Element {
  const rootRef = useRef<HTMLElement | null>(null);
  useFormValidationRuntime(rootRef);

  return (
    <section ref={rootRef} className="formValidationPage" aria-label="Validação de formulário">
      <FormValidationPageTitle />
      <FormValidationExamplesSection />
      <FieldsValidationSection />
    </section>
  );
}
