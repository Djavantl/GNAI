import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';

export default function StudentSection({ id, title, actions, children }: {
  id: string; title: string; actions?: ReactNode; children: ReactNode;
}): React.JSX.Element {
  return <section id={id} aria-labelledby={`${id}-title`} className="studentsPage__section">
    <CardBox><h2 id={`${id}-title`}>{title}</h2>{children}
      {actions ? <div className="studentsPage__sectionActions">{actions}</div> : null}
    </CardBox>
  </section>;
}
export function StudentEmpty({ children }: { children: ReactNode }): React.JSX.Element {
  return <p className="studentsPage__empty">{children}</p>;
}
