import {
  useCallback,
  useEffect,
  useRef,
  useState,
  type CSSProperties,
  type FocusEvent,
  type MouseEvent,
} from 'react';
import Button from '@/components/UI/Button/Button';
import { joinClassNames } from '@/utils/joinClassNames';
import './NotificationToast.css';

export type NotificationToastTone =
  | 'custom'
  | 'info'
  | 'success'
  | 'warning'
  | 'error'
  | 'black'
  | 'white';
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

const exitDuration = 320;

export default function NotificationToast({
  notification,
  onClose,
}: NotificationToastProps): React.JSX.Element {
  const [paused, setPaused] = useState(false);
  const [closing, setClosing] = useState(false);
  const pausedRef = useRef(false);
  const hoveredRef = useRef(false);
  const focusWithinRef = useRef(false);
  const closingRef = useRef(false);
  const timer = useRef<number | undefined>(undefined);
  const exitTimer = useRef<number | undefined>(undefined);
  const startedAt = useRef(0);
  const remaining = useRef(notification.autoHideDelay ?? 0);

  const clearAutoHideTimer = useCallback(() => {
    if (timer.current !== undefined) {
      window.clearTimeout(timer.current);
      timer.current = undefined;
    }
  }, []);

  const beginExit = useCallback(() => {
    if (closingRef.current) return;

    closingRef.current = true;
    clearAutoHideTimer();
    setClosing(true);
    exitTimer.current = window.setTimeout(
      () => onClose(notification.id),
      exitDuration,
    );
  }, [clearAutoHideTimer, notification.id, onClose]);

  const scheduleAutoHide = useCallback(() => {
    if (!notification.autoHideDelay || closingRef.current) return;

    startedAt.current = performance.now();
    timer.current = window.setTimeout(beginExit, remaining.current);
  }, [beginExit, notification.autoHideDelay]);

  useEffect(() => {
    if (!notification.autoHideDelay) {
      return undefined;
    }

    remaining.current = notification.autoHideDelay;
    scheduleAutoHide();

    return () => {
      clearAutoHideTimer();
      if (exitTimer.current !== undefined) {
        window.clearTimeout(exitTimer.current);
      }
    };
  }, [clearAutoHideTimer, notification.autoHideDelay, scheduleAutoHide]);

  function pauseTimer(): void {
    if (
      !notification.autoHideDelay
      || closingRef.current
      || pausedRef.current
    ) return;

    clearAutoHideTimer();
    remaining.current = Math.max(
      0,
      remaining.current - (performance.now() - startedAt.current),
    );
    pausedRef.current = true;
    setPaused(true);
  }

  function resumeTimer(): void {
    if (
      !notification.autoHideDelay
      || closingRef.current
      || !pausedRef.current
    ) return;

    pausedRef.current = false;
    setPaused(false);
    scheduleAutoHide();
  }

  function handleMouseEnter(): void {
    hoveredRef.current = true;
    pauseTimer();
  }

  function handleMouseLeave(): void {
    hoveredRef.current = false;
    if (!focusWithinRef.current) resumeTimer();
  }

  function handleFocus(): void {
    focusWithinRef.current = true;
    pauseTimer();
  }

  function handleBlur(event: FocusEvent<HTMLDivElement>): void {
    if (event.currentTarget.contains(event.relatedTarget)) return;

    focusWithinRef.current = false;
    if (!hoveredRef.current) resumeTimer();
  }

  function handleClick(): void {
    if (notification.clickToHide !== false) {
      beginExit();
    }
  }

  function handleConfirmYes(event: MouseEvent<HTMLButtonElement>): void {
    event.stopPropagation();
    window.alert('Sim clicado!');
    beginExit();
  }

  function handleConfirmNo(event: MouseEvent<HTMLButtonElement>): void {
    event.stopPropagation();
    beginExit();
  }

  const style = notification.autoHideDelay
    ? ({
        '--notification-toast-duration': `${notification.autoHideDelay}ms`,
      } as CSSProperties)
    : undefined;

  return (
    <div
      className={joinClassNames(
        'notificationToast',
        `notificationToast--${notification.tone}`,
        paused ? 'notificationToast--paused' : undefined,
        closing ? 'notificationToast--closing' : undefined,
        notification.clickToHide !== false
          ? 'notificationToast--clickable'
          : undefined,
      )}
      role={notification.confirm ? 'alertdialog' : 'alert'}
      style={style}
      onClick={handleClick}
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      onFocusCapture={handleFocus}
      onBlurCapture={handleBlur}
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
            <Button
              tone="white"
              size="sm"
              className="notificationToast__confirmButton"
              onClick={handleConfirmYes}
            >
              Sim
            </Button>
            <Button
              tone="danger"
              size="sm"
              className="notificationToast__confirmButton"
              onClick={handleConfirmNo}
            >
              Não
            </Button>
          </div>
        ) : (
          <>
            <div className="notificationToast__text">{notification.text}</div>
            <button
              type="button"
              className="notificationToast__dismiss"
              aria-label="Fechar notificação"
              title="Fechar notificação"
              onClick={(event) => {
                event.stopPropagation();
                beginExit();
              }}
            >
              ×
            </button>
          </>
        )}
      </div>
      {notification.autoHideDelay ? (
        <div className="notificationToast__progress" aria-hidden="true">
          <div className="notificationToast__progressBar" />
        </div>
      ) : null}
    </div>
  );
}
