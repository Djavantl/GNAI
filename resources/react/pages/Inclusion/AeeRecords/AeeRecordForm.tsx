import { useMemo, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import Tabs, { type TabItem } from "@/components/Navigation/Tabs/Tabs";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import AeeEvaluationFields from "./AeeEvaluationFields";
import type {
    AeeEvaluationFormData,
    AeeEvaluationTextField,
    AeeRecordRecord,
    AeeSession,
} from "./AeeRecord.types";
import {
    aeeBase,
    aeeRecordShowUrl,
    emptyEvaluation,
    evaluationFromRecord,
    evaluationPayload,
    sessionShowUrl,
} from "./AeeRecord.utils";
import "./AeeRecords.css";

type GeneralField =
    | "duration"
    | "activities_performed"
    | "strategies_used"
    | "resources_used"
    | "general_observations";

interface AeeRecordFormData {
    attendance_session_id: number;
    duration: string;
    activities_performed: string;
    strategies_used: string;
    resources_used: string;
    general_observations: string;
    evaluations: AeeEvaluationFormData[];
}

interface StudentTab {
    key: string;
    studentId: number;
    name: string;
}

export default function AeeRecordForm({
    session,
    aeeRecord,
}: {
    session: AeeSession;
    aeeRecord?: AeeRecordRecord;
}): React.JSX.Element {
    const editing = Boolean(aeeRecord);
    const sessionId = aeeRecord?.attendance_session_id ?? session.id;
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);

    const students: StudentTab[] = useMemo(() => (
        aeeRecord
            ? (aeeRecord.student_evaluations ?? []).map((evaluation) => ({
                key: `student-${evaluation.student_id}`,
                studentId: evaluation.student_id,
                name: evaluation.student?.person?.name ?? `Aluno #${evaluation.student_id}`,
            }))
            : (session.students ?? []).map((student) => ({
                key: `student-${student.id}`,
                studentId: student.id,
                name: student.person?.name ?? `Aluno #${student.id}`,
            }))
    ), [aeeRecord, session.students]);

    const form = useForm<AeeRecordFormData>({
        attendance_session_id: sessionId,
        duration: aeeRecord?.duration ?? "",
        activities_performed: aeeRecord?.activities_performed ?? "",
        strategies_used: aeeRecord?.strategies_used ?? "",
        resources_used: aeeRecord?.resources_used ?? "",
        general_observations: aeeRecord?.general_observations ?? "",
        evaluations: aeeRecord
            ? (aeeRecord.student_evaluations ?? []).map(evaluationFromRecord)
            : (session.students ?? []).map((student) => emptyEvaluation(student.id)),
    });
    const errors = form.errors as Record<string, string | undefined>;
    const [tabFocus, setTabFocus] = useState<{ id?: string; version: number }>({
        id: students[0]?.key,
        version: 0,
    });

    const cancelHref = aeeRecord ? aeeRecordShowUrl(aeeRecord.id) : sessionShowUrl(session.id);
    const evaluationErrorFor = (index: number, field: string): string | undefined => (
        errors[`evaluations.${index}.${field}`]
    );
    const evaluationHasErrors = (index: number): boolean => (
        Object.keys(errors).some((key) => key.startsWith(`evaluations.${index}.`) && errors[key])
    );
    const collectionErrors = Object.entries(errors)
        .filter(([key, message]) => message && (
            key === "evaluations" || /^evaluations\.\d+\.student_id$/.test(key)
        ))
        .map(([, message]) => message as string);

    function updateEvaluation(
        index: number,
        changes: Partial<AeeEvaluationFormData>,
    ): void {
        form.setData((data) => ({
            ...data,
            evaluations: data.evaluations.map((evaluation, position) => (
                position === index ? { ...evaluation, ...changes } : evaluation
            )),
        }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        form.transform((data) => ({
            ...data,
            evaluations: data.evaluations.map(evaluationPayload),
        }));

        const options = {
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
                    text: "Não foi possível salvar o registro do atendimento AEE. Tente novamente.",
                });
                return false;
            },
            onError: (received: Record<string, string>) => {
                const invalidIndex = form.data.evaluations.findIndex((_, index) => (
                    Object.keys(received).some((key) => key.startsWith(`evaluations.${index}.`))
                ));
                const generalInvalid = Object.keys(received).some((key) => !key.startsWith("evaluations"));

                if (!generalInvalid && invalidIndex >= 0 && students[invalidIndex]) {
                    const id = students[invalidIndex].key;
                    setTabFocus((current) => ({ id, version: current.version + 1 }));
                }

                requestAnimationFrame(() => {
                    element.current
                        ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                        ?.focus();
                });
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (aeeRecord) {
            form.put(`${aeeBase}/aee-records/${aeeRecord.id}`, options);
        } else {
            form.post(`${aeeBase}/aee-records/store`, options);
        }
    }

    const renderGeneralTextArea = (
        field: GeneralField,
        label: string,
        placeholder: string,
        rows: number,
        required = false,
    ): React.JSX.Element => {
        const id = `aee-record-${field.replace(/_/g, "-")}`;

        return (
            <FormField
                id={id}
                label={label}
                requiredIndicator={required}
                error={errors[field]}
            >
                <TextArea
                    id={id}
                    name={field}
                    rows={rows}
                    required={required}
                    placeholder={placeholder}
                    value={form.data[field]}
                    aria-invalid={!!errors[field]}
                    aria-describedby={errors[field] ? `${id}-error` : undefined}
                    disabled={form.processing}
                    onChange={(event) => form.setData(field, event.target.value)}
                />
            </FormField>
        );
    };

    const tabs: TabItem[] = students.map((student, index) => {
        const evaluation = form.data.evaluations[index];
        const absent = evaluation ? !evaluation.is_present : false;
        const invalid = evaluationHasErrors(index);
        const states = [
            absent ? "ausente" : null,
            invalid ? "com campos a corrigir" : null,
        ].filter(Boolean);

        return {
            id: student.key,
            accessibleLabel: states.length ? `${student.name} (${states.join(", ")})` : undefined,
            label: (
                <span className="aeeRecordForm__tabLabel">
                    <span>{student.name}</span>
                    {absent ? (
                        <i className="fa fa-user-times aeeRecordForm__tabIcon--danger" aria-hidden="true" />
                    ) : null}
                    {invalid ? (
                        <i className="fa fa-exclamation-circle aeeRecordForm__tabIcon--danger" aria-hidden="true" />
                    ) : null}
                </span>
            ),
            content: evaluation ? (
                <div className="aeeRecordForm__evaluation">
                    <h3 className="aeeRecordForm__evaluationTitle">
                        Avaliação: {student.name}
                    </h3>
                    <AeeEvaluationFields
                        idPrefix={`aee-evaluation-${index}`}
                        namePrefix={`evaluations[${index}]`}
                        values={evaluation}
                        errorFor={(field) => evaluationErrorFor(index, field)}
                        disabled={form.processing}
                        onPresenceChange={(present) => updateEvaluation(index, { is_present: present })}
                        onFieldChange={(field: AeeEvaluationTextField, value) => {
                            const changes: Partial<AeeEvaluationFormData> = {};
                            changes[field] = value;
                            updateEvaluation(index, changes);
                        }}
                    />
                </div>
            ) : null,
        };
    });

    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Agendamentos", href: `${aeeBase}/sessions` },
        { label: "Agendamento", href: sessionShowUrl(sessionId) },
    ];

    if (aeeRecord) {
        breadcrumbItems.push({ label: "Registro", href: aeeRecordShowUrl(aeeRecord.id) });
    }

    breadcrumbItems.push({ label: editing ? "Editar" : "Cadastrar" });

    return (
        <BusinessPage
            title={editing ? "Editar Registro de atendimento AEE" : "Novo Registro de atendimento AEE"}
            subtitle={aeeRecord
                ? `Registro #${aeeRecord.id} • Agendamento #${sessionId}`
                : `Agendamento #${session.id}`}
            breadcrumbItems={breadcrumbItems}
            className="aeeRecordPage"
        >
            <FormLayout
                ref={element}
                className="aeeRecordForm"
                aria-label={editing ? "Editar registro de atendimento AEE" : "Cadastrar registro de atendimento AEE"}
                aria-busy={form.processing}
                onSubmit={submit}
            >
                <CardBox>
                    <FormSection
                        id="aee-record-general"
                        title="Informações Gerais da Execução"
                        variant="inline"
                        iconClass="fa fa-clipboard"
                    >
                        <FormGrid columns={2}>
                            <FormField
                                id="aee-record-duration"
                                label="Duração"
                                requiredIndicator
                                error={errors.duration}
                            >
                                <TextInput
                                    id="aee-record-duration"
                                    name="duration"
                                    required
                                    maxLength={50}
                                    placeholder="Ex: 50 minutos ou 01:00"
                                    value={form.data.duration}
                                    invalid={!!errors.duration}
                                    aria-describedby={errors.duration ? "aee-record-duration-error" : undefined}
                                    disabled={form.processing}
                                    onChange={(event) => form.setData("duration", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                {renderGeneralTextArea(
                                    "activities_performed",
                                    "Atividades Planejadas/Realizadas",
                                    "Descreva as atividades realizadas durante o atendimento AEE...",
                                    3,
                                    true,
                                )}
                            </FormGridWide>
                            {renderGeneralTextArea(
                                "strategies_used",
                                "Estratégias Planejadas/Utilizadas",
                                "Métodos, abordagens pedagógicas ou intervenções utilizadas...",
                                2,
                            )}
                            {renderGeneralTextArea(
                                "resources_used",
                                "Recursos Planejados/Utilizados",
                                "Materiais, tecnologias ou instrumentos utilizados...",
                                2,
                            )}
                            <FormGridWide>
                                {renderGeneralTextArea(
                                    "general_observations",
                                    "Observações Gerais",
                                    "Observações relevantes sobre o atendimento AEE...",
                                    2,
                                )}
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="aee-record-evaluations"
                        title={editing ? "Editar Avaliação Individual" : "Avaliação Individual por Aluno"}
                        variant="inline"
                        iconClass="fa fa-users"
                    >
                        {collectionErrors.length ? (
                            <div className="aeeRecordForm__collectionErrors">
                                {collectionErrors.map((message) => (
                                    <p key={message} className="formField__error">
                                        {message}
                                    </p>
                                ))}
                            </div>
                        ) : null}
                        {tabs.length ? (
                            <div className="aeeRecordForm__tabs">
                                <Tabs
                                    key={tabFocus.version}
                                    items={tabs}
                                    initialActiveId={tabFocus.id}
                                    variant="vertical"
                                />
                            </div>
                        ) : (
                            <p className="aeeRecordForm__empty">
                                Nenhum aluno vinculado a este agendamento.
                            </p>
                        )}
                    </FormSection>
                </CardBox>
                <FormActions
                    status={form.processing ? "Salvando registro do atendimento AEE..." : undefined}
                >
                    <Button
                        href={cancelHref}
                        tone="cancel"
                        label="Cancelar"
                        title="Cancelar e voltar"
                        aria-label="Cancelar e voltar"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-check"
                        label="Salvar"
                        title="Salvar registro do atendimento AEE"
                        aria-label={form.processing
                            ? "Salvando registro do atendimento AEE"
                            : "Salvar registro do atendimento AEE"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
