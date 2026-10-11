import { useEffect, useState } from 'react';
import './StatWidgetCard.css';
import { joinClassNames } from '@/utils/joinClassNames';
import { buildCounterFrames, getCounterStartValue } from '@/utils/counterUp';

type StatWidgetTone = 'info' | 'pink' | 'purple' | 'success';

interface StatWidgetCardProps {
  iconClass: string;
  title: string;
  value: string;
  tone: StatWidgetTone;
  animatedClassName?: string;
}

export default function StatWidgetCard({
  iconClass,
  title,
  value,
  tone,
  animatedClassName
}: StatWidgetCardProps): React.JSX.Element {
  const [displayValue, setDisplayValue] = useState<string>(() => getCounterStartValue(value));

  useEffect(() => {
    const frames = buildCounterFrames(value, 1200, 100);
    const timeouts: number[] = [];

    setDisplayValue(getCounterStartValue(value));

    frames.forEach((frame: string, index: number) => {
      timeouts.push(
        window.setTimeout(() => {
          setDisplayValue(frame);
        }, (index + 1) * 100)
      );
    });

    return () => {
      timeouts.forEach((timeoutId: number) => {
        window.clearTimeout(timeoutId);
      });
    };
  }, [value]);

  return (
    <div className={joinClassNames('statWidgetCard', 'widget-bg-color-icon', 'card-box', animatedClassName)}>
      <div className={joinClassNames('statWidgetCard__icon', 'bg-icon', `statWidgetCard__icon--${tone}`, 'pull-left')}>
        <i className={iconClass} aria-hidden="true" />
      </div>
      <div className="statWidgetCard__content text-right">
        <h3 className="statWidgetCard__value text-dark">
          <b>{displayValue}</b>
        </h3>
        <p className="statWidgetCard__title text-muted">{title}</p>
      </div>
      <div className="clearfix" />
    </div>
  );
}
