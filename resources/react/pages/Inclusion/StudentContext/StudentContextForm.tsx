import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import { aeeBase, studentRecordUrl } from "@/components/Students/Students.utils";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type {
    StudentContextFormProps,
    StudentContextOptionKey,
    StudentContextRecord,
} from "./StudentContext.types";
import StudentContextPage from "./StudentContextPage";
import "./StudentContext.css";

type TextFieldName =
    | "history"
    | "specific_educational_needs"
    | "knowledge"
    | "difficulties"
    | "learning_observations"
    | "behavior_notes"
    | "needs_mobility_support"
    | "needs_communication_support"
    | "needs_pedagogical_adaptation"
    | "uses_assistive_technology"
    | "medical_notes";

type SelectFieldName =
    | "learning_level"
    | "attention_level"
    | "memory_level"
    | "reasoning_level"
    | "communication_type"
    | "interaction_level"
    | "socialization_level"
    | "autonomy_level";

type BooleanFieldName =
    | "shows_aggressive_behavior"
    | "shows_withdrawn_behavior"
    | "has_medical_report"
    | "uses_medication";

type StudentContextFormData = Record<TextFieldName | SelectFieldName, string>
    & Record<BooleanFieldName, boolean>;

export default function StudentContextForm({
    student,
    studentProfile,
    studentContextOptions,
    studentContext,
}: StudentContextFormProps & {
    studentContext?: StudentContextRecord;
}): React.JSX.Element {
    const editing = Boolean(studentContext);
    const text = (value: string | null | undefined): string => value ?? "";
    const form = useForm<StudentContextFormData>({
        history: text(studentContext?.history),
        specific_educational_needs: text(studentContext?.specific_educational_needs),
        knowledge: text(studentContext?.knowledge),
        difficulties: text(studentContext?.difficulties),
        learning_level: text(studentContext?.learning_level),
        attention_level: text(studentContext?.attention_level),
        memory_level: text(studentContext?.memory_level),
        reasoning_level: text(studentContext?.reasoning_level),
        learning_observations: text(studentContext?.learning_observations),
        communication_type: text(studentContext?.communication_type),
        interaction_level: text(studentContext?.interaction_level),
        socialization_level: text(studentContext?.socialization_level),
        shows_aggressive_behavior: Boolean(studentContext?.shows_aggressive_behavior),
        shows_withdrawn_behavior: Boolean(studentContext?.shows_withdrawn_behavior),
        behavior_notes: text(studentContext?.behavior_notes),
        autonomy_level: text(studentContext?.autonomy_level),
        needs_mobility_support: text(studentContext?.needs_mobility_support),
        needs_communication_support: text(studentContext?.needs_communication_support),
        needs_pedagogical_adaptation: text(studentContext?.needs_pedagogical_adaptation),
        uses_assistive_technology: text(studentContext?.uses_assistive_technology),
        has_medical_report: Boolean(studentContext?.has_medical_report),
        uses_medication: Boolean(studentContext?.uses_medication),
        medical_notes: text(studentContext?.medical_notes),
    });
    const errors = form.errors as Partial<Record<keyof StudentContextFormData, string>>;
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);
    const cancelUrl = studentRecordUrl(student.id, "current-context");
    const fieldId = (name: string): string => `student-context-${name.replace(/_/g, "-")}`;

    const textArea = (
        name: TextFieldName,
        label: string,
        options: { rows?: number; placeholder?: string; required?: boolean } = {},
    ): React.JSX.Element => {
        const id = fieldId(name);

        return (
            <FormField
                id={id}
                label={label}
                error={errors[name]}
                requiredIndicator={options.required}
            >
                <TextArea
                    id={id}
                    name={name}
                    rows={options.rows ?? 3}
                    required={options.required}
                    placeholder={options.placeholder}
                    value={form.data[name]}
                    aria-invalid={!!errors[name]}
                    aria-describedby={errors[name] ? `${id}-error` : undefined}
                    onChange={(event) => form.setData(name, event.target.value)}
                />
            </FormField>
        );
    };

    const select = (
        name: SelectFieldName,
        label: string,
        optionsKey: StudentContextOptionKey,
    ): React.JSX.Element => {
        const id = fieldId(name);
        const options = Object.entries(studentContextOptions[optionsKey] ?? {}).map(
            ([value, optionLabel]) => ({ value, label: optionLabel }),
        );

        return (
            <FormField
                id={id}
                label={label}
                error={errors[name]}
            >
                <SelectField
                    id={id}
                    name={name}
                    label={label}
                    options={options}
                    value={form.data[name]}
                    disabled={form.processing}
                    aria-invalid={!!errors[name]}
                    aria-describedby={errors[name] ? `${id}-error` : undefined}
                    onChange={(event) => form.setData(name, event.target.value)}
                />
            </FormField>
        );
    };

    const toggle = (
        name: BooleanFieldName,
        label: string,
        description: string,
    ): React.JSX.Element => {
        const id = fieldId(name);

        return (
            <div className="studentContextForm__toggle">
                <ToggleControl
                    id={id}
                    name={name}
                    size="small"
                    label={label}
                    description={description}
                    checked={form.data[name]}
                    disabled={form.processing}
                    onChange={(checked) => form.setData(name, checked)}
                />
                {errors[name] ? (
                    <span id={`${id}-error`} className="formField__error">
                        {errors[name]}
                    </span>
                ) : null}
            </div>
        );
    };

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        const options = {
            preserveScroll: true,
            onError: () => requestAnimationFrame(() => {
                element.current
                    ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                    ?.focus();
            }),
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
                    text: editing
                        ? "Não foi possível salvar o contexto do aluno. Tente novamente."
                        : "Não foi possível cadastrar o contexto do aluno. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (studentContext) {
            form.put(`${aeeBase}/student-context/${studentContext.id}`, options);
        } else {
            form.post(`${aeeBase}/student-context/${student.id}/store`, options);
        }
    }

    return (
        <StudentContextPage
            title={editing ? "Editar Contexto Educacional" : "Novo Contexto Educacional"}
            subtitle={editing
                ? `Atualize as observações sobre o comportamento e desenvolvimento do aluno(a) ${studentProfile.name}.`
                : `Registre um novo contexto com observações atuais sobre o comportamento do aluno(a) ${studentProfile.name}.`}
            studentId={student.id}
            studentName={studentProfile.name}
            contextId={studentContext?.id}
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="student-context-identification"
                            title="Identificação do Aluno"
                            variant="inline"
                            iconClass="fa fa-user"
                        >
                            <div className="studentContextForm__profile">
                                <img
                                    src={studentProfile.photo_url}
                                    alt={`Foto de ${studentProfile.name}`}
                                    className="studentContextForm__photo"
                                />
                                <div className="studentContextForm__profileText">
                                    <strong>{studentProfile.name}</strong>
                                    <span>Matrícula: {studentProfile.registration || "Não informado"}</span>
                                    <span>
                                        <TableLabel tone={studentProfile.status_color || "inverse"}>
                                            {studentProfile.status_label || "Não informado"}
                                        </TableLabel>
                                    </span>
                                </div>
                            </div>
                            <ul
                                className="studentContextForm__deficiencies"
                                aria-label="Perfis de atendimento"
                            >
                                {studentProfile.deficiencies.length > 0 ? (
                                    studentProfile.deficiencies.map((deficiency) => (
                                        <li
                                            key={deficiency.id}
                                            className="studentContextForm__deficiency"
                                        >
                                            <strong>{deficiency.name || "Não informado"}</strong>
                                            <span>
                                                GRAU: {deficiency.severity_label ?? deficiency.severity ?? "Não informado"}
                                            </span>
                                        </li>
                                    ))
                                ) : (
                                    <li className="studentContextForm__deficiency studentContextForm__deficiency--empty">
                                        Nenhum perfil de atendimento registrado para {studentProfile.name || "este aluno"}.
                                    </li>
                                )}
                            </ul>
                        </FormSection>

                        <FormSection
                            id="student-context-history"
                            title="Histórico e Necessidades Educacionais"
                            variant="inline"
                            iconClass="fa fa-history"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    {textArea("history", "Histórico do Aluno", {
                                        required: true,
                                        placeholder: "Descreva o histórico educacional do aluno",
                                    })}
                                </FormGridWide>
                                <FormGridWide>
                                    {textArea("specific_educational_needs", "Necessidades Educacionais Específicas", {
                                        required: true,
                                        placeholder: "Descreva as necessidades educacionais específicas",
                                    })}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>

                        <FormSection
                            id="student-context-synthesis"
                            title="Síntese Avaliativa"
                            variant="inline"
                            iconClass="fa fa-clipboard"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    {textArea("knowledge", "Conhecimentos e Interesses", {
                                        rows: 4,
                                        required: true,
                                        placeholder: "O que sabe? Do que gosta/afinidades?",
                                    })}
                                </FormGridWide>
                                <FormGridWide>
                                    {textArea("difficulties", "Dificuldades", {
                                        rows: 4,
                                        required: true,
                                        placeholder: "Principais dificuldades observadas",
                                    })}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>

                        <FormSection
                            id="student-context-learning"
                            title="Aprendizagem e Cognição"
                            variant="inline"
                            iconClass="fa fa-graduation-cap"
                        >
                            <FormGrid columns={4}>
                                {select("learning_level", "Nível de Aprendizagem", "learningLevels")}
                                {select("attention_level", "Nível de Atenção", "attentionLevels")}
                                {select("memory_level", "Nível de Memória", "memoryLevels")}
                                {select("reasoning_level", "Nível de Raciocínio", "reasoningLevels")}
                                <FormGridWide>
                                    {textArea("learning_observations", "Observações de Aprendizagem", {
                                        placeholder: "Observações pedagógicas relevantes",
                                    })}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>

                        <FormSection
                            id="student-context-behavior"
                            title="Comunicação e Comportamento"
                            variant="inline"
                            iconClass="fa fa-comments"
                        >
                            <FormGrid columns={3}>
                                {select("communication_type", "Tipo de Comunicação", "communicationTypes")}
                                {select("interaction_level", "Nível de Interação", "interactionLevels")}
                                {select("socialization_level", "Nível de Socialização", "socializationLevels")}
                            </FormGrid>
                            <FormGrid columns={2}>
                                {toggle(
                                    "shows_aggressive_behavior",
                                    "Comportamento agressivo",
                                    "Marque se o aluno apresenta comportamento agressivo.",
                                )}
                                {toggle(
                                    "shows_withdrawn_behavior",
                                    "Comportamento retraído",
                                    "Marque se o aluno apresenta comportamento retraído.",
                                )}
                                <FormGridWide>
                                    {textArea("behavior_notes", "Notas Comportamentais", {
                                        placeholder: "Observações comportamentais",
                                    })}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>

                        <FormSection
                            id="student-context-autonomy"
                            title="Autonomia e Apoios"
                            variant="inline"
                            iconClass="fa fa-life-ring"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    {select("autonomy_level", "Nível de Autonomia", "autonomyLevels")}
                                </FormGridWide>
                                {textArea("needs_mobility_support", "Apoio de Mobilidade", {
                                    placeholder: "Descreva os apoios de mobilidade necessários",
                                })}
                                {textArea("needs_communication_support", "Apoio de Comunicação", {
                                    placeholder: "Descreva os apoios de comunicação necessários",
                                })}
                                {textArea("needs_pedagogical_adaptation", "Adaptação Pedagógica", {
                                    placeholder: "Descreva as adaptações pedagógicas necessárias",
                                })}
                                {textArea("uses_assistive_technology", "Tecnologia Assistiva", {
                                    placeholder: "Descreva as tecnologias assistivas utilizadas",
                                })}
                            </FormGrid>
                        </FormSection>

                        <FormSection
                            id="student-context-health"
                            title="Saúde"
                            variant="inline"
                            iconClass="fa fa-heartbeat"
                        >
                            <FormGrid columns={2}>
                                {toggle(
                                    "has_medical_report",
                                    "Possui laudo médico",
                                    "Marque se o aluno possui laudo médico.",
                                )}
                                {toggle(
                                    "uses_medication",
                                    "Usa medicação",
                                    "Marque se o aluno faz uso de medicação.",
                                )}
                                <FormGridWide>
                                    {textArea("medical_notes", "Observações Médicas", {
                                        placeholder: "Informações médicas relevantes",
                                    })}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando contexto do aluno..." : "Cadastrando contexto do aluno...")
                        : undefined}
                >
                    <Button
                        href={cancelUrl}
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
                        title="Salvar contexto do aluno"
                        aria-label={form.processing ? "Salvando contexto do aluno" : "Salvar contexto do aluno"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </StudentContextPage>
    );
}
