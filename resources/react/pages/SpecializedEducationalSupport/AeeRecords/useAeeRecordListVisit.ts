import { router } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import { notify } from "@/components/UI/NotificationToast/notify";
import { compactListFilters } from "@/hooks/useAutoListFilters";

/**
 * Recarrega as listagens AEE por visitas Inertia parciais, cancelando a
 * visita anterior quando um novo filtro ou página é solicitado.
 */
export default function useAeeRecordListVisit(url: string, failureText: string) {
    const [loading, setLoading] = useState(false);
    const cancel = useRef<(() => void) | undefined>(undefined);

    useEffect(
        () => () => {
            cancel.current?.();
        },
        [],
    );

    function visit(filters: Record<string, string>, page = 1): void {
        cancel.current?.();
        router.get(
            url,
            {
                ...compactListFilters(filters),
                ...(page > 1 ? { page } : {}),
            },
            {
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
                        text: failureText,
                    });
                    return false;
                },
                onFinish: () => {
                    setLoading(false);
                    cancel.current = undefined;
                },
            },
        );
    }

    function cancelVisit(): void {
        cancel.current?.();
    }

    return { loading, visit, cancelVisit };
}
