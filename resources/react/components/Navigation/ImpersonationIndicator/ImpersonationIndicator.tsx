import { Link } from '@inertiajs/react';
import './ImpersonationIndicator.css';

/** Aviso compacto da top bar enquanto o administrador superior navega como outro usuário. */
export default function ImpersonationIndicator({
  name,
  readOnly,
}: {
  name: string;
  readOnly: boolean;
}): React.JSX.Element {
  const modeLabel = readOnly ? 'Somente leitura' : 'Acesso completo';

  return (
    <li className="topbar__navItem impersonationIndicator">
      <p
        className="impersonationIndicator__text"
        title={`Navegando como ${name} (${modeLabel.toLowerCase()})`}
      >
        <i className="fa fa-user-secret" aria-hidden="true" />
        <span className="impersonationIndicator__label">
          Navegando como <strong>{name}</strong>
        </span>
        <span className="impersonationIndicator__mode">{modeLabel}</span>
      </p>
      <Link
        href="/auth/impersonate/leave"
        method="post"
        as="button"
        className="topbar__navLink impersonationIndicator__leave"
        title="Voltar para a minha conta"
        aria-label="Voltar para a minha conta"
      >
        <i className="ti-back-left" aria-hidden="true" />
      </Link>
    </li>
  );
}
