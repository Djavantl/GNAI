import { useState } from "react";
import { router } from "@inertiajs/react";
import { notify } from "@/components/UI/NotificationToast/notify";

/**
 * Coordena a confirmação e o envio de exclusões das visualizações AEE.
 * Sucesso e regras de negócio chegam pelo flash do redirect do servidor;
 * falhas de transporte são informadas pelo toast compartilhado.
 */
export function useAeeDeletion(url: string): {
    open: boolean;
    deleting: boolean;
    openConfirm: () => void;
    closeConfirm: () => void;
    remove: () => void;
} {
    const [open, setOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);

    function fail(text: string): false {
        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text,
        });
        return false;
    }

    function remove(): void {
        if (deleting) return;

        setDeleting(true);
        router.delete(url, {
            onHttpException: () => fail("Não foi possível concluir a exclusão. Tente novamente."),
            onNetworkError: () => fail("Falha de conexão. Confira sua conexão e tente novamente."),
            onSuccess: () => setOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return {
        open,
        deleting,
        openConfirm: () => setOpen(true),
        closeConfirm: () => {
            if (!deleting) setOpen(false);
        },
        remove,
    };
}
