import type { ProgressBarSegment, ProgressBarSize, ProgressBarTone } from '@/components/UI/ProgressBar/ProgressBar';

export interface ProgressDemoItem {
  value: number;
  tone: ProgressBarTone;
  size?: ProgressBarSize;
  striped?: boolean;
  active?: boolean;
  animated?: boolean;
  label?: string;
}

export const basicProgressBars: ProgressDemoItem[] = [
  { value: 95, tone: 'custom' },
  { value: 60, tone: 'primary' },
  { value: 48, tone: 'success' },
  { value: 82, tone: 'info' },
  { value: 95, tone: 'warning' },
  { value: 60, tone: 'danger' },
  { value: 48, tone: 'pink' },
  { value: 82, tone: 'purple' },
  { value: 95, tone: 'inverse' }
];

export const stripedProgressBars: ProgressDemoItem[] = basicProgressBars.map((item: ProgressDemoItem) => ({
  ...item,
  striped: true
}));

export const sizedProgressBars: ProgressDemoItem[] = [
  { value: 89, tone: 'danger', size: 'sm' },
  { value: 77, tone: 'inverse' },
  { value: 45, tone: 'success', size: 'md', striped: true },
  { value: 67, tone: 'warning', size: 'lg', striped: true, active: true }
];

export const activeProgressBars: ProgressDemoItem[] = [
  { value: 60, tone: 'info', striped: true, active: true },
  { value: 48, tone: 'pink', striped: true, active: true },
  { value: 82, tone: 'primary', striped: true, active: true },
  { value: 95, tone: 'purple', striped: true, active: true }
];

export const labeledProgressBars: ProgressDemoItem[] = [
  { value: 85, tone: 'pink', size: 'md', label: '85%' },
  { value: 56, tone: 'inverse', label: '56%' },
  { value: 96, tone: 'purple', size: 'lg', label: '96%' }
];

export const stackedProgressSegments: ProgressBarSegment[] = [
  { value: 20, tone: 'success', striped: true },
  { value: 10, tone: 'info' },
  { value: 15, tone: 'warning', striped: true, active: true },
  { value: 30, tone: 'danger', striped: true, active: true }
];

export const entranceProgressBars: ProgressDemoItem[] = [
  { value: 60, tone: 'primary', animated: true },
  { value: 48, tone: 'pink', animated: true },
  { value: 82, tone: 'info', animated: true },
  { value: 95, tone: 'purple', animated: true }
];

export const entranceStripedProgressBars: ProgressDemoItem[] = [
  { value: 48, tone: 'success', striped: true, animated: true },
  { value: 60, tone: 'warning', striped: true, animated: true },
  { value: 95, tone: 'inverse', striped: true, animated: true },
  { value: 81, tone: 'danger', striped: true, animated: true }
];

export const verticalProgressBars: ProgressDemoItem[] = [
  { value: 89, tone: 'danger', size: 'sm' },
  { value: 77, tone: 'inverse' },
  { value: 45, tone: 'success', size: 'md', striped: true },
  { value: 67, tone: 'warning', size: 'lg', striped: true, active: true }
];
