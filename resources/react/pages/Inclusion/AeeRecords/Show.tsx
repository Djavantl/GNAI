import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import Tabs, { type TabItem } from "@/components/Navigation/Tabs/Tabs";
import { dateLabel } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import {
    AeeEvaluationDetails,
    AeeRecordExecutionSection,
} from "./AeeRecordDetails";
import type { AeeRecordRecord, AeeSession } from "./AeeRecord.types";
import {
    aeeBase,
    aeeRecordPdfUrl,
    sessionShowUrl,
} from "./AeeRecord.utils";
import { useAeeDeletion } from "./useAeeDeletion";
import "./AeeRecords.css";

export default function InclusionAeeRecordsShowPage({
    aeeRecord,
    session,
    can,
}: {
    aeeRecord: AeeRecordRecord;
    session: AeeSession;
    can: {
        update: boolean;
        delete: boolean;
    };
}): React.JSX.Element {
    const recordUrl = `${aeeBase}/aee-records/${aeeRecord.id}`;
    const sessionId = aeeRecord.attendance_session_id;
    const deletion = useAeeDeletion(recordUrl);

    const tabs: TabItem[] = (aeeRecord.student_evaluations ?? []).map((evaluation) => {
        const name = evaluation.student?.person?.name ?? `Aluno #${evaluation.student_id}`;

        return {
            id: `evaluation-${evaluation.id}`,
            label: (
                <span className="aeeRecordForm__tabLabel">
                    <span>{name}</span>
                    {!evaluation.is_present ? (
                        <TableLabel tone="danger">Falta</TableLabel>
                    ) : null}
                </span>
            ),
            content: (
                <AeeEvaluationDetails
                    evaluation={evaluation}
                    studentName={name}
                />
            ),
        };
    });

    return (
        <BusinessPage
            title="Registro de Atendimento AEE"
            subtitle={`Agendamento #${sessionId} • Realizada em: ${dateLabel(session?.session_date)}`}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Agendamentos", href: `${aeeBase}/sessions` },
                { label: `Agendamento #${sessionId}`, href: sessionShowUrl(sessionId) },
                { label: "Registro" },
            ]}
            className="aeeRecordPage"
        >
            <div aria-busy={deletion.deleting}>
                <CardBox>
                    <AeeRecordExecutionSection
                        id="aee-record-execution"
                        aeeRecord={aeeRecord}
                    />
                    <FormSection
                        id="aee-record-evaluations"
                        title="Desempenho Individual"
                        variant="inline"
                        iconClass="fa fa-users"
                    >
                        {tabs.length ? (
                            <div className="aeeRecordForm__tabs">
                                <Tabs
                                    items={tabs}
                                    variant="vertical"
                                />
                            </div>
                        ) : (
                            <p className="aeeRecordForm__empty">
                                Nenhuma avaliação individual registrada.
                            </p>
                        )}
                    </FormSection>
                </CardBox>
                <FormActions
                    secondary={(
                        <Button
                            href={aeeRecordPdfUrl(aeeRecord.id)}
                            target="_blank"
                            rel="noopener noreferrer"
                            tone="dark-red"
                            leftIconClass="fa fa-file-pdf-o"
                            label="PDF"
                            title="Exportar registro do atendimento AEE em PDF"
                            aria-label="Exportar registro do atendimento AEE em PDF"
                        />
                    )}
                    status={deletion.deleting ? "Excluindo registro do atendimento AEE..." : undefined}
                >
                    <Button
                        href={sessionShowUrl(sessionId)}
                        tone="cancel"
                        label="Voltar"
                        title="Voltar para o agendamento"
                        aria-label="Voltar para o agendamento"
                        disabled={deletion.deleting}
                    />
                    {can.update ? (
                        <Button
                            href={`${recordUrl}/edit`}
                            tone="warning"
                            leftIconClass="fa fa-pencil"
                            label="Editar"
                            title="Editar registro do atendimento AEE"
                            aria-label="Editar registro do atendimento AEE"
                            disabled={deletion.deleting}
                        />
                    ) : null}
                    {can.delete ? (
                        <Button
                            tone="dark-red"
                            leftIconClass="fa fa-trash-o"
                            label="Excluir"
                            title="Excluir registro do atendimento AEE"
                            aria-label="Excluir registro do atendimento AEE"
                            disabled={deletion.deleting}
                            onClick={deletion.openConfirm}
                        />
                    ) : null}
                </FormActions>
            </div>
            <ConfirmActionModal
                open={deletion.open}
                title="Excluir Registro de Atendimento"
                message="Deseja excluir permanentemente este registro de atendimento?"
                processing={deletion.deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo registro..."
                confirmTone="dark-red"
                onClose={deletion.closeConfirm}
                onConfirm={deletion.remove}
            />
        </BusinessPage>
    );
}
