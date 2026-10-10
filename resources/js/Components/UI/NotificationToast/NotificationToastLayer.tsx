import NotificationToast, {
  type NotificationToastItem,
  type NotificationToastPosition
} from './NotificationToast';

interface NotificationToastLayerProps {
  notifications: NotificationToastItem[];
  onClose: (id: number) => void;
}

const positions: NotificationToastPosition[] = [
  'top-left',
  'top-right',
  'top-center',
  'bottom-left',
  'bottom-right',
  'bottom-center',
  'left-middle',
  'right-middle'
];

export default function NotificationToastLayer({ notifications, onClose }: NotificationToastLayerProps): JSX.Element {
  return (
    <>
      {positions.map((position: NotificationToastPosition) => {
        const items = notifications.filter((notification: NotificationToastItem) => notification.position === position);

        if (!items.length) {
          return null;
        }

        return (
          <div key={position} className={`notificationToastLayer notificationToastLayer--${position}`}>
            {items.map((notification: NotificationToastItem) => (
              <NotificationToast
                key={notification.id}
                notification={notification}
                onClose={onClose}
              />
            ))}
          </div>
        );
      })}
    </>
  );
}
