import './CheckboxRadio.css';
import ChoiceControl, { type ChoiceTone } from '@/Components/Forms/ChoiceControl/ChoiceControl';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';

const tones: ChoiceTone[] = ['default', 'custom', 'primary', 'success', 'info', 'warning', 'danger', 'purple', 'pink', 'inverse'];
const labels: Record<ChoiceTone, string> = { default: 'Padrão', custom: 'Personalizado', primary: 'Primário', success: 'Sucesso', info: 'Informação', warning: 'Aviso', danger: 'Perigo', purple: 'Roxo', pink: 'Rosa', inverse: 'Inverso' };

export function ChoiceList({ type, circle = false, disabled = false }: { type: 'checkbox' | 'radio'; circle?: boolean; disabled?: boolean }): JSX.Element {
  const disabledItems: Array<{ tone: ChoiceTone; label: string; circle?: boolean }> = [
    { tone: 'default', label: 'Não é possível marcar' },
    { tone: 'custom', label: 'Este também' },
    { tone: 'warning', label: 'E este', circle: true },
    { tone: 'info', label: 'Não é possível marcar' },
    { tone: 'pink', label: 'Este também' },
    { tone: 'purple', label: 'E este', circle: true }
  ];
  const circleTones: ChoiceTone[] = ['default', 'custom', 'info', 'primary', 'success', 'warning', 'danger', 'purple', 'pink', 'inverse'];
  const list: Array<{ tone: ChoiceTone; label: string; circle?: boolean }> = disabled ? disabledItems : (circle ? circleTones : tones).map((tone) => ({ tone, label: labels[tone] }));
  const checkedIndexes = circle ? [1, 2, 4, 6, 7, 9] : [1, 2, 5, 6, 8];

  return <div className="checkboxRadioPage__choiceList">{list.map(({ tone, label, circle: itemCircle }, index) => <ChoiceControl key={`${tone}-${index}`} type={type} id={`${type}-${circle || itemCircle ? 'circle-' : ''}${index}`} name={type === 'radio' ? 'basic-radio' : undefined} tone={tone} label={label} checked={disabled ? index !== 0 : checkedIndexes.includes(index)} circle={circle || itemCircle} disabled={disabled} />)}</div>;
}

export function BasicRadioChoices(): JSX.Element {
  const radioTones: ChoiceTone[] = ['default', 'custom', 'primary', 'success', 'info', 'danger', 'warning', 'purple', 'pink'];

  return <div className="checkboxRadioPage__radioColumns">
    <div>{radioTones.map((tone, index) => <ChoiceControl key={tone} type="radio" id={`radio-basic-a-${index}`} name="radio-basic-a" tone={tone} label={labels[tone]} checked={index === 0} />)}</div>
    <div>{radioTones.map((tone, index) => <ChoiceControl key={tone} type="radio" id={`radio-basic-b-${index}`} name={`radio-basic-b-${index}`} tone={tone} label={labels[tone]} checked={[0, 3, 4, 6, 8].includes(index)} />)}</div>
  </div>;
}

export function Section({ title, description, children }: { title: string; description: React.ReactNode; children: React.ReactNode }): JSX.Element {
  return <div className="checkboxRadioPage__section"><h4 className="header-title m-t-0"><b>{title}</b></h4><p className="checkboxRadioPage__description text-muted font-13">{description}</p>{children}</div>;
}

export function CheckboxRadioShowcase(): JSX.Element {
  return <section className="checkboxRadioPage" aria-label="Checkboxes e rádios">
    <div className="checkboxRadioPage__pageTitleRow"><h4 className="page-title checkboxRadioPage__pageTitle">Checkboxes e rádios</h4><PageBreadcrumb items={[{ label: 'Ubold', href: '#' }, { label: 'UI Kit', href: '#' }, { label: 'Checkboxes e rádios' }]} /></div>
    <div className="checkboxRadioPage__card">
      <div className="checkboxRadioPage__grid">
        <Section title="Básico" description={<>Suporta as cores da marca: <code>.checkbox-primary</code>, <code>.checkbox-info</code> etc.</>}><ChoiceList type="checkbox" /><p className="checkboxRadioPage__hint">Checkboxes sem texto <code>.checkbox-single</code></p><ChoiceControl type="checkbox" id="single-checkbox" single /><ChoiceControl type="checkbox" id="single-checkbox-checked" tone="primary" checked single /><p className="checkboxRadioPage__hint">Checkboxes em linha</p><div className="checkboxRadioPage__inlineGroup"><ChoiceControl type="checkbox" id="inline-checkbox-1" label="Em linha 1" inline /><ChoiceControl type="checkbox" id="inline-checkbox-2" label="Em linha 2" tone="success" checked inline /><ChoiceControl type="checkbox" id="inline-checkbox-3" label="Em linha 3" tone="pink" inline /></div></Section>
        <Section title="Circulares" description={<><code>.checkbox-circle</code> para deixá-los redondos.</>}><ChoiceList type="checkbox" circle /></Section>
        <Section title="Desabilitados" description="O estado desabilitado também é suportado."><ChoiceList type="checkbox" disabled /></Section>
      </div>
    </div>
    <div className="checkboxRadioPage__card">
      <div className="checkboxRadioPage__grid checkboxRadioPage__grid--radios">
        <Section title="Básico" description={<>Suporta as cores da marca: <code>.radio-primary</code>, <code>.radio-danger</code> etc.</>}><BasicRadioChoices /><p className="checkboxRadioPage__hint">Rádios sem texto <code>.radio-single</code></p><ChoiceControl type="radio" id="single-radio-1" name="single-radio" single /><ChoiceControl type="radio" id="single-radio-2" name="single-radio" tone="success" checked single /><p className="checkboxRadioPage__hint">Rádios em linha</p><div className="checkboxRadioPage__inlineGroup"><ChoiceControl type="radio" id="inline-radio-1" name="inline-radio" label="Em linha 1" tone="info" checked inline /><ChoiceControl type="radio" id="inline-radio-2" name="inline-radio" label="Em linha 2" inline /></div></Section>
        <Section title="Desabilitados" description="O estado desabilitado também é suportado."><ChoiceControl type="radio" id="disabled-radio-1" name="disabled-radio-1" label="Próximo" tone="danger" checked disabled /><ChoiceControl type="radio" id="disabled-radio-2" name="disabled-radio-2" label="Um" checked disabled /><ChoiceControl type="radio" id="disabled-radio-3" name="disabled-radio-3" label="Próximo" tone="custom" checked disabled /><ChoiceControl type="radio" id="disabled-radio-4" name="disabled-radio-4" label="Um" tone="pink" checked disabled /></Section>
      </div>
    </div>
  </section>;
}
