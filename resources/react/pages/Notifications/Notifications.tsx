import { useCallback, useRef, useState } from 'react';
import NotificationToastLayer from '@/components/UI/NotificationToast/NotificationToastLayer';
import type { NotificationToastItem } from '@/components/UI/NotificationToast/NotificationToast';
import { sampleNotificationText, type NotificationButtonItem } from './Notifications.data';
import { NotificationCards, NotificationsPageTitle } from './Notifications.sections';
import './Notifications.css';

export default function Notifications(): React.JSX.Element {
  const [notifications, setNotifications] = useState<NotificationToastItem[]>([]);
  const nextIdRef = useRef<number>(1);

  const closeNotification = useCallback((id: number): void => {
    setNotifications((currentNotifications: NotificationToastItem[]) => currentNotifications.filter((notification: NotificationToastItem) => notification.id !== id));
  }, []);

  function showNotification(button: NotificationButtonItem): void {
    const id = nextIdRef.current;
    nextIdRef.current += 1;

    setNotifications((currentNotifications: NotificationToastItem[]) => [
      ...currentNotifications,
      {
        id,
        tone: button.toastTone,
        position: button.position,
        title: button.title ?? 'Notificação de exemplo',
        text: sampleNotificationText,
        confirm: button.confirm,
        autoHideDelay: button.confirm ? undefined : button.autoHideDelay ?? 5000,
        clickToHide: button.confirm ? false : true
      }
    ]);
  }

  return (
    <section className="notificationsPage" aria-label="Notificações">
      <NotificationsPageTitle />
      <NotificationCards onNotify={showNotification} />
      <NotificationToastLayer notifications={notifications} onClose={closeNotification} />
    </section>
  );
}
