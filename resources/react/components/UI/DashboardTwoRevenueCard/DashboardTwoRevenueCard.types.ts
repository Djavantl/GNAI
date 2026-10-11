export interface DashboardTwoRevenueLegendItem {
  label: string;
  color: string;
}

export interface DashboardTwoRevenueSeriesItem {
  year: string;
  desktop: number;
  tablet: number;
  mobile: number;
}

export interface DashboardTwoRevenueProgressItem {
  label: string;
  value: number;
  tone: 'primary' | 'pink' | 'info' | 'warning' | 'success';
}
