import type { SparklineLineSeries } from '@/components/UI/SparklineChart/SparklineChart';

export interface DashboardThreeSparklineMetric {
  label: string;
  value: string;
}

export interface DashboardThreeSparklineCardData {
  barColor?: string;
  barSpacing?: string;
  barValues?: number[];
  barWidth?: string;
  chartHeight: number;
  chartType: 'pie' | 'bar' | 'line';
  lineSeries?: SparklineLineSeries[];
  metrics: DashboardThreeSparklineMetric[];
  pieColors?: string[];
  pieValues?: number[];
  title: string;
}
