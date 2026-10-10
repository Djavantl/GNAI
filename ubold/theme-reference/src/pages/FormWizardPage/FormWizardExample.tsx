import { useRef } from 'react';
import ChoiceControl from '@/Components/Forms/ChoiceControl/ChoiceControl';
import FormField from '@/Components/Forms/FormField/FormField';
import FormWizard from '@/Components/UI/FormWizard/FormWizard';
import TextInput from '@/Components/Forms/TextInput/TextInput';
import { accountFields, profileFields, validatedStepLabels, wizardHints, wizardStepLabels } from './FormWizardPage.data';
import type { WizardExample, WizardFieldConfig } from './FormWizardPage.types';
import { useWizardValidation } from './useWizardValidation';
import './FormWizardPage.css';

export default function FormWizardExample({ variant }: { variant: WizardExample }): JSX.Element {
  const validated = variant === 'validation';
  const formRef = useRef<HTMLFormElement>(null);
  const { values, errors, accepted, setAccepted, update, validate } = useWizardValidation(validated);
  const labels = validated ? validatedStepLabels : wizardStepLabels;

  function fields(config: WizardFieldConfig[]): JSX.Element {
    return <>{config.map((field) => {
      const id = `${variant}-${field.name}`;
      const required = field.required && !(validated && field.name === 'userName');
      const asterisk = required || (!validated && field.name === 'address');
      return <FormField key={id} id={id} label={`${field.label}${asterisk ? ' *' : ''}`} layout="horizontal" error={errors[field.name]}>
        <TextInput id={id} name={field.name} value={values[field.name]} invalid={!!errors[field.name]} aria-required={required || undefined} aria-describedby={errors[field.name] ? `${id}-error` : undefined} onChange={(event) => update(field.name, event.target.value)} />
      </FormField>;
    })}</>;
  }
  function validateStep(index?: number): boolean {
    const valid = validate(index);
    if (!valid) requestAnimationFrame(() => formRef.current?.querySelector<HTMLInputElement>('[role="tabpanel"][aria-hidden="false"] [aria-invalid="true"]')?.focus());
    return valid;
  }
  const mandatory = <div className="wizardExample__row"><label className="wizardExample__full">(*) Obrigatório</label></div>;
  const panels = [
    <>{fields(accountFields)}{variant !== 'basic' ? mandatory : null}</>,
    <>{fields(profileFields)}{mandatory}</>,
    <div className="wizardExample__row"><div className="wizardExample__full"><ul>{wizardHints.map((hint) => <li key={hint.label}><b>{hint.label}</b> {hint.value}</li>)}</ul></div></div>,
    <div className="wizardExample__row"><div className="wizardExample__full">{validated
      ? <><input id={`${variant}-acceptTerms`} className="wizardExample__nativeCheckbox" name="acceptTerms" type="checkbox" checked={accepted} aria-invalid={!!errors.acceptTerms || undefined} aria-describedby={errors.acceptTerms ? `${variant}-terms-error` : undefined} onChange={(event) => setAccepted(event.target.checked)} />{' '}<label htmlFor={`${variant}-acceptTerms`}>Concordo com os Termos e Condições.</label>{errors.acceptTerms ? <label id={`${variant}-terms-error`} className="formField__error" htmlFor={`${variant}-acceptTerms`}>{errors.acceptTerms}</label> : null}</>
      : <ChoiceControl type="checkbox" id={`${variant}-acceptTerms`} tone="primary" label="Concordo com os Termos e Condições." checked={accepted} onChange={setAccepted} />}</div></div>
  ];

  return <form ref={formRef} id={`wizard-example-${variant}`} noValidate onSubmit={(event) => event.preventDefault()}>
    <FormWizard id={`wizard-${variant}`} orientation={variant === 'vertical' ? 'vertical' : 'horizontal'} transition={variant === 'vertical' ? 'fade' : 'slideLeft'} steps={panels.map((content, index) => ({ id: String(index), title: labels[index], content }))} onStepChanging={(current) => validateStep(current)} onFinish={() => {
      if (!validateStep()) return false;
      if (validated) window.alert('Enviado!');
      return true;
    }} />
  </form>;
}
