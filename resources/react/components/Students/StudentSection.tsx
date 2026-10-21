import type { ReactNode } from 'react';
import FormSection from '@/components/Forms/FormSection/FormSection';

export default function StudentSection({
  id,
  title,
  iconClass,
  children,
}: {
  id: string;
  title: string;
  iconClass: string;
  children: ReactNode;
}): React.JSX.Element {
  return (
    <FormSection
      id={id}
      title={title}
      iconClass={iconClass}
      variant="inline"
    >
      {children}
    </FormSection>
  );
}
