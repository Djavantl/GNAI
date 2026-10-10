import FormPage from '@/Components/Forms/FormPage/FormPage';
import FormSection from '@/Components/Forms/FormSection/FormSection';
import SummernoteEditor from '@/Components/Forms/SummernoteEditor/SummernoteEditor';
import { defaultEditorContent, inlineEditorContent } from './SummernotePage.data';
import './SummernotePage.css';

export default function SummernotePage(): JSX.Element {
  return <FormPage title="Editor Summernote">
    <div className="summernotePage">
      <FormSection title="Editor padrão"><SummernoteEditor defaultValue={defaultEditorContent} label="Editor padrão" /></FormSection>
      <FormSection title="Editor inline"><SummernoteEditor mode="inline" defaultValue={inlineEditorContent} label="Editor inline" /></FormSection>
    </div>
  </FormPage>;
}
