export interface DashboardStatItem {
  iconClass: string;
  title: string;
  value: string;
  tone: 'info' | 'pink' | 'purple' | 'success';
  animatedClassName?: string;
}

export const dashboardStats: DashboardStatItem[] = [
  {
    iconClass: 'md md-attach-money text-info',
    title: 'Receita total',
    value: '31,570',
    tone: 'info',
    animatedClassName: 'fadeInDown animated'
  },
  {
    iconClass: 'md md-add-shopping-cart text-pink',
    title: 'Vendas de hoje',
    value: '280',
    tone: 'pink'
  },
  {
    iconClass: 'md md-equalizer text-purple',
    title: 'Conversão',
    value: '0.16%',
    tone: 'purple'
  },
  {
    iconClass: 'md md-remove-red-eye text-success',
    title: 'Visitas de hoje',
    value: '64,570',
    tone: 'success'
  }
];
