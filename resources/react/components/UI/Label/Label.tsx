import './Label.css';
import React from "react";
import { joinClassNames } from '@/utils/joinClassNames';

type LabelVariant = 'default' | 'primary' | 'danger' | 'success' | 'pink' | 'purple' | 'warning' | 'info' | 'inverse';

interface LabelProps {
  children: React.ReactNode;
  className?: string;
  variant?: LabelVariant;
  ariaHidden?: boolean;
}

export default function Label({
  children,
  className = '',
  variant = 'default',
  ariaHidden = false
}: LabelProps): React.JSX.Element {
  return (
    <span
      aria-hidden={ariaHidden}
      className={joinClassNames('label', `label--${variant}`, className)}
    >
      {children}
    </span>
  );
}
