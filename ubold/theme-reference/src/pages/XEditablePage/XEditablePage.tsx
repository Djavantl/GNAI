import CardBox from '@/Components/UI/CardBox/CardBox';
import FormPage from '@/Components/Forms/FormPage/FormPage';
import EditableField from '@/Components/UI/EditableField/EditableField';
import { editableExamples } from './XEditablePage.data';
import './XEditablePage.css';

export default function XEditablePage(): JSX.Element {
  return <FormPage title="X-Editable"><CardBox><div className="xEditablePage__columns">
    {(['inline', 'popup'] as const).map(mode => <section className="xEditablePage__column" key={mode}>
      <h4 className="xEditablePage__title">{mode === 'inline' ? 'Editor inline' : 'Editor em popover'}</h4>
      {editableExamples.map(({ key, label, ...props }) => <div className="xEditablePage__row" key={key}>
        <label >{label}{key === 'comments' ? <> <i>Ctrl+Enter</i></> : null}</label>
        <div className="xEditablePage__value"><EditableField {...props} id={`${mode}-${key}`} mode={mode} defaultValue={mode === 'inline' && key === 'dob' ? '2015-09-24' : props.defaultValue} /></div>
      </div>)}
    </section>)}
  </div></CardBox></FormPage>;
}
