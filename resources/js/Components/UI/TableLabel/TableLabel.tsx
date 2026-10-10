import type { ReactNode } from 'react';
import './TableLabel.css';
export default function TableLabel({
  tone = 'success',
  children,
}: {
  tone?: string;
  children: ReactNode;
}): JSX.Element {
  return <span className={`tableLabel tableLabel--${tone}`}>{children}</span>;
}
