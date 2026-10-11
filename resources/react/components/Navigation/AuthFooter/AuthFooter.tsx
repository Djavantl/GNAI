import { Link } from '@inertiajs/react';
import './AuthFooter.css';

export default function AuthFooter({ backToLogin = false }: { backToLogin?: boolean }): React.JSX.Element {
  return <footer className="authFooter">
    {backToLogin ? <p><Link href="/auth/login"><b>Voltar para login</b></Link></p> : null}
    <p>Gnai &copy; {new Date().getFullYear()} | Gestão NAIs</p>
  </footer>;
}
