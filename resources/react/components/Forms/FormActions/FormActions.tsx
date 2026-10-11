import type { ReactNode } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import './FormActions.css';

interface FormActionsProps {
  children: ReactNode;
  status?: ReactNode;
  className?: string;
}

export default function FormActions({ children, status, className }: FormActionsProps): React.JSX.Element {
  return <div className={joinClassNames('formActions', className)}>
    <span className="formActions__status" role="status">{status}</span>
    {children}
  </div>;
}
