import { useForm } from "@inertiajs/react";
import { useRef } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { SemesterRecord } from "./Semester.types";
import SemesterPage, { semesterBase, semesterLabel } from "./SemesterPage";

type SemesterFormData = {
    year: string;
    term: string;
    start_date: string;
    end_date: string;
    is_current: boolean;
};

/** Laravel serializes `date` casts as ISO timestamps; the form works with AAAA-MM-DD. */
function dateOnly(value: string | null): string {
    return value ? value.split("T")[0] ?? "" : "";
}

export default function SemesterForm({
    semester,
}: {
    semester?: SemesterRecord;
}): React.JSX.Element {
    const editing = Boolean(semester);
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const form = useForm<SemesterFormData>({
        year: semester ? String(semester.year) : String(new Date().getFullYear()),
        term: semester ? String(semester.term) : "1",
        start_date: dateOnly(semester?.start_date ?? null),
        end_date: dateOnly(semester?.end_date ?? null),
        is_current: semester?.is_current ?? false,
    });
    const errors = form.errors as Record<string, string | undefined>;
    const backHref = semester
        ? `${semesterBase}/${semester.id}/show`
        : semesterBase;

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
                    text: "Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: editing
                        ? "Não foi possível salvar o semestre. Tente novamente."
                        : "Não foi possível cadastrar o semestre. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (semester) {
            form.put(`${semesterBase}/${semester.id}`, options);
        } else {
            form.post(`${semesterBase}/store`, options);
        }
    }

    return (
        <SemesterPage
            title={editing ? "Editar Semestre" : "Cadastrar Novo Semestre"}
            subtitle={semester
                ? `Alterando: ${semesterLabel(semester)}`
                : "Defina o período letivo e sua vigência no sistema."}
            current={editing ? "Editar" : "Cadastrar"}
            semester={semester}
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-label={editing ? "Editar semestre" : "Cadastrar semestre"}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="semester-identification"
                            title="Identificação do Período"
                            variant="inline"
                            iconClass="fa fa-calendar"
                        >
                            <FormGrid columns={2}>
                                <FormField
                                    id="semester-year"
                                    label="Ano Letivo"
                                    requiredIndicator
                                    error={errors.year}
                                >
                                    <TextInput
                                        id="semester-year"
                                        name="year"
                                        type="number"
                                        inputMode="numeric"
                                        required
                                        min={1901}
                                        max={2155}
                                        value={form.data.year}
                                        invalid={!!errors.year}
                                        aria-describedby={errors.year ? "semester-year-error" : undefined}
                                        onChange={(event) => form.setData("year", event.target.value)}
                                    />
                                </FormField>
                                <FormField
                                    id="semester-term"
                                    label={editing ? "Período" : "Período (Ex: 1 ou 2)"}
                                    requiredIndicator
                                    error={errors.term}
                                >
                                    <TextInput
                                        id="semester-term"
                                        name="term"
                                        type="number"
                                        inputMode="numeric"
                                        required
                                        min={1}
                                        max={2}
                                        value={form.data.term}
                                        invalid={!!errors.term}
                                        aria-describedby={errors.term ? "semester-term-error" : undefined}
                                        onChange={(event) => form.setData("term", event.target.value)}
                                    />
                                </FormField>
                            </FormGrid>
                        </FormSection>
                        <FormSection
                            id="semester-duration"
                            title="Duração do Semestre"
                            variant="inline"
                            iconClass="fa fa-clock-o"
                        >
                            <FormGrid columns={2}>
                                <FormField
                                    id="semester-start-date"
                                    label="Data de Início"
                                    error={errors.start_date}
                                >
                                    <DatePicker
                                        id="semester-start-date"
                                        name="start_date"
                                        label="Data de Início"
                                        value={form.data.start_date}
                                        disabled={form.processing}
                                        invalid={!!errors.start_date}
                                        describedBy={errors.start_date ? "semester-start-date-error" : undefined}
                                        onChange={(date) => form.setData("start_date", date)}
                                    />
                                </FormField>
                                <FormField
                                    id="semester-end-date"
                                    label="Data de Término"
                                    error={errors.end_date}
                                >
                                    <DatePicker
                                        id="semester-end-date"
                                        name="end_date"
                                        label="Data de Término"
                                        value={form.data.end_date}
                                        disabled={form.processing}
                                        invalid={!!errors.end_date}
                                        describedBy={errors.end_date ? "semester-end-date-error" : undefined}
                                        onChange={(date) => form.setData("end_date", date)}
                                    />
                                </FormField>
                                <FormGridWide>
                                    <ChoiceControl
                                        type="checkbox"
                                        tone="purple"
                                        id="semester-is-current"
                                        name="is_current"
                                        label={editing
                                            ? "Este é o semestre atual"
                                            : "Definir este como o semestre atual do sistema"}
                                        checked={form.data.is_current}
                                        disabled={form.processing}
                                        onChange={(checked) => form.setData("is_current", checked)}
                                    />
                                    {errors.is_current ? (
                                        <span
                                            id="semester-is-current-error"
                                            className="formField__error"
                                        >
                                            {errors.is_current}
                                        </span>
                                    ) : null}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações…" : "Cadastrando semestre…")
                        : undefined}
                >
                    <Button
                        href={backHref}
                        tone="cancel"
                        label="Cancelar"
                        aria-label="Cancelar e voltar"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        label="Salvar"
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </SemesterPage>
    );
}
