import { useId, type ReactNode } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';
import './PageTitle.css';

interface PageTitleProps {
  title: string;
  subtitle?: string;
  actions?: ReactNode;
  titleAs?: 'h1' | 'h2' | 'h3';
  className?: string;
}

export default function PageTitle({ title, subtitle, actions, titleAs: Heading = 'h1', className }: PageTitleProps): React.JSX.Element {
  const titleId = useId();
  return <header className={joinClassNames('pageTitle', className)} aria-labelledby={titleId}>
    <div className="pageTitle__text">
      <Heading id={titleId} className="pageTitle__heading">{title}</Heading>
      {subtitle ? <p className="pageTitle__subtitle">{subtitle}</p> : null}
    </div>
    {actions ? <div className="pageTitle__actions">{actions}</div> : null}
  </header>;
}
