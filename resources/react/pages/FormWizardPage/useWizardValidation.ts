import { useState } from 'react';
import { accountFields, initialWizardValues, profileFields } from './FormWizardPage.data';
import type { WizardErrors, WizardFieldConfig, WizardFieldName, WizardValues } from './FormWizardPage.types';

function fieldError(field: WizardFieldConfig, value: string): string | undefined {
  // Match the legacy demo: required fields and email, without extra password rules.
  if (field.required && value.length === 0) return 'Este campo é obrigatório.';
  if (field.email && value && !/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?)*$/.test(value)) return 'Informe um endereço de e-mail válido.';
  return undefined;
}
const validatedAccount = accountFields.map((field) => field.name === 'userName' ? { ...field, required: false } : field);

export function useWizardValidation(enabled: boolean): {
  values: WizardValues; errors: WizardErrors; accepted: boolean;
  setAccepted: (accepted: boolean) => void;
  update: (field: WizardFieldName, value: string) => void;
  validate: (step?: number) => boolean;
} {
  const [values, setValues] = useState<WizardValues>(initialWizardValues);
  const [errors, setErrors] = useState<WizardErrors>({});
  const [accepted, setAcceptedState] = useState<boolean>(false);

  function update(name: WizardFieldName, value: string): void {
    setValues((current) => ({ ...current, [name]: value }));
    if (enabled && errors[name]) {
      const field = [...validatedAccount, ...profileFields].find((entry) => entry.name === name)!;
      setErrors((current) => ({ ...current, [name]: fieldError(field, value) }));
    }
  }
  function setAccepted(next: boolean): void {
    setAcceptedState(next);
    if (next) setErrors((current) => ({ ...current, acceptTerms: undefined }));
  }
  function validate(step?: number): boolean {
    if (!enabled) return true;
    const fields = step === 0 ? validatedAccount : step === 1 ? profileFields : step === undefined ? [...validatedAccount, ...profileFields] : [];
    const next: WizardErrors = step === undefined ? {} : { ...errors };
    let valid = true;
    for (const field of fields) {
      next[field.name] = fieldError(field, values[field.name]);
      if (next[field.name]) valid = false;
    }
    if (step === 3 || step === undefined) {
      next.acceptTerms = accepted ? undefined : 'Este campo é obrigatório.';
      if (!accepted) valid = false;
    }
    setErrors(next);
    return valid;
  }
  return { values, errors, accepted, setAccepted, update, validate };
}
