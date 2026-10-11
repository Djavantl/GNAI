import { useCallback, useEffect, useRef, useState } from 'react';
import { usePage } from '@inertiajs/react';
import NotificationToastLayer from '@/components/UI/NotificationToast/NotificationToastLayer';
import type { NotificationToastItem, NotificationToastTone } from '@/components/UI/NotificationToast/NotificationToast';

type Flash = Partial<Record<'success' | 'error' | 'warning' | 'status', string | null>>;

/** Mount once in the page composition until the application adopts a shared consumer. */
export default function FlashNotifications(): React.JSX.Element {
  const { flash } = usePage<{ flash?: Flash }>().props;
  const [notifications, setNotifications] = useState<NotificationToastItem[]>([]);
  const sequence = useRef(0);
  useEffect(() => {
    const tones: Record<keyof Flash, NotificationToastTone> = { success: 'success', error: 'error', warning: 'warning', status: 'info' };
    const titles = { success: 'Sucesso', error: 'Não foi possível concluir', warning: 'Atenção', status: 'Informação' };
    setNotifications((Object.keys(tones) as Array<keyof Flash>).flatMap(key => flash?.[key] ? [{
      id: ++sequence.current, tone: tones[key], title: titles[key], text: flash[key]!,
      position: 'bottom-right' as const, autoHideDelay: key === 'success' ? 5000 : undefined, clickToHide: false,
    }] : []));
  }, [flash]);
  const close = useCallback((id: number) => setNotifications(items => items.filter(item => item.id !== id)), []);
  return <NotificationToastLayer notifications={notifications} onClose={close} />;
}
