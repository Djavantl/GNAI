import {
  FormElementsPageTitle,
  FormExamplesCard,
  InlineFormCard,
  InputGroupsCard,
  InputStatesAndSizesCard,
  InputTypesCard
} from './FormElements.sections';
import './FormElements.css';

export default function FormElements(): React.JSX.Element {
  return (
    <section className="formElementsPage" aria-label="Elementos de formulário">
      <FormElementsPageTitle />
      <InputTypesCard />
      <InputStatesAndSizesCard />
      <InputGroupsCard />
      <FormExamplesCard />
      <InlineFormCard />
    </section>
  );
}
