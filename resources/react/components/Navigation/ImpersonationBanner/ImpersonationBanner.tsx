import { Link } from '@inertiajs/react';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';

export default function ImpersonationBanner({ name }: { name: string }): React.JSX.Element {
  return <InlineAlert variant="warning">
    Você está navegando como <strong>{name}</strong>.{' '}
    <Link href="/auth/impersonate/leave" method="post" as="button">Voltar para Admin</Link>
  </InlineAlert>;
}
