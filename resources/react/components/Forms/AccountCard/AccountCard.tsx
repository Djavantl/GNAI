import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import './AccountCard.css';

export default function AccountCard({ title, children }: {
  title?: ReactNode;
  children: ReactNode;
}): React.JSX.Element {
  return <CardBox className="accountCard">
    {title ? <div className="accountCard__heading"><h3>{title}</h3></div> : null}
    <div className="accountCard__body">{children}</div>
  </CardBox>;
}
