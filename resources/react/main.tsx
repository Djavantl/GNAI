import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import AppLayout from '@/components/Navigation/AppLayout/AppLayout';
import type { ComponentType, ReactNode } from 'react';
import './styles/reset.css';
import './styles/tokens.css';
import './styles/globals.css';

type PageComponent = ComponentType & { layout?: (page: ReactNode) => ReactNode };

const pages = import.meta.glob<{ default: PageComponent }>('./pages/**/*.tsx');

void createInertiaApp({
  id: 'root',
  resolve: async (name) => {
    const load = pages[`./pages/${name}.tsx`];
    if (!load) throw new Error(`Página React não encontrada: ${name}`);
    const page = (await load()).default;
    if (!name.startsWith('Catalog/') && !['Auth/Login', 'Auth/ForgotPassword', 'Auth/ResetPassword'].includes(name)) {
      page.layout ??= (children) => <AppLayout>{children}</AppLayout>;
    }
    return page;
  },
  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />);
  },
});
