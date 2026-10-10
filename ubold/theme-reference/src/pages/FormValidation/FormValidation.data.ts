import type { BasicFieldConfig, HorizontalFieldConfig, ValidationColumnConfig } from './FormValidation.types';

export const basicFormFields: BasicFieldConfig[] = [
  {
    id: 'userName',
    label: 'Nome de usuário*',
    name: 'nick',
    placeholder: 'Digite o nome de usuário',
    type: 'text',
    required: true,
    parsleyTrigger: 'change'
  },
  {
    id: 'emailAddress',
    label: 'Endereço de e-mail*',
    name: 'email',
    placeholder: 'Digite o e-mail',
    type: 'email',
    required: true,
    parsleyTrigger: 'change'
  },
  {
    id: 'pass1',
    label: 'Senha*',
    placeholder: 'Senha',
    type: 'password',
    required: true
  },
  {
    id: 'passWord2',
    label: 'Confirmar senha *',
    placeholder: 'Senha',
    type: 'password',
    required: true,
    parsleyEqualTo: '#pass1'
  }
];

export const horizontalFormFields: HorizontalFieldConfig[] = [
  {
    id: 'inputEmail3',
    label: 'E-mail*',
    placeholder: 'E-mail',
    type: 'email',
    required: true,
    parsleyType: 'email'
  },
  {
    id: 'hori-pass1',
    label: 'Senha*',
    placeholder: 'Senha',
    type: 'password',
    required: true
  },
  {
    id: 'hori-pass2',
    label: 'Confirmar senha *',
    placeholder: 'Senha',
    type: 'password',
    required: true,
    parsleyEqualTo: '#hori-pass1'
  },
  {
    id: 'webSite',
    label: 'Site*',
    placeholder: 'URL',
    type: 'url',
    required: true,
    parsleyType: 'url'
  }
];

export const validationColumns: ValidationColumnConfig[] = [
  {
    title: 'Tipo de validação',
    description: 'Seu texto incrível vem aqui.',
    fields: [
      { label: 'Obrigatório', placeholder: 'Digite algo', required: true },
      { label: 'Igual a', type: 'password', id: 'pass2', placeholder: 'Senha', required: true },
      { label: 'E-mail', type: 'email', placeholder: 'Digite um e-mail válido', required: true, parsleyType: 'email' },
      { label: 'URL', type: 'url', placeholder: 'URL', required: true, parsleyType: 'url' },
      { label: 'Dígitos', placeholder: 'Digite apenas dígitos', required: true, parsleyType: 'digits' },
      { label: 'Número', placeholder: 'Digite apenas números', required: true, parsleyType: 'number' },
      { label: 'Alfanumérico', placeholder: 'Digite um valor alfanumérico', required: true, parsleyType: 'alphanum' },
      { label: 'Área de texto', required: true, multiline: true }
    ]
  },
  {
    title: 'Validação de intervalo',
    description: 'Seu texto incrível vem aqui.',
    fields: [
      { label: 'Tamanho mínimo', placeholder: 'Mín. 6 caracteres.', required: true, parsleyMinLength: 6 },
      { label: 'Tamanho máximo', placeholder: 'Máx. 6 caracteres.', required: true, parsleyMaxLength: 6 },
      { label: 'Intervalo de tamanho', placeholder: 'Texto entre 5 e 10 caracteres', required: true, parsleyLength: '[5,10]' },
      { label: 'Valor mínimo', placeholder: 'Valor mínimo é 6', required: true, parsleyMin: 6 },
      { label: 'Valor máximo', placeholder: 'Valor máximo é 6', required: true, parsleyMax: 6 },
      { label: 'Intervalo de valor', placeholder: 'Número entre 6 e 100', required: true, type: 'text range' },
      { label: 'Expressão regular', placeholder: 'Cor hexadecimal', required: true, parsleyPattern: '#[A-Fa-f0-9]{6}' }
    ],
    minCheckOptions: [
      { id: 'checkbox1', label: ' E este ', parsleyMultiple: 'groups', parsleyMinCheck: 2 },
      { id: 'checkbox2', label: ' Não pode marcar este ', parsleyMultiple: 'groups', parsleyMinCheck: 2 },
      { id: 'checkbox3', label: ' Este também ', parsleyMultiple: 'groups', parsleyMinCheck: 2, required: true }
    ],
    maxCheckOptions: [
      { id: 'checkbox4', label: ' E este ', parsleyMultiple: 'group1' },
      { id: 'checkbox5', label: ' Não pode marcar este ', parsleyMultiple: 'group1' },
      { id: 'checkbox6', label: ' Este também ', parsleyMultiple: 'group1', parsleyMaxCheck: 1 }
    ]
  }
];
