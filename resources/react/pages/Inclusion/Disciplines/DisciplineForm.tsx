import { useForm } from "@inertiajs/react";
import { useRef } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
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
import type { DisciplineRecord } from "./Discipline.types";
import DisciplinePage, { disciplineBase } from "./DisciplinePage";

type DisciplineFormData = {
    name: string;
    description: string;
    is_active: string;
};

export default function DisciplineForm({
    discipline,
}: {
    discipline?: DisciplineRecord;
}): React.JSX.Element {
    const editing = Boolean(discipline);
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const form = useForm<DisciplineFormData>({
        name: discipline?.name ?? "",
        description: discipline?.description ?? "",
        is_active: discipline ? (discipline.is_active ? "1" : "0") : "1",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const backHref = discipline
        ? `${disciplineBase}/${discipline.id}/show`
        : disciplineBase;

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
                        ? "Não foi possível salvar a disciplina. Tente novamente."
                        : "Não foi possível cadastrar a disciplina. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (discipline) {
            form.put(`${disciplineBase}/${discipline.id}`, options);
        } else {
            form.post(`${disciplineBase}/store`, options);
        }
    }

    return (
        <DisciplinePage
            title={editing ? "Editar Disciplina" : "Cadastrar Disciplina"}
            subtitle={editing
                ? "Atualize as informações e o status da disciplina selecionada."
                : "Defina o nome e descrição da matéria para o catálogo da instituição."}
            current={editing ? "Editar" : "Cadastrar"}
            discipline={discipline}
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-label={editing ? "Editar disciplina" : "Cadastrar disciplina"}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="discipline-information"
                            title={editing ? "Dados da Disciplina" : "Informações Básicas"}
                            variant="inline"
                            iconClass="fa fa-book"
                        >
                            <FormGrid columns={2}>
                                <FormGridWide>
                                    <FormField
                                        id="discipline-name"
                                        label="Nome da Disciplina"
                                        requiredIndicator
                                        error={errors.name}
                                    >
                                        <TextInput
                                            id="discipline-name"
                                            name="name"
                                            required
                                            maxLength={255}
                                            value={form.data.name}
                                            invalid={!!errors.name}
                                            aria-describedby={errors.name ? "discipline-name-error" : undefined}
                                            onChange={(event) => form.setData("name", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormGridWide>
                                    <FormField
                                        id="discipline-description"
                                        label="Descrição"
                                        error={errors.description}
                                    >
                                        <TextArea
                                            id="discipline-description"
                                            name="description"
                                            rows={3}
                                            value={form.data.description}
                                            aria-invalid={!!errors.description}
                                            aria-describedby={errors.description ? "discipline-description-error" : undefined}
                                            onChange={(event) => form.setData("description", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                                <FormField
                                    id="discipline-status"
                                    label="Status"
                                    requiredIndicator
                                    error={errors.is_active}
                                >
                                    <SelectField
                                        id="discipline-status"
                                        name="is_active"
                                        label="Status"
                                        required
                                        value={form.data.is_active}
                                        options={[
                                            { value: "1", label: "Ativo" },
                                            { value: "0", label: "Inativo" },
                                        ]}
                                        disabled={form.processing}
                                        aria-invalid={!!errors.is_active}
                                        aria-describedby={errors.is_active ? "discipline-status-error" : undefined}
                                        onChange={(event) => form.setData("is_active", event.target.value)}
                                    />
                                </FormField>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações…" : "Cadastrando disciplina…")
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
        </DisciplinePage>
    );
}
