import type { ReactNode } from 'react';
import { Head } from '@inertiajs/react';
import AccountLayout from '@/components/Navigation/AccountLayout/AccountLayout';
import AccountCard from '@/components/Forms/AccountCard/AccountCard';
import AuthFooter from '@/components/Navigation/AuthFooter/AuthFooter';

export default function AuthLayout({ title, heading, children, backToLogin = false }: {
  title: string;
  heading: ReactNode;
  children: ReactNode;
  backToLogin?: boolean;
}): React.JSX.Element {
  return <AccountLayout>
    <Head title={`${title} - Gnai`} />
    <AccountCard title={heading}>{children}</AccountCard>
    <AuthFooter backToLogin={backToLogin} />
  </AccountLayout>;
}
