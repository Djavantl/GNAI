import './Badge.css';
import React from "react";
import { joinClassNames } from '@/utils/joinClassNames';

type BadgeVariant = 'custom' | 'default' | 'primary' | 'success' | 'info' | 'inverse' | 'warning' | 'danger' | 'purple' | 'pink';
type BadgeSize = 'md' | 'xs';
type BadgeShape = 'pill' | 'circle';

interface BadgeProps {
  children: React.ReactNode;
  className?: string;
  variant?: BadgeVariant;
  size?: BadgeSize;
  shape?: BadgeShape;
  ariaHidden?: boolean;
}

export default function Badge({
  children,
  className = '',
  variant = 'custom',
  size = 'md',
  shape = 'pill',
  ariaHidden = false
}: BadgeProps): JSX.Element {
  return (
    <span
      aria-hidden={ariaHidden}
      className={joinClassNames(
        'badge',
        `badge--${variant}`,
        `badge--${size}`,
        `badge--${shape}`,
        className
      )}
    >
      {children}
    </span>
  );
}
