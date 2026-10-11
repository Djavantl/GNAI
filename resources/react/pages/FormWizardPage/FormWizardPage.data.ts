import type { WizardExample, WizardFieldConfig, WizardValues } from './FormWizardPage.types';

export const wizardExamples: Array<{ id: WizardExample; title: string }> = [
  { id: 'basic', title: 'Assistente de formulário básico' },
  { id: 'vertical', title: 'Exemplo de etapas verticais' },
  { id: 'validation', title: 'Assistente com validação' }
];
export const accountFields: WizardFieldConfig[] = [
  { name: 'userName', label: 'Usuário', required: true },
  { name: 'password', label: 'Senha', required: true },
  { name: 'confirm', label: 'Confirmar senha', required: true }
];
export const profileFields: WizardFieldConfig[] = [
  { name: 'name', label: 'Nome', required: true },
  { name: 'surname', label: 'Sobrenome', required: true },
  { name: 'email', label: 'E-mail', required: true, email: true },
  { name: 'address', label: 'Endereço' }
];
export const wizardHints = [
  { label: 'Nome:', value: 'Jonathan' },
  { label: 'Sobrenome:', value: 'Smith' },
  { label: 'E-mail:', value: 'jonathan@smith.com' },
  { label: 'Endereço:', value: '123 Sua cidade, Nome da cidade.' }
];
export const initialWizardValues: WizardValues = { userName: '', password: '', confirm: '', name: '', surname: '', email: '', address: '' };
export const wizardStepLabels = ['Conta', 'Perfil', 'Dicas', 'Concluir'];
export const validatedStepLabels = ['Etapa 1', 'Etapa 2', 'Etapa 3', 'Etapa final'];
