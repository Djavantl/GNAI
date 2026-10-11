export interface DashboardTwoRevenueWindow extends Window {
  Morris?: {
    Area: new (options: DashboardTwoRevenueAreaOptions) => unknown;
  };
}

export interface DashboardTwoRevenueAreaOptions {
  element: HTMLElement;
  pointSize: number;
  lineWidth: number;
  data: Array<Record<string, number | string>>;
  xkey: string;
  ykeys: string[];
  labels: string[];
  hideHover: 'auto';
  pointFillColors: string[];
  pointStrokeColors: string[];
  resize: boolean;
  gridLineColor: string;
  lineColors: string[];
}
