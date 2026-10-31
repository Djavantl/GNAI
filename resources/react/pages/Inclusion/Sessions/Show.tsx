import { useState } from "react";
import { router, useForm } from "@inertiajs/react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { dateLabel } from "@/components/Students/Students.utils";
import SessionPage from "./SessionPage";
import { sessionsBase } from "./SessionForm.utils";
import type { SessionShowProps } from "./SessionList.types";
import "./Show.css";

type CancelForm = {
    cancellation_reason: string;
    send_notification: boolean;
};

function fail(text: string): void {
    notify({
        tone: "error",
        title: "Não foi possível concluir",
        text,
    });
}

function networkFailure(): false {
    fail("Falha de conexão. Confira sua conexão e tente novamente.");
    return false;
}

export default function InclusionSessionsShowPage({
    session,
    record,
    can,
    backUrl,
}: SessionShowProps): React.JSX.Element {
    const sessionUrl = `${sessionsBase}/${session.id}`;
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [sendDeleteNotification, setSendDeleteNotification] = useState(false);
    const [cancelOpen, setCancelOpen] = useState(false);
    const cancelForm = useForm<CancelForm>({
        cancellation_reason: "",
        send_notification: false,
    });
    const busy = deleting || cancelForm.processing;
    const reasonError = cancelForm.errors.cancellation_reason;
    const isAee = record?.kind === "aee";

    function openCancel(): void {
        cancelForm.reset();
        cancelForm.clearErrors();
        setCancelOpen(true);
    }

    function closeCancel(): void {
        if (!cancelForm.processing) setCancelOpen(false);
    }

    function cancelSession(): void {
        if (busy) return;

        cancelForm.transform((data) => ({
            ...data,
            send_notification: data.send_notification ? 1 : 0,
        }));
        cancelForm.post(`${sessionUrl}/cancel`, {
            preserveScroll: true,
            onNetworkError: networkFailure,
            onHttpException: () => {
                fail("Não foi possível cancelar o agendamento. Tente novamente.");
                return false;
            },
            onSuccess: () => {
                setCancelOpen(false);
                cancelForm.reset();
            },
        });
    }

    function openDelete(): void {
        setSendDeleteNotification(false);
        setDeleteOpen(true);
    }

    function remove(): void {
        if (busy) return;

        router.delete(sessionUrl, {
            data: session.is_scheduled
                ? { send_notification: sendDeleteNotification ? 1 : 0 }
                : {},
            preserveScroll: true,
            onStart: () => setDeleting(true),
            onNetworkError: networkFailure,
            onHttpException: () => {
                fail("Não foi possível excluir o agendamento. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <SessionPage
            title="Detalhes do Agendamento"
            subtitle="Informações detalhadas do atendimento especializado."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Agendamentos", href: sessionsBase },
                { label: `Agendamento #${session.id}` },
            ]}
        >
            <div className="sessionShow" aria-busy={busy}>
                <CardBox className="sessionShow__panel">
                    <FormSection
                        id="session-identification"
                        title="Identificação"
                        variant="inline"
                        iconClass="fa fa-id-card-o"
                    >
                        <dl className="sessionShow__grid">
                            <DetailField label="Alunos">
                                {session.student_names.length ? (
                                    <ul className="sessionShow__students">
                                        {session.student_names.map((name, index) => (
                                            <li key={`${name}-${index}`}>
                                                {name}
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    "Sem alunos"
                                )}
                            </DetailField>
                            <DetailField label="Profissional">
                                {session.professional_name}
                            </DetailField>
                            <DetailField label="Status">
                                <TableLabel tone={session.status_color}>
                                    {session.status_label}
                                </TableLabel>
                            </DetailField>
                        </dl>
                    </FormSection>
                    <FormSection
                        id="session-schedule"
                        title="Agendamento e Local"
                        variant="inline"
                        iconClass="fa fa-calendar"
                    >
                        <dl className="sessionShow__grid sessionShow__grid--three">
                            <DetailField label="Data">
                                {dateLabel(session.session_date)}
                            </DetailField>
                            <DetailField label="Horário">
                                {`${session.start_time ?? "--:--"} às ${session.end_time ?? "--:--"}`}
                            </DetailField>
                            <DetailField label="Local">
                                {session.location || "---"}
                            </DetailField>
                            <DetailField label="Tipo de Atendimento">
                                {session.attendance_type_label}
                            </DetailField>
                            <DetailField label="Formato">
                                {session.type_label}
                            </DetailField>
                        </dl>
                    </FormSection>
                    <FormSection
                        id="session-content"
                        title="Conteúdo do Agendamento"
                        variant="inline"
                        iconClass="fa fa-file-text-o"
                    >
                        <dl className="sessionShow__grid">
                            <DetailField label="Objetivo do Agendamento" wide>
                                <span className="sessionShow__text">
                                    {session.session_objective || "---"}
                                </span>
                            </DetailField>
                            {session.cancellation_reason ? (
                                <DetailField label="Motivo do Cancelamento" wide>
                                    <span className="sessionShow__text">
                                        {session.cancellation_reason}
                                    </span>
                                </DetailField>
                            ) : null}
                        </dl>
                    </FormSection>
                </CardBox>
                <FormActions
                    secondary={record?.pdf_url ? (
                        <Button
                            href={record.pdf_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            tone="dark-red"
                            leftIconClass="fa fa-file-pdf-o"
                            label="PDF Pedagógico"
                            title="Exportar atendimento pedagógico em PDF"
                            aria-label="Exportar atendimento pedagógico em PDF"
                        />
                    ) : undefined}
                >
                    <Button
                        href={backUrl}
                        tone="cancel"
                        label="Voltar"
                        title="Voltar para agendamentos"
                        aria-label="Voltar para agendamentos"
                        disabled={busy}
                    />
                    {can.edit ? (
                        <Button
                            href={`${sessionUrl}/edit`}
                            tone="warning"
                            leftIconClass="fa fa-pencil"
                            label="Editar"
                            title="Editar agendamento"
                            aria-label="Editar agendamento"
                        />
                    ) : null}
                    {can.cancel ? (
                        <Button
                            tone="inverse"
                            leftIconClass="fa fa-ban"
                            label="Cancelar Agendamento"
                            title="Cancelar agendamento"
                            aria-label="Cancelar agendamento"
                            disabled={busy}
                            onClick={openCancel}
                        />
                    ) : null}
                    {record?.show_url ? (
                        <Button
                            href={record.show_url}
                            tone="info"
                            leftIconClass="fa fa-eye"
                            label={isAee ? "Ver Atendimento AEE" : "Ver Atendimento Pedagógico"}
                        />
                    ) : null}
                    {record?.create_url ? (
                        <Button
                            href={record.create_url}
                            tone="success"
                            leftIconClass="fa fa-plus"
                            label={isAee ? "Criar Atendimento AEE" : "Criar Registro Pedagógico"}
                        />
                    ) : null}
                    {can.delete ? (
                        <Button
                            tone="dark-red"
                            leftIconClass="fa fa-trash-o"
                            label="Excluir"
                            title="Excluir agendamento"
                            aria-label="Excluir agendamento"
                            disabled={busy}
                            onClick={openDelete}
                        />
                    ) : null}
                </FormActions>
            </div>
            {can.cancel ? (
                <ConfirmActionModal
                    open={cancelOpen}
                    title="Confirmar Cancelamento"
                    message="Tem certeza que deseja cancelar este agendamento?"
                    details={(
                        <div className="sessionShow__cancelDetails">
                            <FormField
                                id="cancellation_reason"
                                label="Motivo do Cancelamento"
                                requiredIndicator
                                error={reasonError}
                            >
                                <TextArea
                                    id="cancellation_reason"
                                    name="cancellation_reason"
                                    rows={3}
                                    required
                                    placeholder="Descreva o motivo obrigatório..."
                                    value={cancelForm.data.cancellation_reason}
                                    disabled={cancelForm.processing}
                                    aria-invalid={Boolean(reasonError)}
                                    aria-describedby={reasonError ? "cancellation_reason-error" : undefined}
                                    onChange={(event) => cancelForm.setData(
                                        "cancellation_reason",
                                        event.target.value,
                                    )}
                                />
                            </FormField>
                            <ChoiceControl
                                type="checkbox"
                                tone="purple"
                                id="session-cancel-send-notification"
                                name="send_notification"
                                label="Enviar e-mail de aviso aos participantes"
                                checked={cancelForm.data.send_notification}
                                disabled={cancelForm.processing}
                                onChange={(checked) => cancelForm.setData("send_notification", checked)}
                            />
                        </div>
                    )}
                    processing={cancelForm.processing}
                    confirmLabel="Confirmar cancelamento"
                    processingLabel="Cancelando agendamento..."
                    cancelLabel="Voltar"
                    confirmTone="dark-red"
                    onClose={closeCancel}
                    onConfirm={cancelSession}
                />
            ) : null}
            {can.delete ? (
                <ConfirmActionModal
                    open={deleteOpen}
                    title="Excluir Agendamento"
                    message="Mover este agendamento para a lixeira?"
                    details={session.is_scheduled ? (
                        <div className="sessionShow__cancelDetails">
                            <ChoiceControl
                                type="checkbox"
                                tone="purple"
                                id="session-delete-send-notification"
                                name="send_notification"
                                label="Enviar e-mail de aviso aos participantes"
                                checked={sendDeleteNotification}
                                disabled={deleting}
                                onChange={setSendDeleteNotification}
                            />
                        </div>
                    ) : undefined}
                    processing={deleting}
                    confirmLabel="Confirmar exclusão"
                    processingLabel="Excluindo agendamento..."
                    confirmTone="dark-red"
                    onClose={() => {
                        if (!deleting) setDeleteOpen(false);
                    }}
                    onConfirm={remove}
                />
            ) : null}
        </SessionPage>
    );
}
