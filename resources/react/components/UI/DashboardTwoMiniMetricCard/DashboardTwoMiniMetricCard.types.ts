export interface DashboardTwoMiniMetricFraction {
  total: number;
  value: number;
}

export interface DashboardTwoMiniMetricItem {
  barValues?: number[];
  chartType: 'bars' | 'pie' | 'donut';
  colorTone: 'info' | 'custom' | 'danger';
  fraction?: DashboardTwoMiniMetricFraction;
  iconClass?: string;
  title: string;
  value: string;
}
