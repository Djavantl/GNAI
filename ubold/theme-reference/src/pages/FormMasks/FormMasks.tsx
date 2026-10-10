import FormField from '@/Components/Forms/FormField/FormField';
import FormPage from '@/Components/Forms/FormPage/FormPage';
import FormSection, { FormDemoDescription } from '@/Components/Forms/FormSection/FormSection';
import MaskedInput from '@/Components/Forms/MaskedInput/MaskedInput';
import NumericInput from '@/Components/Forms/NumericInput/NumericInput';
import { maskColumns, numericColumns } from './FormMasks.data';
import './FormMasks.css';

export default function FormMasks(): JSX.Element {
  return <FormPage title="Máscaras de formulário">
    <FormSection title="Máscaras de entrada" description={<FormDemoDescription />}>
      <div className="formMasks__columns">{maskColumns.map((column, index) => <div key={index} className="formMasks__column"><form className="formMasks__examples" onSubmit={(event) => event.preventDefault()}>
        {column.map((field) => <FormField key={field.id} {...field}><MaskedInput id={field.id} name={field.id} mask={field.mask} aria-describedby={`${field.id}-hint`} /></FormField>)}
      </form></div>)}</div>
    </FormSection>
    <FormSection title="Formatação numérica automática" description={<FormDemoDescription />}>
      <div className="formMasks__columns">{numericColumns.map((column, index) => <div key={index} className="formMasks__column"><form className="formMasks__examples" onSubmit={(event) => event.preventDefault()}>
        {column.map((field) => <FormField key={field.id} {...field}><NumericInput id={field.id} name={field.id} format={field.format} aria-describedby={`${field.id}-hint`} /></FormField>)}
      </form></div>)}</div>
    </FormSection>
  </FormPage>;
}
