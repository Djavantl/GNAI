import type { ReactNode } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import './ProgressBar.css';

export type ProgressBarTone = 'custom' | 'primary' | 'success' | 'info' | 'warning' | 'danger' | 'inverse' | 'purple' | 'pink';
export type ProgressBarSize = 'default' | 'sm' | 'md' | 'lg';
export type ProgressBarOrientation = 'horizontal' | 'vertical' | 'vertical-bottom';

export interface ProgressBarSegment {
  value: number;
  tone: ProgressBarTone;
  striped?: boolean;
  active?: boolean;
  animated?: boolean;
  label?: ReactNode;
}

interface ProgressBarProps {
  value?: number;
  tone?: ProgressBarTone;
  size?: ProgressBarSize;
  striped?: boolean;
  active?: boolean;
  animated?: boolean;
  label?: ReactNode;
  segments?: ProgressBarSegment[];
  orientation?: ProgressBarOrientation;
  className?: string;
}

function renderSegment(
  segment: ProgressBarSegment,
  orientation: ProgressBarOrientation,
  index: number
): JSX.Element {
  const dimension = orientation === 'horizontal' ? 'width' : 'height';

  return (
    <div
      key={`${segment.tone}-${segment.value}-${index}`}
      className={joinClassNames(
        'progressBar__bar',
        `progressBar__bar--${segment.tone}`,
        segment.striped && 'progressBar__bar--striped',
        segment.active && 'progressBar__bar--active',
        segment.animated && 'progressBar__bar--animated'
      )}
      role="progressbar"
      aria-valuenow={segment.value}
      aria-valuemin={0}
      aria-valuemax={100}
      style={{ [dimension]: `${segment.value}%` }}
    >
      {segment.label ?? <span className="progressBar__srOnly">{segment.value}% Complete</span>}
    </div>
  );
}

export default function ProgressBar({
  value = 0,
  tone = 'custom',
  size = 'default',
  striped = false,
  active = false,
  animated = false,
  label,
  segments,
  orientation = 'horizontal',
  className
}: ProgressBarProps): JSX.Element {
  const resolvedSegments: ProgressBarSegment[] = segments ?? [
    {
      value,
      tone,
      striped,
      active,
      animated,
      label
    }
  ];

  return (
    <div
      className={joinClassNames(
        'progressBar',
        size !== 'default' && `progressBar--${size}`,
        orientation !== 'horizontal' && `progressBar--${orientation}`,
        className
      )}
    >
      {resolvedSegments.map((segment: ProgressBarSegment, index: number) => renderSegment(segment, orientation, index))}
    </div>
  );
}
