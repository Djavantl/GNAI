import CardBox from '@/Components/UI/CardBox/CardBox';
import FormPage from '@/Components/Forms/FormPage/FormPage';
import TinyMceEditor from '@/Components/Forms/TinyMceEditor/TinyMceEditor';
import { wysiwigStyleFormats } from './WysiwigPage.data';

export default function WysiwigPage(): JSX.Element {
  return <FormPage title="Editor WYSIWYG">
    <CardBox><TinyMceEditor label="Editor WYSIWYG" styleFormats={wysiwigStyleFormats} /></CardBox>
  </FormPage>;
}
