export interface NotificationItem {
  iconClass: string;
  title: string;
  description: string;
}

export interface ProfileLink {
  iconClass: string;
  label: string;
}

export const notifications: NotificationItem[] = [
  {
    iconClass: 'fa fa-diamond fa-2x text-primary',
    title: 'Um novo pedido foi realizado',
    description: 'Existem novas configurações disponíveis'
  },
  {
    iconClass: 'fa fa-cog fa-2x text-custom',
    title: 'Novas configurações',
    description: 'Existem novas configurações disponíveis'
  },
  {
    iconClass: 'fa fa-bell-o fa-2x text-danger',
    title: 'Atualizações',
    description: 'Existem 2 novas atualizações disponíveis'
  },
  {
    iconClass: 'fa fa-user-plus fa-2x text-info',
    title: 'Novo usuário registrado',
    description: 'Você tem 10 mensagens não lidas'
  },
  {
    iconClass: 'fa fa-diamond fa-2x text-primary',
    title: 'Um novo pedido foi realizado',
    description: 'Existem novas configurações disponíveis'
  },
  {
    iconClass: 'fa fa-cog fa-2x text-custom',
    title: 'Novas configurações',
    description: 'Existem novas configurações disponíveis'
  }
];

export const profileLinks: ProfileLink[] = [
  { iconClass: 'ti-user', label: 'Perfil' },
  { iconClass: 'ti-settings', label: 'Configurações' },
  { iconClass: 'ti-lock', label: 'Tela de bloqueio' },
  { iconClass: 'ti-power-off', label: 'Sair' }
];
