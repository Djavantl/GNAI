import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import type { PageBreadcrumbItem } from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide, FormStack } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DetailField from "@/components/UI/DetailField/DetailField";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    PedagogicalRecordFormContext,
    PedagogicalRecordFormRecord,
} from "./PedagogicalRecordForm.types";
import "./PedagogicalRecordForm.css";

const aeeBase = "/specialized-educational-support";
const recordsBase = `${aeeBase}/pedagogical-records`;

type PedagogicalRecordFormData = {
    with_guardians: boolean;
    guardian_ids: number[];
    follow_up_reason: string;
    duration: string;
    is_present: boolean;
    absence_reason: string;
    systematic_pedagogical_follow_up_record: string;
    strategies_and_resources_adopted: string;
    referrals_made: string;
    complementary_observations: string;
};

type PedagogicalRecordTextField = {
    [Key in keyof PedagogicalRecordFormData]: PedagogicalRecordFormData[Key] extends string ? Key : never;
}[keyof PedagogicalRecordFormData];

type PedagogicalContentField =
    | "systematic_pedagogical_follow_up_record"
    | "strategies_and_resources_adopted"
    | "referrals_made"
    | "complementary_observations";

const pedagogicalContentFields: PedagogicalContentField[] = [
    "systematic_pedagogical_follow_up_record",
    "strategies_and_resources_adopted",
    "referrals_made",
    "complementary_observations",
];

const clearedPedagogicalContent: Record<PedagogicalContentField, string> = {
    systematic_pedagogical_follow_up_record: "",
    strategies_and_resources_adopted: "",
    referrals_made: "",
    complementary_observations: "",
};

