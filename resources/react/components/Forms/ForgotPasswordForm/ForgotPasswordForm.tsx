import { useForm } from '@inertiajs/react';
import InputAction from '@/components/Forms/InputAction/InputAction';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import './ForgotPasswordForm.css';

export default function ForgotPasswordForm(): React.JSX.Element {
  const form = useForm({ email: '' });
  return <form className="forgotPasswordForm" noValidate aria-label="Recuperar acesso ao Gnai" aria-busy={form.processing}
    onSubmit={(event) => {
      event.preventDefault();
      if (!form.processing) form.post('/auth/forgot-password');
    }}>
    <InlineAlert variant="info">
      Digite seu <b>e-mail institucional</b> para receber as instruções de recuperação de acesso ao Gnai.
    </InlineAlert>
    <InputAction type="email" name="email" required autoFocus autoComplete="email"
      placeholder="Digite seu e-mail" aria-label="E-mail institucional"
      value={form.data.email} onChange={(event) => form.setData('email', event.target.value)}
      invalid={!!form.errors.email} aria-describedby={form.errors.email ? 'recovery-email-error' : undefined}
      buttonDisabled={form.processing} label={form.processing ? 'Enviando...' : 'Enviar link'} />
    {form.errors.email ? <p id="recovery-email-error" className="forgotPasswordForm__error" role="alert">{form.errors.email}</p> : null}
  </form>;
}
