import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import { dateLabel, studentRecordUrl } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import {
    AeeEvaluationDetails,
    AeeRecordExecutionSection,
} from "@/pages/Inclusion/AeeRecords/AeeRecordDetails";
import type {
    AeeRecordRecord,
    AeeStudentEvaluationRecord,
} from "@/pages/Inclusion/AeeRecords/AeeRecord.types";
import {
    aeeBase,
    aeeRecordShowUrl,
    studentAeeEvaluationUrl,
    studentAeeRecordsUrl,
} from "@/pages/Inclusion/AeeRecords/AeeRecord.utils";
import { useAeeDeletion } from "@/pages/Inclusion/AeeRecords/useAeeDeletion";
import "@/pages/Inclusion/AeeRecords/AeeRecords.css";

export default function InclusionStudentsAeeRecordsShowPage({
    student,
    evaluation,
    can,
}: {
    student: { id: number };
    evaluation: AeeStudentEvaluationRecord & {
        aee_record: AeeRecordRecord;
    };
    can: {
        update: boolean;
        delete: boolean;
    };
}): React.JSX.Element {
    const aeeRecord = evaluation.aee_record;
    const session = aeeRecord.attendance_session;
    const studentName = evaluation.student?.person?.name ?? `Aluno #${student.id}`;
    const backHref = studentRecordUrl(student.id, "aee-attendance");
    const deletion = useAeeDeletion(studentAeeEvaluationUrl(student.id, evaluation.id, ""));

    return (
        <BusinessPage
            title="Registro de atendimento AEE do Aluno"
            subtitle={`${studentName} • Agendamento #${aeeRecord.attendance_session_id} • Realizada em: ${dateLabel(session?.session_date)}`}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: `${aeeBase}/students` },
                { label: "Aluno", href: `${aeeBase}/students/${student.id}/show` },
                { label: "Registros de Atendimentos AEE", href: studentAeeRecordsUrl(student.id) },
                { label: "Visualizar" },
            ]}
            className="aeeRecordPage"
        >
            <div aria-busy={deletion.deleting}>
                <CardBox>
                    <AeeRecordExecutionSection
                        id="student-aee-execution"
                        aeeRecord={aeeRecord}
                    />
                    <FormSection
                        id="student-aee-evaluation"
                        title="Avaliação Individual do Aluno"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <AeeEvaluationDetails
                            evaluation={evaluation}
                            studentName={studentName}
                        />
                    </FormSection>
                </CardBox>
                <FormActions
                    secondary={(
                        <>
                            <Button
                                href={studentAeeEvaluationUrl(student.id, evaluation.id, "pdf")}
                                target="_blank"
                                rel="noopener noreferrer"
                                tone="dark-red"
                                leftIconClass="fa fa-file-pdf-o"
                                label="PDF"
                                title="Exportar registro do aluno em PDF"
                                aria-label="Exportar registro do aluno em PDF"
                            />
                            <Button
                                href={aeeRecordShowUrl(aeeRecord.id)}
                                tone="info"
                                leftIconClass="fa fa-clone"
                                label="Ver registro completo"
                                title="Ver registro completo do atendimento AEE"
                                aria-label="Ver registro completo do atendimento AEE"
                                disabled={deletion.deleting}
                            />
                        </>
                    )}
                    status={deletion.deleting ? "Excluindo registro do aluno..." : undefined}
                >
                    <Button
                        href={backHref}
                        tone="cancel"
                        label="Voltar"
                        title="Voltar para o prontuário do aluno"
                        aria-label="Voltar para o prontuário do aluno"
                        disabled={deletion.deleting}
                    />
                    {can.update ? (
                        <Button
                            href={studentAeeEvaluationUrl(student.id, evaluation.id, "edit")}
                            tone="warning"
                            leftIconClass="fa fa-pencil"
                            label="Editar"
                            title="Editar avaliação do aluno"
                            aria-label="Editar avaliação do aluno"
                            disabled={deletion.deleting}
                        />
                    ) : null}
                    {can.delete ? (
                        <Button
                            tone="dark-red"
                            leftIconClass="fa fa-trash-o"
                            label="Excluir"
                            title="Excluir registro do aluno neste atendimento AEE"
                            aria-label="Excluir registro do aluno neste atendimento AEE"
                            disabled={deletion.deleting}
                            onClick={deletion.openConfirm}
                        />
                    ) : null}
                </FormActions>
            </div>
            <ConfirmActionModal
                open={deletion.open}
                title="Excluir Registro do Aluno"
                message="Excluir apenas o registro deste aluno neste atendimento AEE?"
                processing={deletion.deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo registro do aluno..."
                confirmTone="dark-red"
                onClose={deletion.closeConfirm}
                onConfirm={deletion.remove}
            />
        </BusinessPage>
    );
}
