import { useEffect, useRef, useState } from "react";
import { router } from "@inertiajs/react";
import { notify } from "@/components/UI/NotificationToast/notify";
import { compactListFilters } from "@/hooks/useAutoListFilters";

type QueryValue = string | number;

/** Inertia visits shared by the table filters, pagination and weekly agenda. */
export function useSessionListRequest(errorText: string) {
    const [loading, setLoading] = useState(false);
    const cancel = useRef<(() => void) | undefined>(undefined);

    useEffect(
        () => () => {
            cancel.current?.();
        },
        [],
    );

    function visit(url: string, data: Record<string, QueryValue> = {}): void {
        cancel.current?.();
        router.get(url, data, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onCancelToken: (token) => {
                cancel.current = () => token.cancel();
            },
            onStart: () => setLoading(true),
            onNetworkError: () => {
                notify({
                    tone: "error",
                    title: "Falha de conexão",
                    text: "Não foi possível conectar ao servidor. Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: errorText,
                });
                return false;
            },
            onFinish: () => {
                setLoading(false);
                cancel.current = undefined;
            },
        });
    }

    return { loading, visit };
}

export function sessionQuery(
    values: Record<string, string>,
    page?: number,
): Record<string, QueryValue> {
    const query: Record<string, QueryValue> = {
        ...(compactListFilters(values) as Record<string, string>),
    };

    if (page && page > 1) {
        query.page = page;
    }

    return query;
}
