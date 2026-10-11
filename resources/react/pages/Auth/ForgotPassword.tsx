import AuthLayout from '@/components/Navigation/AuthLayout/AuthLayout';
import ForgotPasswordForm from '@/components/Forms/ForgotPasswordForm/ForgotPasswordForm';

export default function AuthForgotPasswordPage(): React.JSX.Element {
  return <AuthLayout title="Recuperar senha" heading="Recuperar senha" backToLogin>
    <ForgotPasswordForm />
  </AuthLayout>;
}
