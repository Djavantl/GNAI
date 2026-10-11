import type { ReactNode } from 'react';
import './AppFooter.css';

export default function AppFooter({ children = 'Biblioteca de componentes' }: { children?: ReactNode }): React.JSX.Element {
  return <footer className="appFooter footer">{children}</footer>;
}
