import { useRef, useState, type ReactNode } from 'react';
import { useForm } from '@inertiajs/react';
import Button from '@/components/UI/Button/Button';
import ConfirmActionModal from '@/components/UI/ConfirmActionModal/ConfirmActionModal';
import { useStudentRequest } from './useStudentRequest';

export default function DeleteStudentRecord({
  action,
  title,
  message,
  ariaLabel,
}: {
  action: string;
  title: string;
  message: ReactNode;
  ariaLabel: string;
}): React.JSX.Element {
  const [open, setOpen] = useState(false);
  const busy = useRef(false);
  const form = useForm({});
  const request = useStudentRequest();

  function remove(): void {
    if (busy.current || form.processing) return;

    busy.current = true;
    form.delete(action, {
      ...request.options,
      preserveScroll: true,
      onSuccess: () => setOpen(false),
      onFinish: () => {
        busy.current = false;
      },
    });
  }

  return (
    <>
      <Button
        type="button"
        tone="dark-red"
        size="sm"
        iconOnly
        leftIconClass="fa fa-trash-o"
        disabled={form.processing}
        title={ariaLabel}
        aria-label={ariaLabel}
        onClick={() => setOpen(true)}
      />
      <ConfirmActionModal
        open={open}
        title={title}
        message={message}
        processing={form.processing}
        confirmLabel="Confirmar exclusão"
        processingLabel="Excluindo registro..."
        confirmTone="dark-red"
        onClose={() => setOpen(false)}
        onConfirm={remove}
      />
    </>
  );
}
