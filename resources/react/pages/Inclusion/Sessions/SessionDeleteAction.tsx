import { useState, type ReactNode } from "react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import { useTableListAction } from "@/components/Tables/TableList/TableList";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import { sessionsBase } from "./SessionForm.utils";
import type { SessionListRow } from "./SessionList.types";

interface SessionDeleteControls {
    open: (session: SessionListRow) => void;
    processing: boolean;
}

export default function SessionDeleteAction({
    children,
}: {
    children: (controls: SessionDeleteControls) => ReactNode;
}): React.JSX.Element {
    const removal = useTableListAction<SessionListRow>({
        failureText: "Não foi possível excluir o agendamento. Tente novamente.",
    });
    const [sendNotification, setSendNotification] = useState(false);

    function open(session: SessionListRow): void {
        setSendNotification(false);
        removal.open(session);
    }

    return (
        <>
            {children({ open, processing: removal.processing })}
            <ConfirmActionModal
                open={removal.target !== null}
                title="Excluir Agendamento"
                message="Mover este agendamento para a lixeira?"
                details={removal.target?.is_scheduled ? (
                    <div className="sessionsPage__deleteDetails">
                        <ChoiceControl
                            type="checkbox"
                            tone="purple"
                            id="session-delete-send-notification"
                            name="send_notification"
                            label="Enviar e-mail de aviso aos participantes"
                            checked={sendNotification}
                            disabled={removal.processing}
                            onChange={setSendNotification}
                        />
                    </div>
                ) : undefined}
                processing={removal.processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo agendamento..."
                confirmTone="dark-red"
                onClose={removal.close}
                onConfirm={() => {
                    if (!removal.target) return;
                    removal.submit(
                        "delete",
                        `${sessionsBase}/${removal.target.id}`,
                        removal.target.is_scheduled
                            ? { send_notification: sendNotification ? 1 : 0 }
                            : {},
                    );
                }}
            />
        </>
    );
}
