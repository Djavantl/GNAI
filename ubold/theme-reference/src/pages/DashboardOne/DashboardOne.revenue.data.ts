export interface RevenueMetric {
  label: string;
  value: string;
}

export interface RevenueSummary {
  title: string;
  caption: string;
  amount: string;
  progress: number;
  metrics: RevenueMetric[];
}

export const dashboardRevenueSummary: RevenueSummary = {
  title: 'Receita total',
  caption: 'Total de vendas realizadas hoje',
  amount: '$75',
  progress: 80,
  metrics: [
    { label: 'Meta', value: '$1000' },
    { label: 'Última semana', value: '$523' },
    { label: 'Último mês', value: '$965' }
  ]
};
