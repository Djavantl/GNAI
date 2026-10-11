import type { DashboardThreeMetricItem } from './DashboardThreeMetricCard.types';
import type { DashboardThreeProjectItem } from './DashboardThreeProjectsPortlet.types';
import type { DashboardThreeSparklineCardData } from './DashboardThreeSparklineCard.types';

export const dashboardThreeMetrics: DashboardThreeMetricItem[] = [
  {
    key: 'total-revenue',
    value: '12560',
    title: 'Receita total',
    colorTone: 'info',
    iconClass: 'icon-layers',
    chartType: 'bar',
    chartValues: '5,3,9,6,5,9,7,3,5,2,9,7,2,1',
    chartColors: ['#34d3eb', '#ebeff2'],
    chartWidth: 120,
    chartHeight: 45
  },
  {
    key: 'weekly-sales',
    value: '356',
    title: 'Média de vendas semanais',
    colorTone: 'custom',
    iconClass: 'icon-layers',
    chartType: 'pie',
    chartValues: '1/5',
    chartColors: ['#5fbeaa', '#ebeff2'],
    chartWidth: 50,
    chartHeight: 45
  },
  {
    key: 'visitors',
    value: '96562',
    title: 'Visitantes',
    colorTone: 'danger',
    iconClass: 'icon-layers',
    chartType: 'donut',
    chartValues: '1/5',
    chartColors: ['#f05050', '#ebeff2'],
    chartWidth: 50,
    chartHeight: 45
  }
];

export const dashboardThreeSparklineCards: DashboardThreeSparklineCardData[] = [
  {
    title: 'Vendas diárias',
    chartType: 'pie',
    chartHeight: 165,
    pieValues: [20, 40, 30, 10],
    pieColors: ['#dcdcdc', '#5d9cec', '#36404a', '#5fbeaa'],
    metrics: [
      { label: 'Meta', value: '$1000' },
      { label: 'Última semana', value: '$523' },
      { label: 'Último mês', value: '$965' }
    ]
  },
  {
    title: 'Vendas semanais',
    chartType: 'bar',
    chartHeight: 165,
    barColor: '#fb6d9d',
    barWidth: '10',
    barSpacing: '3',
    barValues: [3, 6, 7, 8, 6, 4, 7, 10, 12, 7, 4, 9, 12, 13, 11, 12],
    metrics: [
      { label: 'Meta', value: '$1000' },
      { label: 'Última semana', value: '$523' },
      { label: 'Último mês', value: '$965' }
    ]
  },
  {
    title: 'Vendas mensais',
    chartType: 'line',
    chartHeight: 165,
    lineSeries: [
      { values: [0, 23, 43, 35, 44, 45, 56, 37, 40], color: '#fb6d9d', rangeMax: 50 },
      { values: [25, 23, 26, 24, 25, 32, 30, 24, 19], color: '#5d9cec', rangeMax: 40, composite: true }
    ],
    metrics: [
      { label: 'Meta', value: '$1000' },
      { label: 'Última semana', value: '$523' },
      { label: 'Último mês', value: '$965' }
    ]
  }
];

export const dashboardThreeProjects: DashboardThreeProjectItem[] = [
  { id: 1, projectName: 'Interface Admin v1', startDate: '01/01/2015', dueDate: '26/04/2015', status: 'Released', statusVariant: 'info', assign: 'Coderthemes' },
  { id: 2, projectName: 'Interface Frontend v1', startDate: '01/01/2015', dueDate: '26/04/2015', status: 'Released', statusVariant: 'success', assign: 'Coderthemes' },
  { id: 3, projectName: 'Interface Admin v1.1', startDate: '01/05/2015', dueDate: '10/05/2015', status: 'Pending', statusVariant: 'pink', assign: 'Coderthemes' },
  { id: 4, projectName: 'Interface Frontend v1.1', startDate: '01/01/2015', dueDate: '31/05/2015', status: 'Work in Progress', statusVariant: 'purple', assign: 'Coderthemes' },
  { id: 5, projectName: 'Interface Admin v1.3', startDate: '01/01/2015', dueDate: '31/05/2015', status: 'Coming soon', statusVariant: 'warning', assign: 'Coderthemes' },
  { id: 6, projectName: 'Interface Admin v1.3', startDate: '01/01/2015', dueDate: '31/05/2015', status: 'Coming soon', statusVariant: 'primary', assign: 'Coderthemes' },
  { id: 7, projectName: 'Interface Admin v1.3', startDate: '01/01/2015', dueDate: '31/05/2015', status: 'Cool', statusVariant: 'info', assign: 'Coderthemes' },
  { id: 8, projectName: 'Interface Admin v1.3', startDate: '01/01/2015', dueDate: '31/05/2015', status: 'Coming soon', statusVariant: 'warning', assign: 'Coderthemes' }
];
