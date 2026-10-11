import { useCallback, useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import NotificationToastLayer from './NotificationToastLayer';
import type { NotificationToastItem } from './NotificationToast';

export default function FlashNotifications(): React.JSX.Element {
  const { flash } = usePage<{ flash?: Record<string, string | null> }>().props;
  const [notifications, setNotifications] = useState<NotificationToastItem[]>([]);
  useEffect(() => {
    setNotifications(Object.entries(flash ?? {}).flatMap(([key, text], index) => text ? [{
      id: Date.now() + index, title: text,
      tone: key === 'error' ? 'error' : key === 'warning' ? 'warning' : 'success',
      position: 'top-right', autoHideDelay: 8000,
    } as NotificationToastItem] : []));
  }, [flash]);
  const close = useCallback((id: number) => setNotifications(items => items.filter(item => item.id !== id)), []);
  return <NotificationToastLayer notifications={notifications} onClose={close} />;
}
