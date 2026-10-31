import { useRef, useState, type ReactNode } from "react";
import { useForm } from "@inertiajs/react";
import { useStudentRequest } from "@/components/Students/useStudentRequest";
import Button, { type ButtonTone } from "@/components/UI/Button/Button";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";

/** Ação do rodapé do PEI que muda estado ou exclui, sempre confirmada antes do envio. */
export default function PeiConfirmAction({
    method,
    action,
    label,
    ariaLabel,
    tone,
    iconClass,
    modalTitle,
    message,
    confirmLabel,
    processingLabel,
    confirmTone = tone,
}: {
    method: "delete" | "patch";
    action: string;
    label: string;
    ariaLabel: string;
    tone: ButtonTone;
    iconClass: string;
    modalTitle: string;
    message: ReactNode;
    confirmLabel: string;
    processingLabel: string;
    confirmTone?: ButtonTone;
}): React.JSX.Element {
    const [open, setOpen] = useState(false);
    const busy = useRef(false);
    const form = useForm({});
    const request = useStudentRequest();

    function confirm(): void {
        if (busy.current || form.processing) return;

        busy.current = true;
        const options = {
            ...request.options,
            preserveScroll: true,
            onSuccess: () => setOpen(false),
            onFinish: () => {
                busy.current = false;
            },
        };

        if (method === "patch") {
            form.patch(action, options);
        } else {
            form.delete(action, options);
        }
    }

    return (
        <>
            <Button
                type="button"
                tone={tone}
                leftIconClass={iconClass}
                label={label}
                title={ariaLabel}
                aria-label={ariaLabel}
                disabled={form.processing}
                onClick={() => setOpen(true)}
            />
            <ConfirmActionModal
                open={open}
                title={modalTitle}
                message={message}
                processing={form.processing}
                confirmLabel={confirmLabel}
                processingLabel={processingLabel}
                confirmTone={confirmTone}
                onClose={() => {
                    if (!form.processing) setOpen(false);
                }}
                onConfirm={confirm}
            />
        </>
    );
}
