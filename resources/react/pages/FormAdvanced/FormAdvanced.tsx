import { useRef } from 'react';
import {
  BootstrapSelectSection,
  FileStyleSection,
  FormAdvancedPageTitle,
  MaxLengthSection,
  MultipleSelectAndSelect2Section,
  SwitcherySection,
  TagsInputSection,
  TouchSpinSection
} from './FormAdvanced.sections';
import { useFormAdvancedRuntime } from './useFormAdvancedRuntime';
import './FormAdvanced.css';

export default function FormAdvanced(): React.JSX.Element {
  const rootRef = useRef<HTMLElement | null>(null);
  useFormAdvancedRuntime(rootRef);

  return (
    <section ref={rootRef} className="formAdvancedPage" aria-label="Formulário avançado">
      <FormAdvancedPageTitle />
      <TagsInputSection />
      <SwitcherySection />
      <MultipleSelectAndSelect2Section />
      <BootstrapSelectSection />
      <FileStyleSection />
      <TouchSpinSection />
      <MaxLengthSection />
    </section>
  );
}
