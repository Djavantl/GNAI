import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import InputAction from '@/components/Forms/InputAction/InputAction';
import TextInput from '@/components/Forms/TextInput/TextInput';
import Button from '@/components/UI/Button/Button';
import './LoginForm.css';

export default function LoginForm(): React.JSX.Element {
  const form = useForm({ email: '', password: '' });
  const [passwordVisible, setPasswordVisible] = useState(false);
  return <form className="loginForm" noValidate aria-label="Acesse sua conta no Gnai" aria-busy={form.processing}
    onSubmit={(event) => {
      event.preventDefault();
      if (!form.processing) form.post('/auth/login', {
        onFinish: () => {
          form.reset('password');
          setPasswordVisible(false);
        },
      });
    }}>
    <div className="loginForm__group">
      <TextInput type="email" name="email" required autoFocus autoComplete="username"
        placeholder="E-mail institucional" aria-label="E-mail institucional"
        value={form.data.email} onChange={(event) => form.setData('email', event.target.value)}
        invalid={!!form.errors.email} aria-describedby={form.errors.email ? 'login-email-error' : undefined} />
      {form.errors.email ? <p id="login-email-error" className="loginForm__error" role="alert">{form.errors.email}</p> : null}
    </div>
    <div className="loginForm__group">
      <InputAction id="login-password" type={passwordVisible ? 'text' : 'password'} name="password"
        required autoComplete="current-password" placeholder="Senha" aria-label="Senha"
        value={form.data.password} onChange={(event) => form.setData('password', event.target.value)}
        invalid={!!form.errors.password}
        aria-describedby={form.errors.password ? 'login-password-error' : undefined}
        action={<button className="loginForm__passwordToggle" type="button"
          aria-label={passwordVisible ? 'Ocultar senha' : 'Mostrar senha'}
          aria-controls="login-password" aria-pressed={passwordVisible}
          title={passwordVisible ? 'Ocultar senha' : 'Mostrar senha'}
          disabled={form.processing} onClick={() => setPasswordVisible((visible) => !visible)}>
          <i className={`fa ${passwordVisible ? 'fa-eye-slash' : 'fa-eye'}`} aria-hidden="true" />
        </button>} />
      {form.errors.password ? <p id="login-password-error" className="loginForm__error" role="alert">{form.errors.password}</p> : null}
    </div>
    <div className="loginForm__submit">
      <Button type="submit" tone="save" block disabled={form.processing}
        label={form.processing ? 'Entrando...' : 'Entrar'} showIcon={false}
        className="loginForm__submitButton" />
    </div>
    <div className="loginForm__forgot">
      <Link href="/auth/forgot-password"><i className="fa fa-lock" aria-hidden="true" /> Esqueceu sua senha?</Link>
    </div>
  </form>;
}
