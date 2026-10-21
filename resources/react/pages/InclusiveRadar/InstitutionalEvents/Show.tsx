import { router } from "@inertiajs/react";
import { useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import InstitutionalEventPage from "./InstitutionalEventPage";
import type { InstitutionalEventRecord } from "./InstitutionalEvent.types";
import "./InstitutionalEvents.css";

type Props = {
    event: InstitutionalEventRecord;
    backRoute: string;
    can: { edit: boolean; destroy: boolean; pdf: boolean };
};

const base = "/inclusive-radar/institutional-events";

export default function InclusiveRadarInstitutionalEventsShowPage({
    event,
    backRoute,
    can,
}: Props): React.JSX.Element {
    const [confirmingDelete, setConfirmingDelete] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [failure, setFailure] = useState("");
    const url = `${base}/${event.id}`;

    function closeModal(): void {
        if (processing) return;
        setConfirmingDelete(false);
        setFailure("");
    }

    function remove(): void {
        if (processing) return;
        setFailure("");
        setProcessing(true);
        router.delete(url, {
            preserveScroll: true,
            onError: () => setFailure("Não foi possível excluir o evento. Tente novamente."),
            onSuccess: () => setConfirmingDelete(false),
            onFinish: () => setProcessing(false),
        });
    }

    return (
        <InstitutionalEventPage
            title="Detalhes da Agenda Institucional"
            subtitle="Visualize as informações do registro da agenda institucional."
            eventId={event.id}
            eventTitle={event.title}
            backRoute={backRoute}
        >
            <CardBox className="institutionalEventShow__card">
                <FormSection
                    id="institutional-event-show-main"
                    title="Informações do Registro"
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <dl className="institutionalEventShow__grid">
                        <DetailField label="Título" wide>{event.title}</DetailField>
                        <DetailField label="Descrição" wide>
                            {event.description_html ? (
                                <div
                                    className="institutionalEventShow__richText"
                                    dangerouslySetInnerHTML={{ __html: event.description_html }}
                                />
                            ) : "Nenhuma descrição fornecida."}
                        </DetailField>
                        <DetailField label="Data de Início">{event.start_date_label}</DetailField>
                        <DetailField label="Horário de Início">{event.start_time}</DetailField>
                        <DetailField label="Data de Término">{event.end_date_label}</DetailField>
                        <DetailField label="Horário de Término">{event.end_time}</DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="institutional-event-show-details"
                    title="Detalhes Adicionais"
                    variant="inline"
                    iconClass="fa fa-info-circle"
                >
                    <dl className="institutionalEventShow__grid">
                        <DetailField label="Local">{event.location || "—"}</DetailField>
                        <DetailField label="Organizador">{event.organizer || "—"}</DetailField>
                        <DetailField label="Ouvintes" wide>{event.audience || "—"}</DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="institutional-event-show-visibility"
                    title="Configurações de Visibilidade"
                    variant="inline"
                    iconClass="fa fa-eye"
                >
                    <dl className="institutionalEventShow__grid">
                        <DetailField label="Status">
                            <TableLabel tone={event.is_active ? "success" : "inverse"}>
                                {event.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${url}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Exportar evento em PDF"
                        aria-label="Exportar evento em PDF"
                    />
                ) : undefined}
            >
                <Button
                    href={backRoute || base}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para a agenda institucional"
                    aria-label="Voltar para a agenda institucional"
                    disabled={processing}
                />
                {can.edit ? (
                    <Button
                        href={`${url}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar evento institucional"
                        aria-label="Editar evento institucional"
                        disabled={processing}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir evento institucional"
                        aria-label="Excluir evento institucional"
                        disabled={processing}
                        onClick={() => setConfirmingDelete(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={confirmingDelete}
                title="Excluir Evento Institucional"
                message={<>O evento <strong>{event.title}</strong> será excluído permanentemente.</>}
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo evento..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={closeModal}
                onConfirm={remove}
            />
        </InstitutionalEventPage>
    );
}
