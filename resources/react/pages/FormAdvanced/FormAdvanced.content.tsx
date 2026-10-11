import type { MaxLengthInputConfig } from './FormAdvanced.types';

export const maxLengthLeftInputs: MaxLengthInputConfig[] = [
  {
    id: 'defaultconfig',
    label: 'Uso padrão',
    description: 'O badge aparecerá por padrão quando os caracteres restantes forem 10 ou menos:'
  },
  {
    id: 'thresholdconfig',
    label: 'Valor limite',
    description: <>Quer que o badge apareça quando existirem 20 caracteres ou menos? Use a opção <code>threshold</code>:</>
  },
  {
    id: 'moreoptions',
    label: 'Algumas opções',
    description: ' '
  }
];

export const maxLengthRightInputs: MaxLengthInputConfig[] = [
  {
    id: 'alloptions',
    label: 'Todas as opções',
    description: <>Observe: se a opção <code>alwaysShow</code> estiver habilitada, a opção <code>threshold</code> será ignorada.</>
  },
  {
    id: 'textarea',
    label: 'Textareas',
    description: 'Bootstrap maxlength suporta textarea e inputs. Até no IE antigo.',
    multiline: true,
    placeholder: 'Este textarea tem limite de 225 caracteres.'
  },
  {
    id: 'placement',
    label: 'Posição',
    description: <>Basta especificar a opção <code>placement</code> com uma das strings aceitas. Se nada for informado, a posição padrão será <code>bottom</code>.</>
  }
];
