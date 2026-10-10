import type { ReactNode } from 'react';
import './Panel.css';
import { joinClassNames } from '@/utils/joinClassNames';

export type PanelTone =
  | 'default'
  | 'custom'
  | 'primary'
  | 'success'
  | 'info'
  | 'warning'
  | 'danger'
  | 'purple'
  | 'pink'
  | 'inverse';

export type PanelAppearance = 'standard' | 'border' | 'color';

interface PanelProps {
  title?: string;
  tone?: PanelTone;
  appearance?: PanelAppearance;
  body?: ReactNode;
  children?: ReactNode;
  footer?: ReactNode;
  headingAction?: ReactNode;
  className?: string;
}

export default function Panel({
  title,
  tone = 'default',
  appearance = 'standard',
  body,
  children,
  footer,
  headingAction,
  className
}: PanelProps): JSX.Element {
  return (
    <div
      className={joinClassNames(
        'panelCard panel',
        `panelCard--${tone}`,
        appearance === 'border' && 'panelCard--border panel-border',
        appearance === 'color' && 'panelCard--color panel-color',
        className
      )}
    >
      {title !== undefined ? <div className="panelCard__heading panel-heading">
        {headingAction}
        <h3 className="panelCard__title panel-title">{title}</h3>
      </div> : null}

      {body ? <div className="panelCard__body panel-body">{body}</div> : null}

      {children}

      {footer ? <div className="panelCard__footer panel-footer">{footer}</div> : null}
    </div>
  );
}
