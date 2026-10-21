import { useRef, useState } from 'react';
import { useForm } from '@inertiajs/react';
import Button from '@/components/UI/Button/Button';
import ConfirmActionModal from '@/components/UI/ConfirmActionModal/ConfirmActionModal';
import { studentUrl } from './Students.utils';
import { useStudentRequest } from './useStudentRequest';

export default function DeleteStudent({
  id,
  name,
  iconOnly = false,
}: {
  id: number;
  name: string;
  iconOnly?: boolean;
}): React.JSX.Element {
  const [open, setOpen] = useState(false);
  const form = useForm({});
  const busy = useRef(false);
  const request = useStudentRequest();
  const deleteLabel = `Excluir aluno ${name}`;

  function close(): void {
    setOpen(false);
  }

  function remove(): void {
    if (busy.current || form.processing) {
      return;
    }

    busy.current = true;
    form.delete(studentUrl(id, ''), {
      ...request.options,
      preserveScroll: true,
      onFinish: () => {
        busy.current = false;
      },
    });
  }

  return (
    <>
      <Button
        tone="dark-red"
        size={iconOnly ? 'sm' : undefined}
        leftIconClass="fa fa-trash-o"
        iconOnly={iconOnly}
        disabled={form.processing}
        title={deleteLabel}
        aria-label={deleteLabel}
        onClick={() => setOpen(true)}
      >
        {iconOnly ? null : 'Excluir'}
      </Button>
      <ConfirmActionModal
        open={open}
        title="Excluir Aluno"
        message={
          <>
            Excluir o aluno <strong>{name}</strong>?
          </>
        }
        processing={form.processing}
        confirmLabel="Confirmar exclusão"
        processingLabel="Excluindo aluno..."
        confirmTone="dark-red"
        onClose={close}
        onConfirm={remove}
      />
    </>
  );
}
