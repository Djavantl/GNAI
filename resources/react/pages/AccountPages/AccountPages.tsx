import AccountLayout from '@/components/Navigation/AccountLayout/AccountLayout';
import AccountCard from '@/components/Forms/AccountCard/AccountCard';
import CredentialsForm from '@/components/Forms/CredentialsForm/CredentialsForm';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import InputAction from '@/components/Forms/InputAction/InputAction';
import ImageDisplay from '@/components/UI/ImageDisplay/ImageDisplay';
import { accountPageCopy as copy } from './AccountPages.data';
import type { AccountPageKind } from './AccountPages.types';
import './AccountPages.css';

export default function AccountPages({ kind }: { kind: AccountPageKind }): React.JSX.Element {
  const signup = kind === 'register' || kind === 'register-v2';
  const social = kind.endsWith('-v2');
  const title = (lead: string): React.JSX.Element => <>{lead} <strong>{copy.brand}</strong></>;
  let content: React.JSX.Element;
  if (kind === 'signup-signin') {
    content = <AccountCard title={title(copy.welcomeTitle)}>
      <div className="accountPages__columns">
        <section className="accountPages__column"><h4><b>{copy.signinHeading}</b></h4><CredentialsForm mode="signin" compact /></section>
        <section className="accountPages__column"><h4><b>{copy.signupHeading}</b></h4><CredentialsForm mode="signup" compact /></section>
      </div>
    </AccountCard>;
  } else if (kind === 'recoverpw') {
    content = <AccountCard title={copy.resetTitle}>
      <form action="#" method="post" className="accountPages__reset">
        <InlineAlert variant="info" dismissible>
          {copy.resetLead} <b>{copy.resetEmail}</b> {copy.resetInstructions}
        </InlineAlert>
        <InputAction type="email" required placeholder={copy.resetPlaceholder} aria-label={copy.resetPlaceholder} label={copy.resetButton} />
      </form>
    </AccountCard>;
  } else if (kind === 'lock-screen') {
    content = <>
      <AccountCard>
        <form action="index.html" method="post" className="accountPages__lock">
          <ImageDisplay src={copy.avatar} alt={`Foto de ${copy.lockUser}`} shape="thumbnail" size="lg" className="accountPages__avatar" />
          <div className="accountPages__lockGroup">
            <h3>{copy.lockUser}</h3><p>{copy.lockInstructions}</p>
            <div className="accountPages__lockInput"><InputAction type="password" required placeholder={copy.lockPlaceholder} aria-label={copy.lockPlaceholder} label={copy.lockButton} /></div>
          </div>
        </form>
      </AccountCard>
      <p className="accountPages__footer">{copy.lockPrompt} <a href="page-login.html"><b>{copy.signinLink}</b></a></p>
    </>;
  } else {
    content = <>
      <AccountCard title={title(signup ? copy.signupTitle : copy.signinTitle)}>
        <CredentialsForm mode={signup ? 'signup' : 'signin'} social={social} />
      </AccountCard>
      <p className="accountPages__footer">{signup ? copy.signinPrompt : copy.signupPrompt}{' '}
        <a href={signup ? 'page-login.html' : 'page-register.html'}><b>{signup ? copy.signinLink : copy.signupLink}</b></a>
      </p>
    </>;
  }
  return <AccountLayout wide={kind === 'signup-signin'}>{content}</AccountLayout>;
}
