import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import { dateLabel, studentRecordUrl } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import AeeEvaluationFields from "@/pages/Inclusion/AeeRecords/AeeEvaluationFields";
import type {
    AeeEvaluationFormData,
    AeeEvaluationTextField,
    AeeRecordRecord,
    AeeStudentEvaluationRecord,
} from "@/pages/Inclusion/AeeRecords/AeeRecord.types";
import {
    aeeBase,
    aeeRecordShowUrl,
    evaluationFromRecord,
    evaluationPayload,
} from "@/pages/Inclusion/AeeRecords/AeeRecord.utils";
import "@/pages/Inclusion/AeeRecords/AeeRecords.css";

type StudentEvaluationFormData = AeeEvaluationFormData & {
    evaluation_id: number;
};

function TextValue({ value }: { value?: string | null }): React.JSX.Element {
    return (
        <p className="aeeRecordForm__text">
            {value || "N/A"}
        </p>
    );
}

export default function InclusionStudentsAeeRecordsEditPage({
    student,
    evaluation,
}: {
    student: { id: number };
    evaluation: AeeStudentEvaluationRecord & {
        aee_record: AeeRecordRecord;
    };
}): React.JSX.Element {
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);
    const aeeRecord = evaluation.aee_record;
    const session = aeeRecord.attendance_session;
    const studentName = evaluation.student?.person?.name ?? `Aluno #${student.id}`;
    const studentAeeBase = `${aeeBase}/students/${student.id}/aee-records`;
    const form = useForm<StudentEvaluationFormData>({
        evaluation_id: evaluation.id,
        ...evaluationFromRecord(evaluation),
    });
    const errors = form.errors as Record<string, string | undefined>;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        form.transform((data) => evaluationPayload(data));
        form.put(`${studentAeeBase}/${evaluation.id}`, {
            preserveScroll: true,
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
                    text: "Não foi possível salvar a avaliação do aluno. Tente novamente.",
                });
                return false;
            },
            onError: () => {
                requestAnimationFrame(() => {
                    element.current
                        ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                        ?.focus();
                });
            },
            onFinish: () => {
                busy.current = false;
            },
        });
    }

    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Alunos", href: `${aeeBase}/students` },
        { label: "Aluno", href: `${aeeBase}/students/${student.id}/show` },
        { label: "Registros de atendimento AEE", href: studentAeeBase },
        { label: "Visualizar", href: `${studentAeeBase}/${evaluation.id}/show` },
        { label: "Editar" },
    ];

    return (
        <BusinessPage
            title="Editar Avaliação do Aluno"
            subtitle={`${studentName} • Registro #${aeeRecord.id} • Agendamento #${aeeRecord.attendance_session_id}`}
            breadcrumbItems={breadcrumbItems}
            className="aeeRecordPage"
        >
            <FormLayout
                ref={element}
                className="aeeRecordForm"
                aria-label="Editar avaliação do aluno"
                aria-busy={form.processing}
                onSubmit={submit}
            >
                <CardBox>
                    <FormSection
                        id="student-aee-session"
                        title="Informações do atendimento AEE"
                        variant="inline"
                        iconClass="fa fa-calendar"
                    >
                        <dl className="aeeRecordForm__details aeeRecordForm__details--three">
                            <DetailField label="Data">
                                {dateLabel(session?.session_date)}
                            </DetailField>
                            <DetailField label="Duração">
                                {aeeRecord.duration}
                            </DetailField>
                            <DetailField label="Profissional">
                                {session?.professional?.person?.name ?? "Não informado"}
                            </DetailField>
                        </dl>
                        <dl className="aeeRecordForm__details">
                            <DetailField label="Objetivo do Atendimento" wide>
                                <TextValue value={session?.session_objective} />
                            </DetailField>
                            <DetailField label="Atividades Realizadas" wide>
                                <TextValue value={aeeRecord.activities_performed} />
                            </DetailField>
                            <DetailField label="Estratégias Utilizadas">
                                <TextValue value={aeeRecord.strategies_used} />
                            </DetailField>
                            <DetailField label="Recursos Utilizados">
                                <TextValue value={aeeRecord.resources_used} />
                            </DetailField>
                            <DetailField label="Observações Gerais" wide>
                                <TextValue value={aeeRecord.general_observations} />
                            </DetailField>
                        </dl>
                    </FormSection>
                    <FormSection
                        id="student-aee-evaluation"
                        title="Editar Avaliação Individual"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <AeeEvaluationFields
                            idPrefix="student-aee-evaluation"
                            values={form.data}
                            errorFor={(field) => errors[field]}
                            disabled={form.processing}
                            onPresenceChange={(present) => form.setData("is_present", present)}
                            onFieldChange={(field: AeeEvaluationTextField, value) => form.setData(field, value)}
                        />
                    </FormSection>
                </CardBox>
                <FormActions
                    secondary={(
                        <Button
                            href={aeeRecordShowUrl(aeeRecord.id)}
                            tone="info"
                            leftIconClass="fa fa-clone"
                            label="Ver registro completo"
                            title="Ver registro completo do atendimento AEE"
                            aria-label="Ver registro completo do atendimento AEE"
                            disabled={form.processing}
                        />
                    )}
                    status={form.processing ? "Salvando avaliação do aluno..." : undefined}
                >
                    <Button
                        href={studentRecordUrl(student.id, "aee-attendance")}
                        tone="cancel"
                        label="Cancelar"
                        title="Cancelar e voltar para o prontuário do aluno"
                        aria-label="Cancelar e voltar para o prontuário do aluno"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title="Salvar avaliação do aluno"
                        aria-label={form.processing
                            ? "Salvando avaliação do aluno"
                            : "Salvar avaliação do aluno"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
