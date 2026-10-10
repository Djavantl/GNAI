import FormPage from '@/Components/Forms/FormPage/FormPage';
import FormSection, { FormDemoDescription } from '@/Components/Forms/FormSection/FormSection';
import FormWizardExample from './FormWizardExample';
import { wizardExamples } from './FormWizardPage.data';

export default function FormWizardPage(): JSX.Element {
  return <FormPage title="Assistente de formulário">
    {wizardExamples.map((example) => <FormSection key={example.id} title={example.title} description={<FormDemoDescription />}><FormWizardExample variant={example.id} /></FormSection>)}
  </FormPage>;
}
