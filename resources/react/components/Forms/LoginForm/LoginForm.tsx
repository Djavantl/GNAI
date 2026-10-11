import { Link, useForm } from '@inertiajs/react';
import TextInput from '@/components/Forms/TextInput/TextInput';
import Button from '@/components/UI/Button/Button';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import AuthFeedback from '@/components/UI/AuthFeedback/AuthFeedback';
import './LoginForm.css';

export default function LoginForm(): React.JSX.Element {
  const form = useForm({ email: '', password: '' });
  return <form className="loginForm" aria-label="Acesse sua conta no Gnai" aria-busy={form.processing}
    onSubmit={(event) => {
      event.preventDefault();
      if (!form.processing) form.post('/auth/login', { onFinish: () => form.reset('password') });
    }}>
    <AuthFeedback />
    <div className="loginForm__group">
      <TextInput type="email" name="email" required autoFocus autoComplete="username"
        placeholder="E-mail institucional" aria-label="E-mail institucional"
        value={form.data.email} onChange={(event) => form.setData('email', event.target.value)}
        invalid={!!form.errors.email} aria-describedby={form.errors.email ? 'login-email-error' : undefined} />
      {form.errors.email ? <div id="login-email-error" role="alert"><InlineAlert variant="danger">{form.errors.email}</InlineAlert></div> : null}
    </div>
    <div className="loginForm__group">
      <TextInput type="password" name="password" required autoComplete="current-password"
        placeholder="Senha" aria-label="Senha"
        value={form.data.password} onChange={(event) => form.setData('password', event.target.value)}
        invalid={!!form.errors.password} aria-describedby={form.errors.password ? 'login-password-error' : undefined} />
      {form.errors.password ? <div id="login-password-error" role="alert"><InlineAlert variant="danger">{form.errors.password}</InlineAlert></div> : null}
    </div>
    <div className="loginForm__submit">
      <Button type="submit" tone="pink" block disabled={form.processing}
        label={form.processing ? 'Entrando...' : 'Entrar'} className="loginForm__submitButton" />
    </div>
    <div className="loginForm__forgot">
      <Link href="/auth/forgot-password"><i className="fa fa-lock" aria-hidden="true" /> Esqueceu sua senha?</Link>
    </div>
  </form>;
}
