import type { DropdownMenuItem, FormFieldConfig } from './FormElements.types';

export const inputTypeLeftFields: FormFieldConfig[] = [
  { label: 'Texto', labelClassName: 'col-md-2', controlClassName: 'col-md-10', type: 'text', defaultValue: 'Algum valor de texto...' },
  { label: 'Email', labelFor: 'example-email', labelClassName: 'col-md-2', controlClassName: 'col-md-10', id: 'example-email', name: 'example-email', type: 'email', placeholder: 'Email' },
  { label: 'Senha', labelClassName: 'col-md-2', controlClassName: 'col-md-10', type: 'password', defaultValue: 'senha' },
  { label: 'Placeholder', labelClassName: 'col-md-2', controlClassName: 'col-md-10', type: 'text', placeholder: 'placeholder' },
  { label: 'Área de texto', labelClassName: 'col-md-2', controlClassName: 'col-md-10', controlType: 'textarea', rows: 5 }
];

export const inputTypeRightFields: FormFieldConfig[] = [
  { label: 'Somente leitura', labelClassName: 'col-md-2', controlClassName: 'col-md-10', type: 'text', defaultValue: 'Valor somente leitura', readOnly: true },
  { label: 'Desabilitado', labelClassName: 'col-md-2', controlClassName: 'col-md-10', type: 'text', defaultValue: 'Valor desabilitado', disabled: true },
  { label: 'Controle estático', labelClassName: 'col-sm-2', controlClassName: 'col-sm-10', controlType: 'static', defaultValue: 'email@exemplo.com' },
  { label: 'Texto de ajuda', labelClassName: 'col-sm-2', controlClassName: 'col-sm-10', type: 'text', placeholder: 'Texto de ajuda', helpText: 'Um bloco de texto de ajuda que quebra para uma nova linha e pode se estender por mais de uma linha.' },
  {
    label: 'Seleção',
    labelClassName: 'col-sm-2',
    controlClassName: 'col-sm-10',
    controlType: 'select',
    options: [{ label: '1' }, { label: '2' }, { label: '3' }, { label: '4' }, { label: '5' }],
    afterControl: 'Seleção múltipla'
  }
];

export const multipleSelectOptions = [{ label: '1' }, { label: '2' }, { label: '3' }, { label: '4' }, { label: '5' }];

export const inputStateFields: FormFieldConfig[] = [
  { label: 'Sucesso', labelFor: 'state-success', labelClassName: 'col-md-3', controlClassName: 'col-md-6', id: 'state-success', name: 'state-success', placeholder: '...', state: 'success' },
  { label: 'Aviso', labelFor: 'state-warning', labelClassName: 'col-md-3', controlClassName: 'col-md-6', id: 'state-warning', name: 'state-warning', placeholder: '...', state: 'warning' },
  { label: 'Perigo', labelFor: 'state-danger', labelClassName: 'col-md-3', controlClassName: 'col-md-6', id: 'state-danger', name: 'state-danger', placeholder: '...', state: 'error' },
  { label: 'Campo com sucesso ', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6', state: 'success', feedbackIcon: 'glyphicon glyphicon-ok' },
  { label: 'Campo com aviso ', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6', state: 'warning', feedbackIcon: 'glyphicon glyphicon-warning-sign' },
  { label: 'Campo com erro ', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6', state: 'error', feedbackIcon: 'glyphicon glyphicon-remove' },
  { label: 'Ícones customizados', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6', placeholder: 'Ícones customizados', feedbackIcon: 'fa fa-envelope l-h-34' }
];

export const inputSizeFields: FormFieldConfig[] = [
  { label: 'Pequeno', labelFor: 'example-input-small', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6 input-sm-wrapper', id: 'example-input-small', name: 'example-input-small', placeholder: '.input-sm' },
  { label: 'Normal', labelFor: 'example-input-normal', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6', id: 'example-input-normal', name: 'example-input-normal', placeholder: 'Normal' },
  { label: 'Grande', labelFor: 'example-input-large', labelClassName: 'col-sm-3', controlClassName: 'col-sm-6 input-lg-wrapper', id: 'example-input-large', name: 'example-input-large', placeholder: '.input-lg' },
  { label: 'Tamanhos da grid', labelClassName: 'col-sm-3', controlClassName: 'col-sm-4', placeholder: '.col-sm-4' },
  { controlClassName: 'col-sm-5 col-sm-offset-3', placeholder: '.col-sm-5' },
  { controlClassName: 'col-sm-6 col-sm-offset-3', placeholder: '.col-sm-6' },
  { controlClassName: 'col-sm-9 col-sm-offset-3', placeholder: '.col-sm-9' }
];

export const dropdownMenuItems: DropdownMenuItem[] = [
  { label: 'Ação' },
  { label: 'Outra ação' },
  { divider: true },
  { label: 'Link separado' }
];
