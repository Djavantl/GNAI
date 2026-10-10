export interface SalesAnalyticsChartPoint {
  y: string;
  a: number;
  b: number;
  c: number;
}

export interface MorrisBarOptions {
  element: HTMLElement;
  data: SalesAnalyticsChartPoint[];
  xkey: string;
  ykeys: string[];
  labels: string[];
  stacked: boolean;
  hideHover: 'auto';
  resize: boolean;
  gridLineColor: string;
  barColors: string[];
}

export interface MorrisAreaOptions {
  element: HTMLElement;
  data: SalesAnalyticsChartPoint[];
  xkey: string;
  ykeys: string[];
  labels: string[];
  hideHover: 'auto';
  pointFillColors: string[];
  pointStrokeColors: string[];
  resize: boolean;
  gridLineColor: string;
  lineColors: string[];
  pointSize: number;
  lineWidth: number;
}

export interface MorrisRuntime {
  Bar: new (options: MorrisBarOptions) => unknown;
  Area: new (options: MorrisAreaOptions) => unknown;
}

export interface SalesAnalyticsWindow extends Window {
  Raphael?: unknown;
  Morris?: MorrisRuntime;
}