export default function PedagogicalRecordForm({
    context,
    record,
    returnStudentId,
}: {
    context: PedagogicalRecordFormContext;
    record?: PedagogicalRecordFormRecord;
    returnStudentId?: number;
}): React.JSX.Element {
    const editing = Boolean(record);
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);
    const guardians = context.guardians;
    const hasGuardians = guardians.length > 0;
    const sessionHref = `${aeeBase}/sessions/${context.session_id}/show`;
    const recordHref = record ? `${recordsBase}/${record.id}/show` : undefined;
    const cancelHref = record
        ? `${recordHref}${returnStudentId ? `?student_id=${returnStudentId}` : ""}`
        : sessionHref;

    const form = useForm<PedagogicalRecordFormData>({
        with_guardians: (record?.guardians?.length ?? 0) > 0,
        guardian_ids: record?.guardians?.map((guardian) => guardian.id) ?? [],
        follow_up_reason: record?.follow_up_reason ?? context.session_objective ?? "",
        duration: record?.duration ?? "",
        is_present: record?.is_present ?? true,
        absence_reason: record?.absence_reason ?? "",
        systematic_pedagogical_follow_up_record: record?.systematic_pedagogical_follow_up_record ?? "",
        strategies_and_resources_adopted: record?.strategies_and_resources_adopted ?? "",
        referrals_made: record?.referrals_made ?? "",
        complementary_observations: record?.complementary_observations ?? "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const guardianError = errors.guardian_ids
        ?? Object.entries(errors).find(([key]) => key.startsWith("guardian_ids."))?.[1];
    const showAbsence = !form.data.is_present;
    const showPedagogicalContent = form.data.is_present || form.data.with_guardians;
    const describedBy = (name: string, id: string): string | undefined =>
        errors[name] ? `${id}-error` : undefined;

    const breadcrumbItems: PageBreadcrumbItem[] = [
        { label: "Home", href: "/auth/dashboard" },
        { label: "Agendamentos", href: `${aeeBase}/sessions` },
        { label: `Agendamento #${context.session_id}`, href: sessionHref },
        { label: "Atendimento Pedagógico", href: recordHref },
    ];

    if (editing) breadcrumbItems.push({ label: "Editar" });

    function updatePresence(isPresent: boolean): void {
        form.setData((current) => ({
            ...current,
            is_present: isPresent,
            absence_reason: isPresent ? "" : current.absence_reason,
            ...(!isPresent && !current.with_guardians ? clearedPedagogicalContent : {}),
        }));
    }

    function updateWithGuardians(withGuardians: boolean): void {
        form.setData((current) => ({
            ...current,
            with_guardians: withGuardians,
            guardian_ids: withGuardians ? current.guardian_ids : [],
            ...(!withGuardians && !current.is_present ? clearedPedagogicalContent : {}),
        }));
    }

    function toggleGuardian(id: number, checked: boolean): void {
        form.setData((current) => ({
            ...current,
            guardian_ids: checked
                ? [...current.guardian_ids, id]
                : current.guardian_ids.filter((value) => value !== id),
        }));
    }

    function focusFirstError(): void {
        window.requestAnimationFrame(() => {
            element.current
                ?.querySelector<HTMLElement>("[aria-invalid='true']")
                ?.focus();
        });
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;

        busy.current = true;
        // Mantém o payload do Blade: campos ocultos/desabilitados não são enviados.
        form.transform((data) => {
            const payload: Record<string, unknown> = {
                with_guardians: data.with_guardians,
                follow_up_reason: data.follow_up_reason,
                duration: data.duration,
                is_present: data.is_present,
            };

            if (!record) payload.attendance_session_id = context.session_id;
            if (data.with_guardians) payload.guardian_ids = data.guardian_ids;
            if (!data.is_present) payload.absence_reason = data.absence_reason;
            if (data.is_present || data.with_guardians) {
                pedagogicalContentFields.forEach((field) => {
                    payload[field] = data[field];
                });
            }

            return payload;
        });

        const options = {
            preserveScroll: true,
            onError: focusFirstError,
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
                    text: "Não foi possível salvar o atendimento pedagógico. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (record) {
            // A origem no prontuário segue pela URL, para o redirect após salvar voltar a ela.
            form.put(`${recordsBase}/${record.id}${returnStudentId ? `?student_id=${returnStudentId}` : ""}`, options);
        } else {
            form.post(`${recordsBase}/store`, options);
        }
    }

    function textAreaField({
        name,
        id,
        label,
        rows,
        placeholder,
        required = false,
    }: {
        name: PedagogicalRecordTextField;
        id: string;
        label: string;
        rows: number;
        placeholder: string;
        required?: boolean;
    }): React.JSX.Element {
        return (
            <FormField
                id={id}
                label={label}
                requiredIndicator={required}
                error={errors[name]}
            >
                <TextArea
                    id={id}
                    name={name}
                    rows={rows}
                    required={required}
                    placeholder={placeholder}
                    value={form.data[name]}
                    aria-invalid={!!errors[name] || undefined}
                    aria-describedby={describedBy(name, id)}
                    onChange={(event) => form.setData(name, event.target.value)}
                />
            </FormField>
        );
    }

    return (
        <BusinessPage
            title={editing ? "Editar Atendimento Pedagógico" : "Novo Atendimento Pedagógico"}
            subtitle={`Agendamento #${context.session_id}`}
            breadcrumbItems={breadcrumbItems}
            className="pedagogicalRecordPage"
        >
            <FormLayout
                ref={element}
                className="pedagogicalRecordForm"
                aria-label={editing ? "Editar atendimento pedagógico" : "Cadastrar atendimento pedagógico"}
                aria-busy={form.processing}
                onSubmit={submit}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="pedagogical-record-identification"
                            title="Identificação do Estudante"
                            variant="inline"
                            iconClass="fa fa-user"
                        >
                            <dl className="pedagogicalRecordForm__details">
                                <DetailField label="Aluno">
                                    {context.student_name ?? "Aluno não informado"}
                                </DetailField>
                                <DetailField label="Profissional">
                                    {context.professional_name ?? "Profissional não informado"}
                                </DetailField>
                                <DetailField label="Data">
                                    {context.session_date ?? "Não informado"}
                                </DetailField>
                                <DetailField label="Horário">
                                    {`${context.start_time} às ${context.end_time ?? "--:--"}`}
                                </DetailField>
                                <DetailField label="Tipo">
                                    {context.attendance_type_label}
                                </DetailField>
                            </dl>
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    <FormStack>
                                        <ToggleControl
                                            id="pedagogical-record-with-guardians"
                                            name="with_guardians"
                                            size="small"
                                            label="Atendimento realizado com responsável pelo aluno"
                                            description={hasGuardians
                                                ? "Marque para selecionar os responsáveis que participaram do atendimento."
                                                : "O aluno não possui responsáveis cadastrados no sistema."}
                                            checked={form.data.with_guardians}
                                            disabled={!hasGuardians || form.processing}
                                            onChange={updateWithGuardians}
                                        />
                                    </FormStack>
                                </FormGridWide>
                                {form.data.with_guardians ? (
                                    <FormGridWide>
                                        <FormField
                                            id="pedagogical-record-guardians"
                                            label="Responsáveis participantes"
                                            error={guardianError}
                                        >
                                            <ChoiceGroup
                                                id="pedagogical-record-guardians"
                                                label="Responsáveis participantes"
                                                className="pedagogicalRecordForm__guardians"
                                                invalid={!!guardianError}
                                                describedBy={guardianError ? "pedagogical-record-guardians-error" : undefined}
                                            >
                                                {guardians.map((guardian) => (
                                                    <ChoiceControl
                                                        key={guardian.id}
                                                        type="checkbox"
                                                        tone="purple"
                                                        id={`pedagogical-record-guardian-${guardian.id}`}
                                                        name="guardian_ids[]"
                                                        label={(
                                                            <>
                                                                <strong>{guardian.name}</strong>
                                                                <span className="pedagogicalRecordForm__relationship">
                                                                    {", "}{guardian.relationship_label}
                                                                </span>
                                                            </>
                                                        )}
                                                        aria-label={`${guardian.name}, ${guardian.relationship_label}`}
                                                        checked={form.data.guardian_ids.includes(guardian.id)}
                                                        disabled={form.processing}
                                                        onChange={(checked) => toggleGuardian(guardian.id, checked)}
                                                    />
                                                ))}
                                            </ChoiceGroup>
                                        </FormField>
                                    </FormGridWide>
                                ) : null}
                            </FormGrid>
                        </FormSection>
                        <FormSection
                            id="pedagogical-record-follow-up"
                            title="Acompanhamento Pedagógico"
                            variant="inline"
                            iconClass="fa fa-book"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    {textAreaField({
                                        name: "follow_up_reason",
                                        id: "pedagogical-record-follow-up-reason",
                                        label: "Motivo do Acompanhamento",
                                        rows: 3,
                                        placeholder: "Informe o motivo do acompanhamento pedagógico...",
                                        required: true,
                                    })}
                                </FormGridWide>
                                <FormGridWide>
                                    <FormField
                                        id="pedagogical-record-duration"
                                        label="Período/Duração do Acompanhamento"
                                        requiredIndicator
                                        error={errors.duration}
                                    >
                                        <TextInput
                                            id="pedagogical-record-duration"
                                            name="duration"
                                            required
                                            maxLength={50}
                                            placeholder="Ex: 50 minutos ou 01:00"
                                            value={form.data.duration}
                                            invalid={!!errors.duration}
                                            aria-describedby={describedBy("duration", "pedagogical-record-duration")}
                                            onChange={(event) => form.setData("duration", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormGridWide>
                                    <FormStack>
                                        <ToggleControl
                                            id="pedagogical-record-is-present"
                                            name="is_present"
                                            size="small"
                                            label="Presença do Estudante no Atendimento"
                                            checked={form.data.is_present}
                                            disabled={form.processing}
                                            onChange={updatePresence}
                                        />
                                    </FormStack>
                                </FormGridWide>
                                {showAbsence ? (
                                    <FormGridWide>
                                        {textAreaField({
                                            name: "absence_reason",
                                            id: "pedagogical-record-absence-reason",
                                            label: "Motivo da Ausência",
                                            rows: 2,
                                            placeholder: "Informe o motivo da ausência do aluno...",
                                            required: true,
                                        })}
                                    </FormGridWide>
                                ) : null}
                                {showPedagogicalContent ? (
                                    <>
                                        <FormGridWide>
                                            {textAreaField({
                                                name: "systematic_pedagogical_follow_up_record",
                                                id: "pedagogical-record-systematic-follow-up",
                                                label: "Registro do Acompanhamento Pedagógico Sistemático",
                                                rows: 4,
                                                placeholder: "Registre o acompanhamento pedagógico sistemático realizado...",
                                                required: true,
                                            })}
                                        </FormGridWide>
                                        <FormGridWide>
                                            {textAreaField({
                                                name: "strategies_and_resources_adopted",
                                                id: "pedagogical-record-strategies",
                                                label: "Estratégias e Recursos Adotados (quando necessário)",
                                                rows: 3,
                                                placeholder: "Descreva as estratégias e os recursos adotados, quando necessário...",
                                            })}
                                        </FormGridWide>
                                        <FormGridWide>
                                            {textAreaField({
                                                name: "referrals_made",
                                                id: "pedagogical-record-referrals",
                                                label: "Encaminhamentos Realizados",
                                                rows: 3,
                                                placeholder: "Informe os encaminhamentos realizados...",
                                            })}
                                        </FormGridWide>
                                        <FormGridWide>
                                            {textAreaField({
                                                name: "complementary_observations",
                                                id: "pedagogical-record-observations",
                                                label: "Observações Complementares",
                                                rows: 3,
                                                placeholder: "Registre informações complementares relevantes...",
                                            })}
                                        </FormGridWide>
                                    </>
                                ) : null}
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações…" : "Salvando atendimento…")
                        : undefined}
                >
                    <Button
                        href={cancelHref}
                        tone="cancel"
                        label="Cancelar"
                        aria-label="Cancelar e voltar"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        label="Salvar"
                        title="Salvar atendimento pedagógico"
                        aria-label="Salvar atendimento pedagógico"
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
