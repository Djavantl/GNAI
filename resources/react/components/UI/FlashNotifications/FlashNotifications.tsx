import { useCallback, useEffect, useRef, useState } from "react";
import { usePage } from "@inertiajs/react";
import type {
    NotificationToastItem,
    NotificationToastTone,
} from "@/components/UI/NotificationToast/NotificationToast";
import NotificationToastLayer from "@/components/UI/NotificationToast/NotificationToastLayer";

type FlashKey = "success" | "error" | "warning" | "status";
type Flash = Partial<Record<FlashKey, string | null>>;

const tones: Record<FlashKey, NotificationToastTone> = {
    success: "success",
    error: "error",
    warning: "warning",
    status: "info",
};

const titles: Record<FlashKey, string> = {
    success: "Sucesso",
    error: "Não foi possível concluir",
    warning: "Atenção",
    status: "Informação",
};

/** Global authenticated-layout consumer for server flash messages. */
export default function FlashNotifications(): React.JSX.Element {
    const { flash } = usePage<{ flash?: Flash }>().props;
    const [notifications, setNotifications] = useState<
        NotificationToastItem[]
    >([]);
    const sequence = useRef(0);

    useEffect(() => {
        setNotifications(
            (Object.keys(tones) as FlashKey[]).flatMap((key) => {
                const text = flash?.[key];

                return text
                    ? [
                          {
                              id: ++sequence.current,
                              tone: tones[key],
                              title: titles[key],
                              text,
                              position: "bottom-right" as const,
                              autoHideDelay:
                                  key === "success" ? 5000 : undefined,
                              clickToHide: false,
                          },
                      ]
                    : [];
            }),
        );
    }, [flash]);

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
