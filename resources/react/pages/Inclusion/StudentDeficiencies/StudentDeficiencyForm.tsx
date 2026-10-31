import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import { studentRecordUrl, studentsBase } from "@/components/Students/Students.utils";
import type {
    DeficiencyOption,
    StudentDeficiencyRecord,
    StudentDeficiencyStudent,
} from "./StudentDeficiency.types";

type StudentDeficiencyFormData = {
    deficiency_id: string;
    severity: string;
    notes: string;
};

type StudentDeficiencyFormProps = { student: StudentDeficiencyStudent } & (
    | { mode: "create"; deficiencies: DeficiencyOption[]; studentDeficiency?: never }
    | { mode: "edit"; studentDeficiency: StudentDeficiencyRecord; deficiencies?: never }
);

const severityOptions = [
    { value: "mild", label: "Leve" },
    { value: "moderate", label: "Moderada" },
    { value: "severe", label: "Severa" },
];

export default function StudentDeficiencyForm(props: StudentDeficiencyFormProps): React.JSX.Element {
    const { student, studentDeficiency } = props;
    const form = useForm<StudentDeficiencyFormData>({
        deficiency_id: studentDeficiency ? String(studentDeficiency.deficiency_id) : "",
        severity: studentDeficiency?.severity ?? "",
        notes: studentDeficiency?.notes ?? "",
    });
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const errors = form.errors as Partial<Record<keyof StudentDeficiencyFormData, string>>;
    const cancelUrl = studentRecordUrl(student.id, "support-profiles");
    const base = `${studentsBase}/${student.id}/deficiencies`;
    const describedBy = (field: keyof StudentDeficiencyFormData, id: string) =>
        errors[field] ? `${id}-error` : undefined;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        const options = {
            preserveScroll: true,
            onError: () =>
                requestAnimationFrame(() =>
                    element.current
                        ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                        ?.focus(),
                ),
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
                    text: studentDeficiency
                        ? "Não foi possível atualizar o perfil de atendimento. Atualize a página e tente novamente."
                        : "Não foi possível vincular o perfil de atendimento. Atualize a página e tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (studentDeficiency) {
            form.put(`${base}/${studentDeficiency.id}`, options);
        } else {
            form.post(base, options);
        }
    }

    return (
        <FormLayout
            ref={element}
            aria-label={studentDeficiency ? "Editar perfil de atendimento do aluno" : "Vincular perfil de atendimento"}
            aria-busy={form.processing}
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="student-deficiency-information"
                        title={studentDeficiency ? "Atualizar Informações" : "Identificação do Perfil"}
                        variant="inline"
                        iconClass="fa fa-universal-access"
                    >
                        <FormGrid columns={2}>
                            {props.mode === "create" ? (
                                <FormField
                                    id="student-deficiency-profile"
                                    label="Perfil"
                                    error={errors.deficiency_id}
                                    requiredIndicator
                                >
                                    <SelectField
                                        id="student-deficiency-profile"
                                        name="deficiency_id"
                                        label="Perfil"
                                        required
                                        disabled={form.processing}
                                        value={form.data.deficiency_id}
                                        options={props.deficiencies.map((deficiency) => ({
                                            value: String(deficiency.id),
                                            label: deficiency.name,
                                        }))}
                                        aria-invalid={!!errors.deficiency_id}
                                        aria-describedby={describedBy("deficiency_id", "student-deficiency-profile")}
                                        onChange={(event) => form.setData("deficiency_id", event.target.value)}
                                    />
                                </FormField>
                            ) : (
                                <FormField
                                    id="student-deficiency-profile"
                                    label="Perfil"
                                    error={errors.deficiency_id}
                                    requiredIndicator
                                >
                                    <TextInput
                                        id="student-deficiency-profile"
                                        value={props.studentDeficiency.deficiency?.name ?? ""}
                                        readOnly
                                        invalid={!!errors.deficiency_id}
                                        aria-describedby={describedBy("deficiency_id", "student-deficiency-profile")}
                                    />
                                </FormField>
                            )}
                            <FormField
                                id="student-deficiency-severity"
                                label="Severidade"
                                error={errors.severity}
                                requiredIndicator
                            >
                                <SelectField
                                    id="student-deficiency-severity"
                                    name="severity"
                                    label="Severidade"
                                    required
                                    disabled={form.processing}
                                    value={form.data.severity}
                                    options={severityOptions}
                                    aria-invalid={!!errors.severity}
                                    aria-describedby={describedBy("severity", "student-deficiency-severity")}
                                    onChange={(event) => form.setData("severity", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField
                                    id="student-deficiency-notes"
                                    label="Observações"
                                    error={errors.notes}
                                >
                                    <TextArea
                                        id="student-deficiency-notes"
                                        name="notes"
                                        rows={4}
                                        maxLength={1000}
                                        placeholder={
                                            studentDeficiency
                                                ? undefined
                                                : "Detalhes adicionais sobre o perfil de atendimento do aluno..."
                                        }
                                        value={form.data.notes}
                                        aria-invalid={!!errors.notes}
                                        aria-describedby={describedBy("notes", "student-deficiency-notes")}
                                        onChange={(event) => form.setData("notes", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                </fieldset>
            </CardBox>
            <FormActions
                status={
                    form.processing
                        ? studentDeficiency
                            ? "Salvando alterações..."
                            : "Vinculando perfil de atendimento..."
                        : undefined
                }
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
                    label="Salvar"
                    title="Salvar perfil de atendimento"
                    aria-label={form.processing ? "Salvando perfil de atendimento" : "Salvar perfil de atendimento"}
                    disabled={form.processing}
                />
            </FormActions>
        </FormLayout>
    );
}
