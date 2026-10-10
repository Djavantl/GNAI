import type { CSSProperties, ReactNode } from 'react';

export interface WizardStep { id: string; title: string; content: ReactNode; }
export type WizardTransition = 'slideLeft' | 'fade';
export interface FormWizardProps {
  id: string;
  steps: WizardStep[];
  orientation?: 'horizontal' | 'vertical';
  transition?: WizardTransition;
  transitionDuration?: number;
  labels?: Partial<{ previous: string; next: string; finish: string; navigation: string }>;
  onStepChanging?: (currentIndex: number, nextIndex: number) => boolean;
  onFinish?: () => boolean | void;
  className?: string;
  style?: CSSProperties;
}

export interface WizardNavigationState {
  current: number;
  previous: number | null;
  visited: number[];
  error: boolean;
  finished: boolean;
}
