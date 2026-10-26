import type { ReactNode } from 'react';
import { Head } from '@inertiajs/react';
import AccountLayout from '@/components/Navigation/AccountLayout/AccountLayout';
import AccountCard from '@/components/Forms/AccountCard/AccountCard';
import AuthFooter from '@/components/Navigation/AuthFooter/AuthFooter';
import './AuthLayout.css';

function LoginIllustration(): React.JSX.Element {
  return <div className="authLayout__welcome">
    <svg className="authLayout__pattern" viewBox="0 0 320 320" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
      <defs>
        <pattern id="gnai-geometric-pattern" width="76" height="76" patternUnits="userSpaceOnUse">
          <g fill="none" stroke="#e5e0ff" strokeOpacity=".2" strokeWidth="2.5">
            <path d="M3 3h34v34H3zM10 10h20v20H10zM17 17h6v6h-6z" />
            <circle cx="57" cy="19" r="16" /><circle cx="57" cy="19" r="10" /><circle cx="57" cy="19" r="4" />
            <path d="m39 57 18-18 18 18-18 18z m9 0 9-9 9 9-9 9z" />
            <path d="M3 43h25m-25 8h18m-18 8h25" />
            <path d="m38 34 5-8 5 8zM31 70h10m-5-5v10M67 3v8m-5-4h10" />
          </g>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#gnai-geometric-pattern)" />
    </svg>
    <div className="authLayout__welcomeCopy">
      <h1>BEM-VINDO</h1>
      <p>Um espaço para fortalecer o cuidado, a acessibilidade e o suporte educacional.</p>
    </div>
  </div>;
}

export default function AuthLayout({ title, heading, children, backToLogin = false, split = false }: {
  title: string;
  heading: ReactNode;
  children: ReactNode;
  backToLogin?: boolean;
  split?: boolean;
}): React.JSX.Element {
  if (split) {
    return <>
      <Head title={`${title} - Gnai`} />
      <AccountLayout illustration={<LoginIllustration />}>
        <div className={`authLayout__formInner${backToLogin ? ' authLayout__formInner--recovery' : ''}`}>
          <div className="authLayout__brandMark">
            <span><img src="/images/logo.png" alt="" /></span>
            <strong>Gnai</strong>
          </div>
          <AccountCard title={heading}>
            {children}
          </AccountCard>
          <AuthFooter backToLogin={backToLogin} />
        </div>
      </AccountLayout>
    </>;
  }
  return <AccountLayout>
    <Head title={`${title} - Gnai`} />
    <AccountCard title={heading}>{children}</AccountCard>
    <AuthFooter backToLogin={backToLogin} />
  </AccountLayout>;
}
