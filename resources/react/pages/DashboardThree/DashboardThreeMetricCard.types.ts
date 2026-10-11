export interface DashboardThreeMetricItem {
  chartColors: string[];
  chartHeight: number;
  chartType: 'bar' | 'pie' | 'donut';
  chartValues: string;
  chartWidth: number;
  colorTone: 'info' | 'custom' | 'danger';
  iconClass?: string;
  key: 'total-revenue' | 'weekly-sales' | 'visitors';
  title: string;
  value: string;
}
