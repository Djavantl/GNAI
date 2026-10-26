import { Link } from '@inertiajs/react';
import './AuthFooter.css';

export default function AuthFooter({ backToLogin = false }: { backToLogin?: boolean }): React.JSX.Element {
  return <footer className="authFooter">
    {backToLogin ? <p className="authFooter__back"><Link href="/auth/login"><i className="fa fa-arrow-left" aria-hidden="true" /> Voltar para login</Link></p> : null}
    <p className="authFooter__copyright">Gnai &copy; {new Date().getFullYear()} | Gestão NAIs</p>
  </footer>;
}
