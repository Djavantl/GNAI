import { useEffect, useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import type { SelectFieldOption } from "@/components/Forms/SelectField/SelectField.types";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";

const aeeBase = "/specialized-educational-support";

export interface PeiDisciplineTeacherOption {
    id: number;
    person?: { name: string } | null;
}

export interface PeiDisciplineRecord {
    id: number;
    teacher_id: number;
    discipline_id: number;
    specific_objectives: string | null;
    content_programmatic: string | null;
    methodologies: string | null;
    evaluations: string | null;
    complementary_records: string | null;
    opinion: string | null;
    teacher?: { person?: { name: string } | null } | null;
    discipline?: { name: string } | null;
}

type PeiDisciplineFormData = {
    teacher_id: string;
    discipline_id: string;
    specific_objectives: string;
    content_programmatic: string;
    methodologies: string;
    evaluations: string;
    complementary_records: string;
    opinion: string;
};

type PeiDisciplineTextField = Exclude<keyof PeiDisciplineFormData, "teacher_id" | "discipline_id">;

const textFields: Array<{
    name: PeiDisciplineTextField;
    label: string;
    placeholder: string;
    required: boolean;
    hint?: string;
}> = [
    {
        name: "specific_objectives",
        label: "Objetivos Específicos",
        placeholder: "Descreva os objetivos de aprendizagem adaptados para o aluno...",
        required: true,
    },
    {
        name: "content_programmatic",
        label: "Conteúdo Programático",
        placeholder: "Liste os conteúdos que serão abordados nesta disciplina...",
        required: true,
    },
    {
        name: "methodologies",
        label: "Metodologias e Estratégias",
        placeholder: "Descreva como o conteúdo será ensinado (recursos, materiais, apoios)...",
        required: true,
    },
    {
        name: "evaluations",
        label: "Processo de Avaliação",
        placeholder: "Como a aprendizagem será avaliada nesta disciplina?",
        required: true,
    },
    {
        name: "complementary_records",
        label: "Registros Complementares",
        placeholder: "Registre aqui informações complementares relevantes para o acompanhamento pedagógico do estudante.",
        required: false,
        hint: "Espaço opcional destinado ao registro de informações relevantes que contribuam para o acompanhamento pedagógico do estudante e que não estejam contempladas nos campos anteriores.",
    },
    {
        name: "opinion",
        label: "Parecer",
        placeholder: "Descrever avanços do estudante, considerando as metas previstas para ele e principais dificuldades.",
        required: false,
    },
];

export default function PeiDisciplineForm({
    pei,
    teachers = [],
    loggedTeacher = null,
    peiDiscipline,
}: {
    pei: { id: number };
    teachers?: PeiDisciplineTeacherOption[];
    loggedTeacher?: { id: number; name: string | null } | null;
    peiDiscipline?: PeiDisciplineRecord;
}): React.JSX.Element {
    const editing = Boolean(peiDiscipline);
    const busy = useRef(false);
    const element = useRef<HTMLFormElement>(null);
    const peiHref = `${aeeBase}/peis/${pei.id}/show?tab=disciplines`;
    const form = useForm<PeiDisciplineFormData>({
        teacher_id: String(peiDiscipline?.teacher_id ?? loggedTeacher?.id ?? ""),
        discipline_id: String(peiDiscipline?.discipline_id ?? ""),
        specific_objectives: peiDiscipline?.specific_objectives ?? "",
        content_programmatic: peiDiscipline?.content_programmatic ?? "",
        methodologies: peiDiscipline?.methodologies ?? "",
        evaluations: peiDiscipline?.evaluations ?? "",
        complementary_records: peiDiscipline?.complementary_records ?? "",
        opinion: peiDiscipline?.opinion ?? "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const [disciplineOptions, setDisciplineOptions] = useState<SelectFieldOption[]>([]);
    const [loadingDisciplines, setLoadingDisciplines] = useState(false);
    const describedBy = (name: string, id: string, hint = false): string | undefined =>
        [hint ? `${id}-hint` : undefined, errors[name] ? `${id}-error` : undefined]
            .filter(Boolean)
            .join(" ") || undefined;
    const teacherOptions = teachers.map((teacher) => ({
        value: String(teacher.id),
        label: teacher.person?.name ?? "",
    }));
    const teacherId = form.data.teacher_id;

    useEffect(() => {
        if (editing) return;

        setDisciplineOptions([]);
        if (!teacherId) {
            setLoadingDisciplines(false);
            return;
        }

        const controller = new AbortController();
        const url = new URL(`${aeeBase}/peis/${pei.id}/teacher-disciplines`, window.location.origin);
        url.searchParams.set("teacher_id", teacherId);
        setLoadingDisciplines(true);

        void fetch(url.toString(), {
            headers: { Accept: "application/json" },
            credentials: "same-origin",
            signal: controller.signal,
        })
            .then((response) => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json() as Promise<Array<{ id: number; name: string }>>;
            })
            .then((items) => {
                const options = items.map((item) => ({
                    value: String(item.id),
                    label: item.name,
                }));
                setDisciplineOptions(options);
                form.setData((current) => ({
                    ...current,
                    discipline_id: options.some((option) => option.value === current.discipline_id)
                        ? current.discipline_id
                        : "",
                }));
            })
            .catch(() => {
                if (controller.signal.aborted) return;
                notify({
                    tone: "error",
                    title: "Não foi possível carregar",
                    text: "Erro ao buscar disciplinas do professor.",
                });
            })
            .finally(() => {
                if (!controller.signal.aborted) setLoadingDisciplines(false);
            });

        return () => controller.abort();
    }, [editing, pei.id, teacherId]);

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
                    text: "Não foi possível salvar a adaptação curricular. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (peiDiscipline) {
            form.put(`${aeeBase}/peis/${pei.id}/disciplines/${peiDiscipline.id}`, options);
        } else {
            form.post(`${aeeBase}/peis/${pei.id}/disciplines/store`, options);
        }
    }

    const disciplineName = peiDiscipline?.discipline?.name ?? "";

    return (
        <BusinessPage
            title={editing ? "Editar Adaptação Curricular" : "Cadastrar Adaptação Curricular"}
            subtitle={editing
                ? `Atualize o planejamento pedagógico para a disciplina de ${disciplineName}.`
                : "Defina os objetivos, conteúdos e metodologias adaptadas para este PEI."}
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Alunos", href: `${aeeBase}/students` },
                { label: "PEI", href: peiHref },
                { label: editing ? "Editar Adaptação" : "Cadastrar Adaptação" },
            ]}
            className="peiDisciplineFormPage"
        >
            <FormLayout
                ref={element}
                aria-label={editing ? "Editar adaptação curricular" : "Cadastrar adaptação curricular"}
                aria-busy={form.processing}
                onSubmit={submit}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="pei-discipline-identification"
                            title={editing ? "Identificação (Somente Leitura)" : "Identificação da Disciplina"}
                            variant="inline"
                            iconClass="fa fa-graduation-cap"
                        >
                            <FormGrid columns={2}>
                                <FormField
                                    id="pei-discipline-teacher"
                                    label="Professor Responsável"
                                    requiredIndicator={!editing && !loggedTeacher}
                                    error={errors.teacher_id}
                                >
                                    {editing || loggedTeacher ? (
                                        <TextInput
                                            id="pei-discipline-teacher"
                                            name={editing ? "teacher_display" : "teacher_name"}
                                            value={editing
                                                ? peiDiscipline?.teacher?.person?.name ?? ""
                                                : loggedTeacher?.name ?? ""}
                                            disabled
                                            readOnly
                                            invalid={!!errors.teacher_id}
                                            aria-describedby={describedBy("teacher_id", "pei-discipline-teacher")}
                                        />
                                    ) : (
                                        <SelectField
                                            id="pei-discipline-teacher"
                                            name="teacher_id"
                                            label="Professor Responsável"
                                            required
                                            value={form.data.teacher_id}
                                            options={teacherOptions}
                                            aria-invalid={!!errors.teacher_id}
                                            aria-describedby={describedBy("teacher_id", "pei-discipline-teacher")}
                                            disabled={form.processing}
                                            onChange={(event) => form.setData("teacher_id", event.target.value)}
                                        />
                                    )}
                                </FormField>
                                <FormField
                                    id="pei-discipline-discipline"
                                    label="Disciplina"
                                    requiredIndicator={!editing}
                                    error={errors.discipline_id}
                                >
                                    {editing ? (
                                        <TextInput
                                            id="pei-discipline-discipline"
                                            name="discipline_display"
                                            value={disciplineName}
                                            disabled
                                            readOnly
                                            invalid={!!errors.discipline_id}
                                            aria-describedby={describedBy("discipline_id", "pei-discipline-discipline")}
                                        />
                                    ) : (
                                        <SelectField
                                            id="pei-discipline-discipline"
                                            name="discipline_id"
                                            label="Disciplina"
                                            required
                                            placeholder={loadingDisciplines
                                                ? "Carregando disciplinas..."
                                                : "Selecione uma disciplina"}
                                            value={form.data.discipline_id}
                                            options={disciplineOptions}
                                            aria-invalid={!!errors.discipline_id}
                                            aria-describedby={describedBy("discipline_id", "pei-discipline-discipline")}
                                            aria-busy={loadingDisciplines || undefined}
                                            disabled={form.processing || loadingDisciplines || !teacherId}
                                            onChange={(event) => form.setData("discipline_id", event.target.value)}
                                        />
                                    )}
                                </FormField>
                            </FormGrid>
                        </FormSection>
                        <FormSection
                            id="pei-discipline-planning"
                            title="Planejamento Adaptado"
                            variant="inline"
                            iconClass="fa fa-book"
                        >
                            <FormGrid columns={2}>
                                {textFields.map((field) => {
                                    const id = `pei-discipline-${field.name.replace(/_/g, "-")}`;

                                    return (
                                        <FormGridWide key={field.name}>
                                            <FormField
                                                id={id}
                                                label={field.label}
                                                hint={field.hint}
                                                requiredIndicator={field.required}
                                                error={errors[field.name]}
                                            >
                                                <TextArea
                                                    id={id}
                                                    name={field.name}
                                                    rows={4}
                                                    required={field.required}
                                                    placeholder={editing ? undefined : field.placeholder}
                                                    value={form.data[field.name]}
                                                    aria-invalid={!!errors[field.name] || undefined}
                                                    aria-describedby={describedBy(field.name, id, Boolean(field.hint))}
                                                    onChange={(event) => form.setData(field.name, event.target.value)}
                                                />
                                            </FormField>
                                        </FormGridWide>
                                    );
                                })}
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações…" : "Salvando adaptação…")
                        : undefined}
                >
                    <Button
                        href={peiHref}
                        tone="cancel"
                        label="Cancelar"
                        aria-label="Cancelar e voltar ao PEI"
                        disabled={form.processing}
                    />
                    <Button
                        type="submit"
                        tone="save"
                        label="Salvar"
                        title={editing ? "Atualizar adaptação" : "Salvar adaptação"}
                        aria-label={editing ? "Atualizar adaptação" : "Salvar adaptação"}
                        disabled={form.processing}
                    />
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
