import type { ElementType, ReactNode } from 'react';
import { joinClassNames } from '@/utils/joinClassNames';

interface CardBoxProps {
  as?: ElementType;
  children: ReactNode;
  className?: string;
}

export default function CardBox({
  as: Component = 'div',
  children,
  className
}: CardBoxProps): JSX.Element {
  return <Component className={joinClassNames('card-box', className)}>{children}</Component>;
}
