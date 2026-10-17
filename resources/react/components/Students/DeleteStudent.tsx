import { useRef, useState } from 'react';
import { useForm } from '@inertiajs/react';
import Button from '@/components/UI/Button/Button';
import ConfirmActionModal from '@/components/UI/ConfirmActionModal/ConfirmActionModal';
import { studentUrl } from './Students.utils';
import { useStudentRequest } from './useStudentRequest';

export default function DeleteStudent({
  id,
  name,
}: {
  id: number;
  name: string;
}): React.JSX.Element {
  const [open, setOpen] = useState(false);
  const form = useForm({});
  const busy = useRef(false);
  const request = useStudentRequest();

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
        onClick={() => setOpen(true)}
      >
        Excluir aluno
      </Button>
      <ConfirmActionModal
        open={open}
        title="Excluir Aluno"
        message={
          <>
            Excluir o aluno <strong>{name}</strong>?
          </>
        }
        error={request.error}
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
