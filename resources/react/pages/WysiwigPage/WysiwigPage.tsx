import CardBox from '@/components/UI/CardBox/CardBox';
import FormPage from '@/components/Forms/FormPage/FormPage';
import TinyMceEditor from '@/components/Forms/TinyMceEditor/TinyMceEditor';
import { wysiwigStyleFormats } from './WysiwigPage.data';

export default function WysiwigPage(): React.JSX.Element {
  return <FormPage title="Editor WYSIWYG">
    <CardBox><TinyMceEditor label="Editor WYSIWYG" styleFormats={wysiwigStyleFormats} /></CardBox>
  </FormPage>;
}
