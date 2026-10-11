import { useId, type FormEventHandler } from 'react';
import Button from '@/components/UI/Button/Button';
import TextInput from '@/components/Forms/TextInput/TextInput';
import ChoiceControl from '@/components/Forms/ChoiceControl/ChoiceControl';
import SocialLoginButtons from '@/components/UI/SocialLoginButtons/SocialLoginButtons';
import { credentialsCopy } from './CredentialsForm.data';
import type { CredentialsCopy, CredentialsMode } from './CredentialsForm.types';
import './CredentialsForm.css';

export default function CredentialsForm({ mode, compact = false, social = false,
  action = 'index.html', copy = credentialsCopy[mode], onSubmit }: {
  mode: CredentialsMode;
  compact?: boolean;
  social?: boolean;
  action?: string;
  copy?: CredentialsCopy;
  onSubmit?: FormEventHandler<HTMLFormElement>;
}): React.JSX.Element {
  const id = useId();
  return <form className={`credentialsForm credentialsForm--${mode}${compact ? ' credentialsForm--compact' : ''}`}
    action={action} onSubmit={onSubmit}>
    {mode === 'signup' ? <div className="credentialsForm__group">
      <TextInput type="email" required placeholder={copy.email} aria-label={copy.email} />
    </div> : null}
    <div className="credentialsForm__group">
      <TextInput required placeholder={copy.username} aria-label={copy.username} />
    </div>
    <div className="credentialsForm__group">
      <TextInput type="password" required placeholder={copy.password} aria-label={copy.password} />
    </div>
    <div className="credentialsForm__group">
      <ChoiceControl type="checkbox" id={id} tone="primary" checked={mode === 'signup'}
        label={mode === 'signin' ? copy.remember : <>{copy.termsLead} <a href="#">{copy.terms}</a></>} />
    </div>
    <div className="credentialsForm__submit">
      <Button type="submit" tone="pink" block={!compact} width={compact ? 'sm' : undefined}
        label={compact ? copy.compactSubmit : copy.submit} className="credentialsForm__submitButton" />
    </div>
    {mode === 'signin' ? <div className={`credentialsForm__forgot${social ? ' credentialsForm__forgot--social' : ''}`}>
      <a href="page-recoverpw.html">{!compact ? <><i className="fa fa-lock" />{' '}</> : null}{copy.forgot}</a>
    </div> : null}
    {social ? <SocialLoginButtons title={copy.socialTitle} /> : null}
  </form>;
}
