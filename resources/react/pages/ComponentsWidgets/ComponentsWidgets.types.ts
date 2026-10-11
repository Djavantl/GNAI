import type { SparklineChartConfig } from '@/components/UI/SparklineChart/SparklineChart';

export type Tone = 'info' | 'pink' | 'purple' | 'success' | 'custom' | 'warning' | 'inverse' | 'danger' | 'primary';

export type ColumnSize = 'quarter' | 'third' | 'full' | 'half';

export interface IconMetricItem {
  tone: Tone;
  iconClass: string;
}

export interface InlineMetricItem {
  tone: Tone;
  iconClass: string;
  value: string;
  label: string;
}

export interface StatusMetricItem {
  title: string;
  value: string;
  tone: Tone | 'dark';
  description: string;
  trendTone: 'primary' | 'danger';
  trendIcon: string;
  trend: string;
}

export interface MiniStatItem {
  tone: Tone;
  iconClass: string;
  value: number;
  label: string;
  target: number;
}

export interface PanelMetricItem {
  tone: Tone;
  iconClass: string;
  value: number;
  label: string;
}

export type InboxItem = [avatar: string, author: string, text: string, date: string];

export interface ChatItem {
  odd: boolean;
  avatar: string;
  alt: string;
  time: string;
  author: string;
  text: string;
}

export interface TodoItem {
  id: number;
  text: string;
  done: boolean;
}

export type TransactionItem = [icon: string, text: string, price: string, priceClass: string, date: string];

export type SweetAlertMessage = 'chat' | 'todo';

export type WidgetSparklineConfig = SparklineChartConfig;
