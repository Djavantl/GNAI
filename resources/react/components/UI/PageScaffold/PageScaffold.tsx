import { Head } from '@inertiajs/react';
import type { ReactNode } from 'react';
import './PageScaffold.css';

interface PageScaffoldProps {
  title: string;
  children?: ReactNode;
}

export default function PageScaffold({ title, children }: PageScaffoldProps): React.JSX.Element {
  return (
    <>
      <Head title={title} />
      <main className="pageScaffold">
        <h1>{title}</h1>
        {children}
      </main>
    </>
  );
}
