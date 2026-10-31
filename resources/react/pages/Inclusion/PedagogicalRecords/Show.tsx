import { router } from "@inertiajs/react";
import { useRef, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import { aeeBase, studentRecordUrl } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type {
    PedagogicalRecordShowProps,
} from "./PedagogicalRecordShow.types";
import "./Show.css";

const recordsBase = `${aeeBase}/pedagogical-records`;

function TextValue({
    value,
    fallback,
}: {
    value: string | null;
    fallback: string;
}): React.JSX.Element {
    const text = value?.trim();

    return (
        <span className="pedagogicalRecordShow__text">
            {text || fallback}
        </span>
    );
}

export default function InclusionPedagogicalRecordsShowPage({
    pedagogicalRecord: record,
    session,
    can,
    returnStudentId,
}: PedagogicalRecordShowProps): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const busy = useRef(false);
    const studentId = returnStudentId && returnStudentId > 0 ? returnStudentId : undefined;
    const studentQuery = studentId ? `?student_id=${studentId}` : "";
    const recordUrl = `${recordsBase}/${record.id}`;
    const sessionHref = `${aeeBase}/sessions/${session.id}/show`;
    const backHref = studentId
        ? studentRecordUrl(studentId, "pedagogical-attendance")
        : sessionHref;
    const backLabel = studentId
        ? "Voltar para o prontuário do aluno"
        : "Voltar para o agendamento";
    const showPedagogicalContent = record.is_present || record.has_guardians;
    const subtitle = `Agendamento #${session.id} • Realizado em: ${session.session_date ?? "Não informado"}`;

    function failure(text: string): void {
        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text,
        });
    }

    function remove(): void {
        if (busy.current) return;

        busy.current = true;
        setDeleting(true);
        router.delete(`${recordUrl}${studentQuery}`, {
            onNetworkError: () => {
                failure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                failure("Não foi possível excluir o atendimento pedagógico. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => {
                busy.current = false;
                setDeleting(false);
            },
        });
    }

    return (
        <BusinessPage
            title="Atendimento Pedagógico"
            subtitle={subtitle}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Agendamentos", href: `${aeeBase}/sessions` },
                { label: `Agendamento #${session.id}`, href: sessionHref },
                { label: "Atendimento Pedagógico" },
            ]}
            className="pedagogicalRecordShow"
        >
            <CardBox>
                <FormSection
                    id="pedagogical-record-show-identification"
                    title="Identificação do Estudante"
                    variant="inline"
                    iconClass="fa fa-user"
                >
                    <dl className="pedagogicalRecordShow__grid">
                        <DetailField label="Aluno">
                            {session.student_name ?? "Aluno não informado"}
                        </DetailField>
                        <DetailField label="Profissional">
                            {session.professional_name ?? "Profissional não informado"}
                        </DetailField>
                        <DetailField
                            label="Horário"
                            wide
                        >
                            {`${session.start_time ?? "--:--"} às ${session.end_time ?? "--:--"}`}
                        </DetailField>
                        {record.has_guardians ? (
                            <DetailField
                                label="Responsáveis participantes"
                                wide
                            >
                                {record.guardian_names}
                            </DetailField>
                        ) : null}
                    </dl>
                </FormSection>
                <FormSection
                    id="pedagogical-record-show-follow-up"
                    title="Acompanhamento Pedagógico"
                    variant="inline"
                    iconClass="fa fa-book"
                >
                    <dl className="pedagogicalRecordShow__grid">
                        <DetailField
                            label="Motivo do Acompanhamento"
                            wide
                        >
                            <TextValue
                                value={record.follow_up_reason}
                                fallback="Não informado."
                            />
                        </DetailField>
                        <DetailField label="Período/Duração do Acompanhamento">
                            {record.duration || "Não informado"}
                        </DetailField>
                        <DetailField label="Presença do Estudante no Atendimento">
                            <TableLabel tone={record.is_present ? "success" : "danger"}>
                                <i
                                    className={record.is_present
                                        ? "fa fa-check-circle m-r-5"
                                        : "fa fa-times-circle m-r-5"}
                                    aria-hidden="true"
                                />
                                {record.is_present ? "Presente" : "Ausente"}
                            </TableLabel>
                        </DetailField>
                        {!record.is_present ? (
                            <DetailField
                                label="Motivo da Ausência"
                                wide
                            >
                                <TextValue
                                    value={record.absence_reason}
                                    fallback="Não informado."
                                />
                            </DetailField>
                        ) : null}
                        {showPedagogicalContent ? (
                            <>
                                <DetailField
                                    label="Registro do Acompanhamento Pedagógico Sistemático"
                                    wide
                                >
                                    <TextValue
                                        value={record.systematic_pedagogical_follow_up_record}
                                        fallback="Não informado"
                                    />
                                </DetailField>
                                <DetailField
                                    label="Estratégias e Recursos Adotados (quando necessário)"
                                    wide
                                >
                                    <TextValue
                                        value={record.strategies_and_resources_adopted}
                                        fallback="N/A"
                                    />
                                </DetailField>
                                <DetailField
                                    label="Encaminhamentos Realizados"
                                    wide
                                >
                                    <TextValue
                                        value={record.referrals_made}
                                        fallback="N/A"
                                    />
                                </DetailField>
                                <DetailField
                                    label="Observações Complementares"
                                    wide
                                >
                                    <TextValue
                                        value={record.complementary_observations}
                                        fallback="N/A"
                                    />
                                </DetailField>
                            </>
                        ) : null}
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${recordUrl}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Exportar atendimento pedagógico em PDF"
                        aria-label="Exportar atendimento pedagógico em PDF"
                    />
                ) : undefined}
            >
                <Button
                    href={backHref}
                    tone="cancel"
                    label="Voltar"
                    title={backLabel}
                    aria-label={backLabel}
                    disabled={deleting}
                />
                {can.update ? (
                    <Button
                        href={`${recordUrl}/edit${studentQuery}`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar atendimento pedagógico"
                        aria-label="Editar atendimento pedagógico"
                        disabled={deleting}
                    />
                ) : null}
                {can.delete ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir atendimento pedagógico"
                        aria-label="Excluir atendimento pedagógico"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Atendimento Pedagógico"
                message="Deseja excluir este atendimento pedagógico?"
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo atendimento..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!deleting) setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </BusinessPage>
    );
}
