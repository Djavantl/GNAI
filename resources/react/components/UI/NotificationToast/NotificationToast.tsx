import { useEffect } from 'react';
import Button from '@/components/UI/Button/Button';
import { joinClassNames } from '@/utils/joinClassNames';
import './NotificationToast.css';

export type NotificationToastTone = 'custom' | 'info' | 'success' | 'warning' | 'error' | 'black' | 'white';
export type NotificationToastPosition =
  | 'top-left'
  | 'top-right'
  | 'top-center'
  | 'bottom-left'
  | 'bottom-right'
  | 'bottom-center'
  | 'left-middle'
  | 'right-middle';

export interface NotificationToastItem {
  id: number;
  tone: NotificationToastTone;
  position: NotificationToastPosition;
  title: string;
  text?: string;
  confirm?: boolean;
  autoHideDelay?: number;
  clickToHide?: boolean;
}

interface NotificationToastProps {
  notification: NotificationToastItem;
  onClose: (id: number) => void;
}

const iconByTone: Record<NotificationToastTone, string> = {
  custom: 'md md-album',
  info: 'fa fa-question',
  success: 'fa fa-check',
  warning: 'fa fa-warning',
  error: 'fa fa-exclamation',
  black: 'fa fa-adjust',
  white: 'fa fa-adjust'
};

export default function NotificationToast({ notification, onClose }: NotificationToastProps): React.JSX.Element {
  useEffect(() => {
    if (!notification.autoHideDelay) {
      return undefined;
    }

    const timer = window.setTimeout(() => {
      onClose(notification.id);
    }, notification.autoHideDelay);

    return () => {
      window.clearTimeout(timer);
    };
  }, [notification.autoHideDelay, notification.id, onClose]);

  function handleClick(): void {
    if (notification.clickToHide !== false) {
      onClose(notification.id);
    }
  }

  function handleConfirmYes(event: React.MouseEvent<HTMLButtonElement>): void {
    event.stopPropagation();
    window.alert('Sim clicado!');
    onClose(notification.id);
  }

  function handleConfirmNo(event: React.MouseEvent<HTMLButtonElement>): void {
    event.stopPropagation();
    onClose(notification.id);
  }

  return (
    <div
      className={joinClassNames(
        'notificationToast',
        `notificationToast--${notification.tone}`
      )}
      role={notification.confirm ? 'alertdialog' : 'alert'}
      onClick={handleClick}
    >
      <div className="notificationToast__image">
        <i className={iconByTone[notification.tone]} aria-hidden="true" />
      </div>

      <div className="notificationToast__textWrapper">
        <div className="notificationToast__title">{notification.title}</div>
        {notification.confirm ? (
          <div className="notificationToast__text">
            Tem certeza de que deseja não fazer nada?
            <div className="notificationToast__clearfix" />
            <br />
            <Button tone="white" size="sm" className="notificationToast__confirmButton" onClick={handleConfirmYes}>
              Sim
            </Button>
            <Button tone="danger" size="sm" className="notificationToast__confirmButton" onClick={handleConfirmNo}>
              Não
            </Button>
          </div>
        ) : (
          <>
            <div className="notificationToast__text">{notification.text}</div>
            <button type="button" className="notificationToast__dismiss" aria-label="Fechar notificação"
              onClick={(event) => { event.stopPropagation(); onClose(notification.id); }}>Fechar</button>
          </>
        )}
      </div>
    </div>
  );
}
