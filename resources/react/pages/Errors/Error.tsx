import { Head } from '@inertiajs/react';
import AccountLayout from '@/components/Navigation/AccountLayout/AccountLayout';
import ErrorState from '@/components/UI/ErrorState/ErrorState';
import type { ErrorCode } from '@/components/UI/ErrorState/ErrorState.types';

const supportedStatuses: ErrorCode[] = ['400', '403', '404', '419', '500', '503'];

export default function Error({ status, homeHref }: { status: number; homeHref: string }): React.JSX.Element {
  const code: ErrorCode = supportedStatuses.includes(String(status) as ErrorCode)
    ? String(status) as ErrorCode
    : '500';

  return (
    <>
      <Head title={`${status} — Erro | Gnai`} />
      <AccountLayout>
        <ErrorState code={code} homeHref={homeHref} homeLabel="Voltar" />
      </AccountLayout>
    </>
  );
}
