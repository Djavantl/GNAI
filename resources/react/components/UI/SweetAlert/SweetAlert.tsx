import { useEffect, useLayoutEffect, useRef, useState } from 'react';
import type { SweetAlertState } from './SweetAlert.types';
import './SweetAlert.css';

interface SweetAlertModalProps {
  alert: SweetAlertState;
  onClose: () => void;
}

const sweetAlertCloseAnimationMs = 220;

function SweetAlertIcon({ alert }: { alert: SweetAlertState }): React.JSX.Element | null {
  if (alert.type === 'success') {
    return (
      <div className="sa-icon sa-success animate" style={{ display: 'block' }}>
        <span className="sa-line sa-tip animateSuccessTip" />
        <span className="sa-line sa-long animateSuccessLong" />
        <div className="sa-placeholder" />
        <div className="sa-fix" />
      </div>
    );
  }

  if (alert.type === 'error') {
    return (
      <div className="sa-icon sa-error animateErrorIcon" style={{ display: 'block' }}>
        <span className="sa-x-mark animateXMark">
          <span className="sa-line sa-left" />
          <span className="sa-line sa-right" />
        </span>
      </div>
    );
  }

  if (alert.type === 'warning') {
    return (
      <div className="sa-icon sa-warning pulseWarning" style={{ display: 'block' }}>
        <span className="sa-body pulseWarningIns" />
        <span className="sa-dot pulseWarningIns" />
      </div>
    );
  }

  if (alert.type === 'custom' && alert.imageUrl) {
    return <div className="sa-icon sa-custom" style={{ display: 'block', backgroundImage: `url(${alert.imageUrl})` }} />;
  }

  return null;
}

export default function SweetAlert({ alert, onClose }: SweetAlertModalProps): React.JSX.Element {
  const [isClosing, setIsClosing] = useState<boolean>(false);
  const [modalTopMargin, setModalTopMargin] = useState<number>(-200);
  const modalRef = useRef<HTMLDivElement | null>(null);
  const hasCancelButton = alert.showCancelButton === true;
  const hasConfirmButton = alert.showConfirmButton !== false;


  useLayoutEffect(() => {
    const modal = modalRef.current;

    if (!modal) {
      return;
    }

    const previousLeft = modal.style.left;
    const previousDisplay = modal.style.display;
    modal.style.left = '-9999px';
    modal.style.display = 'block';
    const computedStyle = window.getComputedStyle(modal);
    const paddingTop = Number.parseInt(computedStyle.getPropertyValue('padding-top'), 10) || 0;
    const computedTopMargin = `-${Math.round((modal.clientHeight + paddingTop) / 2)}px`;
    setModalTopMargin(Number.parseInt(computedTopMargin, 10));
    modal.style.left = previousLeft;
    modal.style.display = previousDisplay;
  }, [alert]);

  useEffect(() => {
    document.documentElement.classList.add('stop-scrolling');
    document.body.classList.add('stop-scrolling');

    return () => {
      document.documentElement.classList.remove('stop-scrolling');
      document.body.classList.remove('stop-scrolling');
    };
  }, []);

  useEffect(() => {
    if (!alert.timer) {
      return;
    }

    const timer = window.setTimeout(() => {
      closeWithAnimation();
    }, alert.timer);

    return () => window.clearTimeout(timer);
  }, [alert.timer]);

  function closeWithAnimation(): void {
    setIsClosing(true);
    window.setTimeout(onClose, sweetAlertCloseAnimationMs);
  }

  function handleConfirm(): void {
    alert.onConfirm?.();

    if (alert.closeOnConfirm !== false) {
      closeWithAnimation();
    }
  }

  function handleCancel(): void {
    alert.onCancel?.();

    if (alert.closeOnCancel !== false) {
      closeWithAnimation();
    }
  }

  return (
    <>
      <div className={`sweet-overlay ${isClosing ? 'sweet-overlay--closing' : ''}`} style={{ display: 'block' }} />
      <div
        ref={modalRef}
        className={`sweet-alert ${isClosing ? 'hideSweetAlert' : 'showSweetAlert'}`}
        data-animation="pop"
        data-has-cancel-button={hasCancelButton ? 'true' : 'false'}
        data-has-confirm-button={hasConfirmButton ? 'true' : 'false'}
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="components-sweet-alert-title"
        style={{ display: 'block', marginTop: `${modalTopMargin}px` }}
      >
        <SweetAlertIcon alert={alert} />
        <h2 id="components-sweet-alert-title">{alert.title}</h2>
        {alert.text ? <p>{alert.text}</p> : <p />}

        {hasCancelButton ? (
          <button type="button" className="cancel" tabIndex={2} onClick={handleCancel}>
            {alert.cancelButtonText ?? 'Cancel'}
          </button>
        ) : null}

        {hasConfirmButton ? (
          <span className="sa-confirm-button-container">
            <button
              type="button"
              className="confirm"
              tabIndex={1}
              style={alert.confirmButtonColor ? { backgroundColor: alert.confirmButtonColor } : undefined}
              onClick={handleConfirm}
            >
              {alert.confirmButtonText ?? 'OK'}
            </button>
            <div className="la-ball-fall"><div /><div /><div /></div>
          </span>
        ) : null}
      </div>
    </>
  );
}
