import { usePage } from '@inertiajs/react';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';

type AuthFlash = { error?: string | null; status?: string | null; success?: string | null };

export default function AuthFeedback(): React.JSX.Element {
  const { flash } = usePage<{ flash?: AuthFlash }>().props;
  return <div aria-live="polite" aria-atomic="true">
    {flash?.error ? <InlineAlert variant="danger">{flash.error}</InlineAlert> : null}
    {flash?.status || flash?.success ? <InlineAlert variant="success">{flash.status || flash.success}</InlineAlert> : null}
  </div>;
}
