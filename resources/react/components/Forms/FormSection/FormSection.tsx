import { useId, type ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import './FormSection.css';

interface FormSectionProps {
  id?: string;
  title: string;
  description?: ReactNode;
  children: ReactNode;
  variant?: 'card' | 'inline';
  iconClass?: string;
}
export default function FormSection({ id, title, description, children, variant = 'card', iconClass = 'fa fa-list-alt' }: FormSectionProps): React.JSX.Element {
  const generatedId = useId();
  const titleId = `${id ?? generatedId}-title`;
  const Heading = variant === 'inline' ? 'h2' : 'h4';
  const content = <>
    <Heading id={titleId} className="formSection__title">
      <i className={iconClass} aria-hidden="true" /><span>{title}</span>
    </Heading>
    {description ? <p className="formSection__description">{description}</p> : null}
    {children}
  </>;
  return variant === 'inline'
    ? <section id={id} aria-labelledby={titleId} className="formSection formSection--inline">{content}</section>
    : <CardBox className="formSection">{content}</CardBox>;
}

export function FormDemoDescription(): React.JSX.Element {
  return <>Use as classes de botão em um elemento <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code> ou <code>&lt;input&gt;</code>.</>;
}
