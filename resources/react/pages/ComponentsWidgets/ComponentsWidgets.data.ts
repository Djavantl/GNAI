import type {
  ChatItem,
  IconMetricItem,
  InlineMetricItem,
  InboxItem,
  MiniStatItem,
  PanelMetricItem,
  StatusMetricItem,
  TodoItem,
  TransactionItem,
  WidgetSparklineConfig
} from './ComponentsWidgets.types';

export const iconMetricItems: IconMetricItem[] = [
  { tone: 'info', iconClass: 'md md-favorite-outline' },
  { tone: 'pink', iconClass: 'md md-favorite-outline' },
  { tone: 'purple', iconClass: 'md md-favorite-outline' },
  { tone: 'success', iconClass: 'md md-favorite-outline' },
  { tone: 'custom', iconClass: 'icon-plane' },
  { tone: 'warning', iconClass: 'icon-trophy' },
  { tone: 'inverse', iconClass: 'icon-layers' },
  { tone: 'danger', iconClass: 'icon-basket-loaded' }
];

export const inlineMetrics: InlineMetricItem[] = [
  { tone: 'primary', iconClass: 'md md-add-shopping-cart', value: '8654', label: 'Vendas totais acumuladas' },
  { tone: 'custom', iconClass: 'md md-attach-money', value: '41550', label: 'Valores de receita' },
  { tone: 'pink', iconClass: 'md md-account-child', value: '8916', label: 'Total de usuários' },
  { tone: 'purple', iconClass: 'md md-visibility', value: '12589', label: 'Total de visitas' }
];

export const statusMetrics: StatusMetricItem[] = [
  { title: 'Status da receita', value: '$5623', tone: 'primary', description: 'Receita total: $22506', trendTone: 'primary', trendIcon: 'fa fa-caret-up', trend: '10.25%' },
  { title: 'Status de vendas', value: '169', tone: 'pink', description: 'Vendas totais: 2398', trendTone: 'danger', trendIcon: 'fa fa-caret-down', trend: '7.85%' },
  { title: 'Status da receita', value: '$9653', tone: 'success', description: 'Receita total: $22506', trendTone: 'primary', trendIcon: 'fa fa-caret-up', trend: '10.25%' },
  { title: 'Status de vendas', value: '895', tone: 'dark', description: 'Vendas totais: 2398', trendTone: 'danger', trendIcon: 'fa fa-caret-down', trend: '7.85%' }
];

export const miniStats: MiniStatItem[] = [
  { tone: 'info', iconClass: 'ion-social-usd', value: 15852, label: 'Vendas totais', target: 60 },
  { tone: 'warning', iconClass: 'ion-ios7-cart', value: 956, label: 'Novos pedidos', target: 90 },
  { tone: 'pink', iconClass: 'ion-android-contacts', value: 5210, label: 'Novos usuários', target: 57 },
  { tone: 'success', iconClass: 'ion-eye', value: 20544, label: 'Visitantes únicos', target: 60 }
];

export const panelMetrics: PanelMetricItem[] = [
  { tone: 'primary', iconClass: 'md md-attach-money', value: 50568, label: 'Receita total' },
  { tone: 'pink', iconClass: 'md md-add-shopping-cart', value: 1256, label: 'Vendas' },
  { tone: 'info', iconClass: 'md md-store-mall-directory', value: 18, label: 'Lojas' },
  { tone: 'custom', iconClass: 'md md-account-child', value: 8564, label: 'Usuários' }
];

const sparkLinePrimary = [0, 23, 43, 35, 44, 45, 56, 37, 40];
const sparkLineSecondary = [25, 23, 26, 24, 25, 32, 30, 24, 19];
const sparkBars = [3, 6, 7, 8, 6, 4, 7, 10, 12, 7, 4, 9, 12, 13, 11, 12];

export const inboxItems: InboxItem[] = [
  ['avatar-1.jpg', 'Chadengle', 'Olá! Estou disponível...', '13:40'],
  ['avatar-2.jpg', 'Tomaslau', 'Terminei! Até mais...', '13:34'],
  ['avatar-3.jpg', 'Stillnotdavid', 'Este tema é incrível!', '13:17'],
  ['avatar-4.jpg', 'Kurafire', 'Prazer em conhecer você', '12:20'],
  ['avatar-5.jpg', 'Shahedk', 'Olá! Estou disponível...', '10:15'],
  ['avatar-6.jpg', 'Adhamdannaway', 'Este tema é incrível!', '09:56'],
  ['avatar-8.jpg', 'Arashasghari', 'Olá! Estou disponível...', '10:15'],
  ['avatar-9.jpg', 'Joshaustin', 'Terminei! Até mais...', '09:56']
];

