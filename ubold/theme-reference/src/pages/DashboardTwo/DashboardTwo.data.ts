import type { DashboardTwoChatMessage } from '@/Components/UI/DashboardTwoChatCard/DashboardTwoChatCard.types';
import type { DashboardTwoMiniMetricItem } from '@/Components/UI/DashboardTwoMiniMetricCard/DashboardTwoMiniMetricCard.types';
import type { DashboardTwoRevenueLegendItem, DashboardTwoRevenueProgressItem, DashboardTwoRevenueSeriesItem } from '@/Components/UI/DashboardTwoRevenueCard/DashboardTwoRevenueCard.types';
import type { DashboardTwoTodoItem } from '@/Components/UI/DashboardTwoTodoCard/DashboardTwoTodoCard.types';
import type { DashboardTwoTransactionItem } from '@/Components/UI/DashboardTwoTransactionsCard/DashboardTwoTransactionsCard.types';

export interface DashboardTwoStatItem {
  iconClass: string;
  iconToneClass: string;
  title: string;
  value: string;
}

export const dashboardTwoStats: DashboardTwoStatItem[] = [
  {
    iconClass: 'md md-attach-money text-primary',
    iconToneClass: 'primary',
    title: 'Receita total',
    value: '50568'
  },
  {
    iconClass: 'md md-add-shopping-cart text-pink',
    iconToneClass: 'pink',
    title: 'Vendas',
    value: '1256'
  },
  {
    iconClass: 'md md-store-mall-directory text-info',
    iconToneClass: 'info',
    title: 'Lojas',
    value: '18'
  },
  {
    iconClass: 'md md-account-child text-custom',
    iconToneClass: 'custom',
    title: 'Usuários',
    value: '8564'
  }
];

export const dashboardTwoRevenueData = {
  title: 'Receita total',
  legend: [
    { label: 'Desktops', color: '#36404a' },
    { label: 'Tablets', color: '#5d9cec' },
    { label: 'Celulares', color: '#bbbbbb' }
  ] satisfies DashboardTwoRevenueLegendItem[],
  series: [
    { year: '2009', desktop: 10, tablet: 20, mobile: 30 },
    { year: '2010', desktop: 75, tablet: 65, mobile: 30 },
    { year: '2011', desktop: 50, tablet: 40, mobile: 30 },
    { year: '2012', desktop: 75, tablet: 65, mobile: 30 },
    { year: '2013', desktop: 50, tablet: 40, mobile: 30 },
    { year: '2014', desktop: 75, tablet: 65, mobile: 30 },
    { year: '2015', desktop: 90, tablet: 60, mobile: 30 }
  ] satisfies DashboardTwoRevenueSeriesItem[],
  progressItems: [
    { label: 'iMacs', value: 80, tone: 'primary' },
    { label: 'iBooks', value: 50, tone: 'pink' },
    { label: 'iPhone 5s', value: 70, tone: 'info' },
    { label: 'iPhone 6', value: 65, tone: 'warning' },
    { label: 'iPhone 6s', value: 40, tone: 'success' }
  ] satisfies DashboardTwoRevenueProgressItem[]
};

export const dashboardTwoMiniMetrics: DashboardTwoMiniMetricItem[] = [
  {
    colorTone: 'info',
    iconClass: 'icon-layers',
    title: 'Visitantes',
    value: '1256',
    chartType: 'bars',
    barValues: [5, 3, 9, 6, 5, 9, 7, 3, 5, 2, 9, 7, 2, 1]
  },
  {
    colorTone: 'custom',
    iconClass: 'icon-layers',
    title: 'Visitantes',
    value: '1256',
    chartType: 'pie',
    fraction: { value: 1, total: 5 }
  },
  {
    colorTone: 'danger',
    iconClass: 'icon-layers',
    title: 'Visitantes',
    value: '1256',
    chartType: 'donut',
    fraction: { value: 1, total: 5 }
  }
];

export const dashboardTwoTransactions: DashboardTwoTransactionItem[] = [
  { iconClass: 'ti-download text-success', amountToneClass: 'text-success', title: 'Publicidade', amount: '+$230', date: '07/09/2015' },
  { iconClass: 'ti-upload text-danger', amountToneClass: 'text-danger', title: 'Licença de suporte', amount: '-$965', date: '07/09/2015' },
  { iconClass: 'ti-download text-success', amountToneClass: 'text-success', title: 'Licença estendida', amount: '+$830', date: '07/09/2015' },
  { iconClass: 'ti-download text-success', amountToneClass: 'text-success', title: 'Publicidade', amount: '+$230', date: '05/09/2015' },
  { iconClass: 'ti-upload text-danger', amountToneClass: 'text-danger', title: 'Novos plugins adicionados', amount: '-$452', date: '05/09/2015' },
  { iconClass: 'ti-download text-success', amountToneClass: 'text-success', title: 'Google Inc.', amount: '+$230', date: '04/09/2015' },
  { iconClass: 'ti-upload text-danger', amountToneClass: 'text-danger', title: 'Anúncio do Facebook', amount: '-$364', date: '03/09/2015' },
  { iconClass: 'ti-download text-success', amountToneClass: 'text-success', title: 'Nova venda', amount: '+$230', date: '03/09/2015' },
  { iconClass: 'ti-download text-success', amountToneClass: 'text-success', title: 'Publicidade', amount: '+$230', date: '29/08/2015' },
  { iconClass: 'ti-upload text-danger', amountToneClass: 'text-danger', title: 'Licença de suporte', amount: '-$854', date: '27/08/2015' }
];

export const dashboardTwoInitialMessages: DashboardTwoChatMessage[] = [
  {
    id: 'm1',
    author: 'John Deo',
    avatar: '/assets/gnai/images/avatar-1.jpg',
    time: '10:00',
    text: 'Olá!'
  },
  {
    id: 'm2',
    author: 'Smith',
    avatar: '/assets/gnai/images/users/avatar-5.jpg',
    time: '10:01',
    text: 'Oi, como você está? E sobre a nossa próxima reunião?',
    isCurrentUser: true
  },
  {
    id: 'm3',
    author: 'John Deo',
    avatar: '/assets/gnai/images/avatar-1.jpg',
    time: '10:01',
    text: 'Sim, está tudo certo.'
  },
  {
    id: 'm4',
    author: 'Smith',
    avatar: '/assets/gnai/images/users/avatar-5.jpg',
    time: '10:02',
    text: 'Ótimo, isso é excelente.',
    isCurrentUser: true
  }
];

export const dashboardTwoInitialTodos: DashboardTwoTodoItem[] = [
  { id: '1', text: 'Criar o tema de uma página', done: false },
  { id: '2', text: 'Construir uma aplicação baseada em JS', done: true },
  { id: '3', text: 'Criar a página de componentes', done: true },
  { id: '4', text: 'Fazer os testes', done: true },
  { id: '5', text: 'Ajustar os detalhes finais', done: false },
  { id: '6', text: 'Construir uma aplicação em Angular', done: true }
];
