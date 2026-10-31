import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { TagRecord } from "./Tag.types";
import TagPage from "./TagPage";
import "./TagForm.css";

const base = "/inclusive-radar/tags";

export default function TagForm({
    tag,
    canSubmit,
}: {
    tag?: TagRecord;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(tag);
    const form = useForm({
        name: tag?.name ?? "",
        is_active: tag?.is_active ?? true,
    });
    const errors = form.errors as Record<string, string | undefined>;
    const backHref = editing && tag ? `${base}/${tag.id}` : base;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (!canSubmit || form.processing) return;

        if (tag) {
            form.put(`${base}/${tag.id}`, {
                preserveScroll: true,
                onNetworkError: () => {
                    notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                    return false;
                },
                onHttpException: () => {
                    notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível salvar a etiqueta. Tente novamente." });
                    return false;
                },
            });
        } else {
            form.post(`${base}/store`, {
                preserveScroll: true,
                onNetworkError: () => {
                    notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                    return false;
                },
                onHttpException: () => {
                    notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível cadastrar a etiqueta. Tente novamente." });
                    return false;
                },
            });
        }
    }

    return (
        <TagPage
            title={editing ? "Editar Etiqueta" : "Nova Etiqueta"}
            subtitle={editing
                ? `Atualizando informações de: ${tag?.name ?? ""}`
                : "Defina uma nova etiqueta para classificar as publicações."}
            current={editing ? "Editar" : "Cadastrar"}
            tag={tag}
        >
            <FormLayout onSubmit={submit} aria-busy={form.processing}>
                <CardBox>
                    <FormSection
                        id="tag-identification"
                        title="Identificação"
                        variant="inline"
                        iconClass="fa fa-tags"
                    >
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <FormField
                                    id="tag-name"
                                    label="Nome da Etiqueta"
                                    requiredIndicator
                                    error={errors.name}
                                >
                                    <TextInput
                                        id="tag-name"
                                        name="name"
                                        required
                                        maxLength={100}
                                        placeholder="Ex: Acessibilidade, Eventos, Editais, etc."
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        aria-describedby={errors.name
                                            ? "tag-name-error"
                                            : undefined}
                                        disabled={form.processing}
                                        onChange={(event) => form.setData("name", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                            <div className="tagForm__active">
                                <ToggleControl
                                    id="tag-active"
                                    name="is_active"
                                    size="small"
                                    label="Etiqueta Ativa"
                                    labelAddon={(
                                        <InfoTooltip
                                            label="Informações sobre o status da etiqueta"
                                            text="Define se esta etiqueta estará disponível para seleção no sistema."
                                        />
                                    )}
                                    checked={form.data.is_active}
                                    disabled={form.processing}
                                    onChange={(checked) => form.setData("is_active", checked)}
                                />
                            </div>
                        </FormGrid>
                    </FormSection>
                </CardBox>
                <FormActions
                    status={form.processing ? (editing ? "Salvando alterações…" : "Cadastrando etiqueta…") : undefined}
                >
                    <Button
                        href={backHref}
                        tone="cancel"
                        label="Cancelar"
                        aria-label="Cancelar e voltar"
                        disabled={form.processing}
                    />
                    {canSubmit ? (
                        <Button
                            type="submit"
                            tone="save"
                            label={editing ? "Salvar" : "Cadastrar"}
                            disabled={form.processing}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </TagPage>
    );
}
