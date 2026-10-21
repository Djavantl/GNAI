import type {
    NotificationToastPosition,
    NotificationToastTone,
} from "./NotificationToast";

type NotificationListener = (notification: AppNotification) => void;

let activeListener: NotificationListener | undefined;
const pendingNotifications: AppNotification[] = [];
const recentNotifications = new Map<string, number>();
const pendingLimit = 20;
const duplicateWindow = 750;
const recentLimit = 50;

export interface AppNotification {
    tone: NotificationToastTone;
    title: string;
    text?: string;
    position?: NotificationToastPosition;
    autoHideDelay?: number;
    clickToHide?: boolean;
}

export function notify(notification: AppNotification): void {
    const signature = [
        notification.tone,
        notification.title,
        notification.text ?? "",
        notification.position ?? "",
    ].join("\u0000");
    const now = Date.now();
    const lastShownAt = recentNotifications.get(signature);

    if (lastShownAt !== undefined && now - lastShownAt < duplicateWindow) {
        return;
    }

    recentNotifications.set(signature, now);
    if (recentNotifications.size > recentLimit) {
        const oldestSignature = recentNotifications.keys().next().value;
        if (oldestSignature) recentNotifications.delete(oldestSignature);
    }

    if (!activeListener) {
        pendingNotifications.push(notification);
        if (pendingNotifications.length > pendingLimit) {
            pendingNotifications.splice(
                0,
                pendingNotifications.length - pendingLimit,
            );
        }
        return;
    }

    activeListener(notification);
}

/**
 * Registers the single application-level toast consumer.
 * Messages emitted during an Inertia layout transition are queued and flushed
 * as soon as the next consumer mounts, instead of being lost in the gap.
 */
export function subscribeNotifications(
    listener: NotificationListener,
): () => void {
    activeListener = listener;

    const pending = pendingNotifications.splice(0);
    pending.forEach(listener);

    return () => {
        if (activeListener === listener) {
            activeListener = undefined;
        }
    };
}
