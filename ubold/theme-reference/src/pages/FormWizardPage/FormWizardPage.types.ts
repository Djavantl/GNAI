export type WizardExample = 'basic' | 'vertical' | 'validation';
export type WizardFieldName = 'userName' | 'password' | 'confirm' | 'name' | 'surname' | 'email' | 'address';
export interface WizardFieldConfig { name: WizardFieldName; label: string; required?: boolean; email?: boolean; }
export type WizardValues = Record<WizardFieldName, string>;
export type WizardErrors = Partial<Record<WizardFieldName | 'acceptTerms', string>>;
