import type { ButtonTone } from '@/Components/UI/Button/Button';
import type { NotificationToastPosition, NotificationToastTone } from '@/Components/UI/NotificationToast/NotificationToast';

export interface NotificationButtonItem {
  label: string;
  buttonTone: ButtonTone;
  toastTone: NotificationToastTone;
  position: NotificationToastPosition;
  title?: string;
  confirm?: boolean;
  autoHideDelay?: number;
  lineBreakBefore?: boolean;
}

export interface NotificationSectionItem {
  title: string;
  description: string;
  buttons: NotificationButtonItem[];
}

const messageText = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vitae orci ut dolor scelerisque aliquam.';
const typeButtons: Array<Omit<NotificationButtonItem, 'position'>> = [
  { label: 'Info', buttonTone: 'info', toastTone: 'info', lineBreakBefore: true },
  { label: 'Sucesso', buttonTone: 'success', toastTone: 'success' },
  { label: 'Aviso', buttonTone: 'warning', toastTone: 'warning' },
  { label: 'Erro', buttonTone: 'danger', toastTone: 'error' },
  { label: 'Preto', buttonTone: 'inverse', toastTone: 'black' },
  { label: 'Branco', buttonTone: 'white', toastTone: 'white' }
];

function notificationDescription(): string {
  return 'Adicione um handler onclick e chame o método $.Notification.notify com parâmetro contendo o tipo de alerta, texto da mensagem e posição do alerta.';
}

function positionButtons(vertical: 'top' | 'bottom'): NotificationButtonItem[] {
  const labelByPosition: Array<{ label: string; position: NotificationToastPosition }> = [
    { label: 'Esquerda', position: `${vertical}-left` },
    { label: 'Direita', position: `${vertical}-right` },
    { label: 'Centro', position: `${vertical}-center` }
  ];

  return [
    ...labelByPosition.map((item) => ({
      label: item.label,
      buttonTone: 'default' as ButtonTone,
      toastTone: 'custom' as NotificationToastTone,
      position: item.position
    })),
    ...typeButtons.map((item) => ({
      ...item,
      position: `${vertical}-left` as NotificationToastPosition
    }))
  ];
}

function toneButtons(position: NotificationToastPosition): NotificationButtonItem[] {
  return [
    { label: 'Custom', buttonTone: 'default', toastTone: 'custom', position },
    ...typeButtons.map((item) => ({
      ...item,
      lineBreakBefore: false,
      position
    }))
  ];
}

function autoHideButtons(): NotificationButtonItem[] {
  return toneButtons('top-right').map((item) => ({
    ...item,
    title: 'Será fechado em 5 segundos...',
    autoHideDelay: 5000
  }));
}

function confirmButtons(): NotificationButtonItem[] {
  const positions: NotificationToastPosition[] = [
    'top-left',
    'top-left',
    'top-right',
    'top-center',
    'bottom-left',
    'bottom-center',
    'bottom-right'
  ];

  return toneButtons('top-left').map((item, index) => ({
    ...item,
    position: positions[index],
    title: 'Você tem certeza?!',
    confirm: true
  }));
}

export const sampleNotificationText = messageText;

export const notificationSections: NotificationSectionItem[] = [
  {
    title: 'Notificação (Topo)',
    description: notificationDescription(),
    buttons: positionButtons('top')
  },
  {
    title: 'Notificação (Rodapé)',
    description: notificationDescription(),
    buttons: positionButtons('bottom')
  },
  {
    title: 'Notificação (Meio esquerdo)',
    description: notificationDescription(),
    buttons: toneButtons('left-middle')
  },
  {
    title: 'Notificação (Meio direito)',
    description: notificationDescription(),
    buttons: toneButtons('right-middle')
  },
  {
    title: 'Auto ocultar em 5 segundos',
    description: notificationDescription(),
    buttons: autoHideButtons()
  },
  {
    title: 'Notificação de confirmação',
    description: notificationDescription(),
    buttons: confirmButtons()
  }
];