export const chatItems: ChatItem[] = [
  { odd: false, avatar: '/assets/react/images/avatar-1.jpg', alt: 'male', time: '10:00', author: 'John Deo', text: 'Olá!' },
  { odd: true, avatar: '/assets/react/images/users/avatar-5.jpg', alt: 'Female', time: '10:01', author: 'Smith', text: 'Oi, como você está? E a nossa próxima reunião?' },
  { odd: false, avatar: '/assets/react/images/avatar-1.jpg', alt: 'male', time: '10:01', author: 'John Deo', text: 'Sim, está tudo certo' },
  { odd: true, avatar: '/assets/react/images/users/avatar-5.jpg', alt: 'male', time: '10:02', author: 'Smith', text: 'Uau, isso é ótimo' }
];

export const todoItems: TodoItem[] = [
  { id: 6, text: 'Construir uma aplicação Angular', done: true },
  { id: 5, text: 'Hehe!! Isso parece legal!', done: false },
  { id: 4, text: 'Testando??', done: true },
  { id: 3, text: 'Criar a página de componentes', done: true },
  { id: 2, text: 'Construir uma aplicação em JS', done: true },
  { id: 1, text: 'Criar o tema de uma página', done: false }
];

export const transactions: TransactionItem[] = [
  ['ti-download text-success', 'Publicidade', '+$230', 'text-success', '07/09/2015'],
  ['ti-upload text-danger', 'Licença de suporte', '-$965', 'text-danger', '07/09/2015'],
  ['ti-download text-success', 'Licença estendida', '+$830', 'text-success', '07/09/2015'],
  ['ti-download text-success', 'Publicidade', '+$230', 'text-success', '05/09/2015'],
  ['ti-upload text-danger', 'Novos plugins adicionados', '-$452', 'text-danger', '05/09/2015'],
  ['ti-download text-success', 'Google Inc.', '+$230', 'text-success', '04/09/2015'],
  ['ti-upload text-danger', 'Anúncio do Facebook', '-$364', 'text-danger', '03/09/2015'],
  ['ti-download text-success', 'Nova venda', '+$230', 'text-success', '03/09/2015'],
  ['ti-download text-success', 'Publicidade', '+$230', 'text-success', '29/08/2015'],
  ['ti-upload text-danger', 'Licença de suporte', '-$854', 'text-danger', '27/08/2015']
];

export const friendAvatars = ['avatar-1.jpg', 'avatar-2.jpg', 'avatar-3.jpg', 'avatar-4.jpg', 'avatar-5.jpg', 'avatar-6.jpg', 'avatar-7.jpg'];

export const sparklineWidgetCharts: WidgetSparklineConfig[] = [
  {
    chartType: 'line',
    chartHeight: 165,
    lineSeries: [
      {
        values: sparkLinePrimary,
        rangeMax: 50,
        color: '#5fbeaa',
        fillColor: 'rgba(95, 190, 170, 0.3)',
        highlightLineColor: 'rgba(0,0,0,.1)',
        highlightSpotColor: 'rgba(0,0,0,.2)'
      },
      {
        values: sparkLineSecondary,
        rangeMax: 40,
        color: '#5d9cec',
        fillColor: 'rgba(93, 156, 236, 0.3)',
        composite: true,
        highlightLineColor: 'rgba(0,0,0,.1)',
        highlightSpotColor: 'rgba(0,0,0,.2)'
      }
    ]
  },
  {
    chartType: 'bar',
    chartHeight: 165,
    barValues: sparkBars,
    barWidth: '10',
    barSpacing: '3',
    barColor: '#fb6d9d'
  },
  {
    chartType: 'pie',
    chartHeight: 165,
    pieValues: [20, 40, 30, 10],
    pieColors: ['#dcdcdc', '#5d9cec', '#36404a', '#5fbeaa']
  }
];

export const sparklineWidgetChartsSecondary: WidgetSparklineConfig[] = [
  {
    chartType: 'line',
    chartHeight: 165,
    lineSeries: [
      {
        values: sparkLinePrimary,
        rangeMax: 50,
        color: '#fb6d9d',
        fillColor: 'transparent',
        highlightLineColor: 'rgba(0,0,0,.1)',
        highlightSpotColor: 'rgba(0,0,0,.2)'
      },
      {
        values: sparkLineSecondary,
        rangeMax: 40,
        color: '#5d9cec',
        fillColor: 'transparent',
        composite: true,
        highlightLineColor: 'rgba(0,0,0,1)',
        highlightSpotColor: 'rgba(0,0,0,1)'
      }
    ]
  },
  {
    chartType: 'mouse-speed',
    chartHeight: 165,
    rangeMax: 50,
    lineColor: '#1e88e5',
    fillColor: 'rgba(30, 136, 229, 0.3)',
    highlightLineColor: 'rgba(24,147,126,.1)',
    highlightSpotColor: 'rgba(24,147,126,.2)',
    tooltipSuffix: ' pixels por segundo',
    refreshInterval: 500
  },
  {
    chartType: 'bar-line',
    chartHeight: 165,
    barValues: sparkBars,
    barWidth: '10',
    barSpacing: '3',
    barColor: '#5fbeaa',
    lineValues: sparkBars,
    lineColor: '#fb6d9d',
    fillColor: 'transparent',
    highlightLineColor: 'rgba(0,0,0,.1)',
    highlightSpotColor: 'rgba(0,0,0,.2)'
  }
];
