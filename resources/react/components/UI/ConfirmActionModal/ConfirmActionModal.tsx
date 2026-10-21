import { useEffect, useRef, type ReactNode } from 'react';
import Button, {
  type ButtonTone,
} from '@/components/UI/Button/Button';
import InlineAlert from '@/components/UI/InlineAlert/InlineAlert';
import Modal, {
  useModalClose,
} from '@/components/UI/Modal/Modal';
import './ConfirmActionModal.css';

interface ConfirmActionModalProps {
  open: boolean;
  title: string;
  message: ReactNode;
  details?: ReactNode;
  error?: ReactNode;
  processing?: boolean;
  confirmLabel?: string;
  processingLabel?: string;
  cancelLabel?: string;
  confirmTone?: ButtonTone;
  onClose: () => void;
  onConfirm: () => void;
}

interface ConfirmActionModalFooterProps {
  processing: boolean;
  confirmLabel: string;
  processingLabel: string;
  cancelLabel: string;
  confirmTone: ButtonTone;
  onClose: () => void;
  onConfirm: () => void;
}

function ConfirmActionModalFooter({
  processing,
  confirmLabel,
  processingLabel,
  cancelLabel,
  confirmTone,
  onClose,
  onConfirm,
}: ConfirmActionModalFooterProps): React.JSX.Element {
  const closeModal = useModalClose();

  return (
    <>
      <Button
        className="confirmActionModal__cancel"
        tone="cancel"
        iconOnly
        leftIconClass="fa fa-times"
        title={cancelLabel}
        aria-label={cancelLabel}
        disabled={processing}
        onClick={closeModal ?? onClose}
      />
      <Button
        tone={confirmTone}
        iconOnly
        leftIconClass={
          processing ? 'fa fa-spinner fa-spin' : 'fa fa-check'
        }
        title={processing ? processingLabel : confirmLabel}
        aria-label={processing ? processingLabel : confirmLabel}
        disabled={processing}
        onClick={onConfirm}
      />
    </>
  );
}

export default function ConfirmActionModal({
  open,
  title,
  message,
  details,
  error,
  processing = false,
  confirmLabel = 'Confirmar',
  processingLabel = 'Processando...',
  cancelLabel = 'Cancelar',
  confirmTone = 'danger',
  onClose,
  onConfirm,
}: ConfirmActionModalProps): React.JSX.Element {
  const content = useRef<HTMLDivElement | null>(null);
  const returnFocus = useRef<HTMLElement | null>(null);

  useEffect(() => {
    if (!open) {
      return;
    }

    returnFocus.current = document.activeElement as HTMLElement | null;
    const frame = requestAnimationFrame(() => {
      content.current
        ?.closest('[role="dialog"]')
        ?.querySelector<HTMLButtonElement>('.confirmActionModal__cancel')
        ?.focus();
    });

    function trapFocus(event: KeyboardEvent): void {
      if (event.key !== 'Tab') {
        return;
      }

      const controls = content.current
        ?.closest('[role="dialog"]')
        ?.querySelectorAll<HTMLElement>(
          'button:not(:disabled), a[href], input:not(:disabled), select:not(:disabled), textarea:not(:disabled)',
        );

      if (!controls?.length) {
        return;
      }

      const first = controls[0];
      const last = controls[controls.length - 1];

      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      }

      if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }

    document.addEventListener('keydown', trapFocus);

    return () => {
      cancelAnimationFrame(frame);
      document.removeEventListener('keydown', trapFocus);
      returnFocus.current?.focus();
    };
  }, [open]);

  return (
    <Modal
      open={open}
      title={title}
      effect="fadein"
      closeOnBackdrop={!processing}
      onClose={onClose}
      layerClassName="confirmActionModalLayer"
      contentClassName="confirmActionModal"
      bodyClassName="confirmActionModal__body"
      footer={
        <ConfirmActionModalFooter
          processing={processing}
          confirmLabel={confirmLabel}
          processingLabel={processingLabel}
          cancelLabel={cancelLabel}
          confirmTone={confirmTone}
          onClose={onClose}
          onConfirm={onConfirm}
        />
      }
    >
      <div ref={content}>
        <p className="confirmActionModal__message">
          {message}
        </p>
        {details ? (
          <div className="confirmActionModal__details">
            {details}
          </div>
        ) : null}
        {error ? (
          <div role="alert" className="confirmActionModal__error">
            <InlineAlert variant="danger">
              {error}
            </InlineAlert>
          </div>
        ) : null}
        {processing ? (
          <p className="confirmActionModal__status" role="status">
            {processingLabel}
          </p>
        ) : null}
      </div>
    </Modal>
  );
}
