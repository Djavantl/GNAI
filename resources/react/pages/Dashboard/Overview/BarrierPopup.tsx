import { router } from '@inertiajs/react';
import Button from '@/components/UI/Button/Button';
import Label from '@/components/UI/Label/Label';
import type { Barrier } from './Overview.types';
import './BarrierPopup.css';

export default function BarrierPopup({ barrier }: { barrier: Barrier }): React.JSX.Element {
  const tones = { secondary: 'default', info: 'info', warning: 'warning', success: 'success', danger: 'danger' } as const;
  const tone = tones[barrier.color as keyof typeof tones] ?? 'default';
  return <section aria-label={`Barreira: ${barrier.name}`}>
    <h3 className="barrierPopup__title">{barrier.name}</h3>
    <div className="barrierPopup__content">
      <p className="barrierPopup__category">{barrier.category_name}</p>
      <Label variant={tone}>{barrier.status_label}</Label>
      <div className="barrierPopup__actions">
        <Button href={barrier.url} size="sm" tone="default" onClick={event => {
          if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
          if (new URL(event.currentTarget.href).origin !== window.location.origin) return;
          event.preventDefault();
          router.visit(barrier.url);
        }}>Ver detalhes</Button>
      </div>
    </div>
  </section>;
}
