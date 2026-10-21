import { useCallback, useEffect, useRef, useState } from "react";
import { usePage } from "@inertiajs/react";
import type {
    NotificationToastItem,
    NotificationToastTone,
} from "@/components/UI/NotificationToast/NotificationToast";
import NotificationToastLayer from "@/components/UI/NotificationToast/NotificationToastLayer";
import {
    subscribeNotifications,
    type AppNotification,
} from "@/components/UI/NotificationToast/notify";

type FlashKey = "success" | "error" | "warning" | "status";
type Flash = Partial<Record<FlashKey, string | null>> & {
    response_id?: string;
};
type ValidationErrors = Record<string, unknown>;

const tones: Record<FlashKey, NotificationToastTone> = {
    success: "success",
    error: "error",
    warning: "warning",
    status: "success",
};

const titles: Record<FlashKey, string> = {
    success: "Sucesso",
    error: "Não foi possível concluir",
    warning: "Atenção",
    status: "Sucesso",
};

const processedPages = new WeakSet<object>();
const processedResponseIds = new Set<string>();
const processedResponseLimit = 100;

function markResponseAsProcessed(page: object, responseId?: string): boolean {
    if (!responseId) {
        if (processedPages.has(page)) return false;

        processedPages.add(page);
        return true;
    }

    if (processedResponseIds.has(responseId)) return false;

    processedResponseIds.add(responseId);
    if (processedResponseIds.size > processedResponseLimit) {
        const oldestResponseId = processedResponseIds.values().next().value;
        if (oldestResponseId) processedResponseIds.delete(oldestResponseId);
    }

    return true;
}

/** Global consumer for server flash, validation and client-side operation messages. */
export default function FlashNotifications(): React.JSX.Element {
    const page = usePage<{
        flash?: Flash;
        errors?: ValidationErrors;
    }>();
    const [notifications, setNotifications] = useState<
        NotificationToastItem[]
    >([]);
    const sequence = useRef(0);

    useEffect(() => {
        const { flash, errors } = page.props;
        if (!markResponseAsProcessed(page, flash?.response_id)) return;

        const next: NotificationToastItem[] = (
            Object.keys(tones) as FlashKey[]
        ).flatMap((key) => {
            const text = flash?.[key];

            return text
                ? [
                      {
                          id: ++sequence.current,
                          tone: tones[key],
                          title: titles[key],
                          text,
                          position: "top-right" as const,
                          autoHideDelay: 5000,
                          clickToHide: false,
                      },
                  ]
                : [];
        });

        if (Object.keys(errors ?? {}).length && !flash?.error) {
            next.push({
                id: ++sequence.current,
                tone: "error",
                title: "Verifique os campos do formulário",
                text: "Há campos obrigatórios não preenchidos ou dados inválidos. Confira os campos destacados.",
                position: "top-right",
                autoHideDelay: 5000,
                clickToHide: false,
            });
        }

        if (next.length) {
            setNotifications((items) => [...items, ...next]);
        }
    }, [page]);

    useEffect(() => {
        const show = (notification: AppNotification) => {
            setNotifications((items) => [
                ...items,
                {
                    ...notification,
                    id: ++sequence.current,
                    position: notification.position ?? "top-right",
                    autoHideDelay:
                        notification.autoHideDelay ?? 5000,
                    clickToHide: notification.clickToHide ?? false,
                },
            ]);
        };

        return subscribeNotifications(show);
    }, []);

    const close = useCallback((id: number) => {
        setNotifications((items) =>
            items.filter((item) => item.id !== id),
        );
    }, []);

    return (
        <NotificationToastLayer
            notifications={notifications}
            onClose={close}
        />
    );
}
