import { useEffect, useRef } from "react";
import Button from "@/components/UI/Button/Button";
import Modal from "@/components/UI/Modal/Modal";

export default function SessionNotificationModal({
    open,
    title,
    message,
    onClose,
    onChoose,
}: {
    open: boolean;
    title: string;
    message: string;
    onClose: () => void;
    onChoose: (sendNotification: boolean) => void;
}): React.JSX.Element {
    const content = useRef<HTMLParagraphElement | null>(null);

    useEffect(() => {
        if (!open) return undefined;

        const returnFocus = document.activeElement as HTMLElement | null;
        const frame = requestAnimationFrame(() => {
            content.current
                ?.closest('[role="dialog"]')
                ?.querySelector<HTMLElement>(".sessionNotificationModal__back")
                ?.focus();
        });

        return () => {
            cancelAnimationFrame(frame);
            returnFocus?.focus();
        };
    }, [open]);

    return (
        <Modal
            open={open}
            title={title}
            effect="fadein"
            onClose={onClose}
            contentClassName="sessionNotificationModal"
            footer={(
                <div className="sessionNotificationModal__actions">
                    <Button
                        className="sessionNotificationModal__back"
                        tone="cancel"
                        label="Voltar"
                        title="Voltar ao formulário"
                        aria-label="Voltar ao formulário"
                        onClick={onClose}
                    />
                    <Button
                        tone="inverse"
                        leftIconClass="fa fa-save"
                        label="Salvar sem E-mail"
                        onClick={() => onChoose(false)}
                    />
                    <Button
                        tone="save"
                        leftIconClass="fa fa-envelope"
                        label="Salvar e Enviar E-mail"
                        onClick={() => onChoose(true)}
                    />
                </div>
            )}
        >
            <p ref={content} className="sessionNotificationModal__message">
                {message}
            </p>
        </Modal>
    );
}
