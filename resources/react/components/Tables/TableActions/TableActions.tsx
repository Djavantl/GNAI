import type { ReactNode } from 'react';
import './TableActions.css';

export default function TableActions({
  children,
}: {
  children: ReactNode;
}): React.JSX.Element {
  return (
    <span className="tableActions">
      {children}
    </span>
  );
}
