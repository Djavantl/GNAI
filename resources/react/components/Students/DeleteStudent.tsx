import { useEffect, useRef, useState } from 'react';
import { useForm } from '@inertiajs/react';
import Button from '@/components/UI/Button/Button';
import Modal from '@/components/UI/Modal/Modal';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import { studentUrl } from './Students.utils';
import { useStudentRequest } from './useStudentRequest';

export default function DeleteStudent({ id, name }: { id: number; name: string }): React.JSX.Element {
  const [open, setOpen] = useState(false);
  const form = useForm({});
  const busy = useRef(false);
  const trigger = useRef<HTMLElement | null>(null);
  const content = useRef<HTMLDivElement | null>(null);
  const request = useStudentRequest();
  useEffect(() => {
    if (!open) return;
    const frame = requestAnimationFrame(() => content.current?.querySelector<HTMLButtonElement>('button')?.focus());
    const trapFocus = (event: KeyboardEvent) => {
      if (event.key !== 'Tab') return;
      const controls = content.current?.closest('[role="dialog"]')?.querySelectorAll<HTMLButtonElement>('button:not(:disabled)');
      if (!controls?.length) return;
      const first = controls[0], last = controls[controls.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
      if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    };
    document.addEventListener('keydown', trapFocus);
    return () => { cancelAnimationFrame(frame); document.removeEventListener('keydown', trapFocus); trigger.current?.focus(); };
  }, [open]);
  return <>
    <Button tone="danger" onClick={() => { trigger.current = document.activeElement as HTMLElement; setOpen(true); }}>Excluir aluno</Button>
    {open ? <Modal open title="Excluir Aluno" onClose={() => { if (!busy.current) setOpen(false); }} layerClassName="studentDeleteDialog" closeOnBackdrop={false}>
      <div ref={content}>
        <p>Excluir este aluno? <strong>{name}</strong></p>
        {request.error ? <div role="alert"><InlineAlert variant="danger">{request.error}</InlineAlert></div> : null}
        <div className="studentsPage__actions">
          <Button disabled={form.processing} onClick={() => setOpen(false)}>Cancelar</Button>
          <Button tone="danger" disabled={form.processing} onClick={() => {
            if (busy.current || form.processing) return;
            busy.current = true;
            form.delete(studentUrl(id, ''), {
              ...request.options, preserveScroll: true,
              onSuccess: page => { if (page.props.flash && (page.props.flash as { error?: string }).error) setOpen(false); },
              onFinish: () => { busy.current = false; },
            });
          }}>{form.processing ? 'Excluindo...' : 'Confirmar Exclusão'}</Button>
        </div>
        <p role="status">{form.processing ? 'Excluindo aluno...' : ''}</p>
      </div>
    </Modal> : null}
  </>;
}
