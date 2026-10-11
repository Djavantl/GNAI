import type { ReactNode } from 'react';
import CardBox from '@/components/UI/CardBox/CardBox';
import './FormSection.css';

interface FormSectionProps { title: string; description?: ReactNode; children: ReactNode; }
export default function FormSection({ title, description, children }: FormSectionProps): React.JSX.Element {
  return <CardBox className="formSection">
    <h4 className="formSection__title"><b>{title}</b></h4>
    {description ? <p className="formSection__description">{description}</p> : null}
    {children}
  </CardBox>;
}

export function FormDemoDescription(): React.JSX.Element {
  return <>Use as classes de botão em um elemento <code>&lt;a&gt;</code>, <code>&lt;button&gt;</code> ou <code>&lt;input&gt;</code>.</>;
}
