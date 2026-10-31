import { useForm } from "@inertiajs/react";
import { useMemo, useRef, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import ChoiceGroup from "@/components/Forms/ChoiceGroup/ChoiceGroup";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { CourseDisciplineOption, CourseRecord } from "./Course.types";
import CoursePage, { courseBase } from "./CoursePage";

type CourseFormData = {
    name: string;
    description: string;
    discipline_ids: number[];
    is_active: string;
};

export default function CourseForm({
    course,
    disciplines,
}: {
    course?: CourseRecord;
    disciplines: CourseDisciplineOption[];
}): React.JSX.Element {
    const editing = Boolean(course);
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [search, setSearch] = useState("");
    const availableIds = useMemo(
        () => new Set(disciplines.map((discipline) => discipline.id)),
        [disciplines],
    );
    const form = useForm<CourseFormData>({
        name: course?.name ?? "",
        description: course?.description ?? "",
        // As in the legacy form, only active disciplines are listed and submitted.
        discipline_ids: (course?.disciplines ?? [])
            .map((discipline) => discipline.id)
            .filter((id) => availableIds.has(id)),
        is_active: course ? (course.is_active ? "1" : "0") : "1",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const disciplinesError = errors.discipline_ids
        ?? Object.entries(errors).find(([key]) => key.startsWith("discipline_ids."))?.[1];
    const term = search.toLowerCase().trim();
    const visibleDisciplines = term
        ? disciplines.filter((discipline) => discipline.name.toLowerCase().includes(term))
        : disciplines;
    const backHref = course ? `${courseBase}/${course.id}/show` : courseBase;

    function toggleDiscipline(id: number, checked: boolean): void {
        form.setData((data) => ({
            ...data,
            discipline_ids: checked
                ? [...data.discipline_ids.filter((current) => current !== id), id]
                : data.discipline_ids.filter((current) => current !== id),
        }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        form.transform((data) => {
            if (editing) return data;
            const { is_active: _isActive, ...creation } = data;
            return creation;
        });

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
                        ? "Não foi possível salvar o curso. Tente novamente."
                        : "Não foi possível cadastrar o curso. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (course) {
            form.put(`${courseBase}/${course.id}`, options);
        } else {
            form.post(`${courseBase}/store`, options);
        }
    }

    return (
        <CoursePage
            title={editing ? `Editar o curso ${course?.name ?? ""}` : "Cadastrar novo curso"}
            subtitle={editing
                ? "Atualize as informações do curso e sua matriz curricular."
                : "Defina as informações do curso e sua matriz curricular inicial."}
            current={editing ? "Editar" : "Cadastrar"}
            course={course}
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-label={editing ? "Editar curso" : "Cadastrar curso"}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="course-information"
                            title={editing ? "Informações Básicas" : "Dados do Curso"}
                            variant="inline"
                            iconClass="fa fa-university"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    <FormField
                                        id="course-name"
                                        label="Nome do Curso"
                                        requiredIndicator
                                        error={errors.name}
                                    >
                                        <TextInput
                                            id="course-name"
                                            name="name"
                                            required
                                            maxLength={255}
                                            value={form.data.name}
                                            invalid={!!errors.name}
                                            aria-describedby={errors.name ? "course-name-error" : undefined}
                                            onChange={(event) => form.setData("name", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormGridWide>
                                    <FormField
                                        id="course-description"
                                        label="Descrição"
                                        error={errors.description}
                                    >
                                        <TextArea
                                            id="course-description"
                                            name="description"
                                            rows={4}
                                            value={form.data.description}
                                            aria-invalid={!!errors.description}
                                            aria-describedby={errors.description ? "course-description-error" : undefined}
                                            onChange={(event) => form.setData("description", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                {editing ? (
                                    <FormField
                                        id="course-status"
                                        label="Status do Curso"
                                        requiredIndicator
                                        error={errors.is_active}
                                    >
                                        <SelectField
                                            id="course-status"
                                            name="is_active"
                                            label="Status do Curso"
                                            required
                                            value={form.data.is_active}
                                            options={[
                                                { value: "1", label: "Ativo" },
                                                { value: "0", label: "Inativo" },
                                            ]}
                                            disabled={form.processing}
                                            aria-invalid={!!errors.is_active}
                                            aria-describedby={errors.is_active ? "course-status-error" : undefined}
                                            onChange={(event) => form.setData("is_active", event.target.value)}
                                        />
                                    </FormField>
                                ) : null}
                            </FormGrid>
                        </FormSection>
                        <FormSection
                            id="course-curriculum"
                            title={editing
                                ? "Matriz Curricular (Selecione as Disciplinas)"
                                : "Matriz Curricular Inicial"}
                            variant="inline"
                            iconClass="fa fa-book"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    <FormField
                                        id="course-discipline-search"
                                        label="Buscar disciplina"
                                    >
                                        <TextInput
                                            id="course-discipline-search"
                                            type="search"
                                            placeholder="Digite o nome da disciplina..."
                                            autoComplete="off"
                                            aria-controls="course-disciplines"
                                            value={search}
                                            onChange={(event) => setSearch(event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormGridWide>
                                    <ChoiceGroup
                                        id="course-disciplines"
                                        label="Disciplinas do curso"
                                        invalid={!!disciplinesError}
                                        describedBy={disciplinesError ? "course-disciplines-error" : undefined}
                                    >
                                        {visibleDisciplines.map((discipline) => (
                                            <ChoiceControl
                                                key={discipline.id}
                                                type="checkbox"
                                                tone="purple"
                                                id={`course-discipline-${discipline.id}`}
                                                name="discipline_ids[]"
                                                label={discipline.name}
                                                checked={form.data.discipline_ids.includes(discipline.id)}
                                                disabled={form.processing}
                                                onChange={(checked) => toggleDiscipline(discipline.id, checked)}
                                            />
                                        ))}
                                        {!visibleDisciplines.length ? (
                                            <span role="status">
                                                {term
                                                    ? "Nenhuma disciplina encontrada com esse termo."
                                                    : "Nenhuma disciplina ativa disponível."}
                                            </span>
                                        ) : null}
                                    </ChoiceGroup>
                                    {disciplinesError ? (
                                        <span
                                            id="course-disciplines-error"
                                            className="formField__error"
                                        >
                                            {disciplinesError}
                                        </span>
                                    ) : null}
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações…" : "Cadastrando curso…")
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
        </CoursePage>
    );
}
