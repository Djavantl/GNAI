import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide, FormStack } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { BarrierCategoryRecord } from "./BarrierCategory.types";
import BarrierCategoryPage from "./BarrierCategoryPage";

const base = "/inclusive-radar/barrier-categories";

export default function BarrierCategoryForm({
    category,
    canSubmit,
}: {
    category?: BarrierCategoryRecord;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(category);
    const form = useForm({
        name: category?.name ?? "",
        description: category?.description ?? "",
        is_active: category?.is_active ?? true,
        blocks_map: category?.blocks_map ?? true,
    });
    const errors = form.errors as Record<string, string | undefined>;
    const backHref = editing && category ? `${base}/${category.id}` : base;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (!canSubmit || form.processing) return;

        if (category) {
            form.put(`${base}/${category.id}`, {
                preserveScroll: true,
                onNetworkError: () => {
                    notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                    return false;
                },
                onHttpException: () => {
                    notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível salvar a categoria. Tente novamente." });
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
                    notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível cadastrar a categoria. Tente novamente." });
                    return false;
                },
            });
        }
    }

    return (
        <BarrierCategoryPage
            title={editing ? "Editar Categoria de Barreira" : "Nova Categoria de Barreira"}
            subtitle={editing
                ? `Atualizando as definições da categoria: ${category?.name ?? ""}`
                : "Defina classificações para identificar obstáculos à acessibilidade."}
            current={editing ? "Editar" : "Cadastrar"}
            category={category}
        >
            <FormLayout onSubmit={submit} aria-busy={form.processing}>
                <CardBox>
                    <FormSection
                        id="barrier-category-information"
                        title="Informações da Categoria"
                        variant="inline"
                        iconClass="fa fa-tags"
                    >
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <FormField
                                    id="barrier-category-name"
                                    label="Nome da Categoria"
                                    requiredIndicator
                                    error={errors.name}
                                >
                                    <TextInput
                                        id="barrier-category-name"
                                        name="name"
                                        required
                                        maxLength={150}
                                        placeholder="Ex: Arquitetônica, Atitudinal, Comunicacional..."
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        aria-describedby={errors.name ? "barrier-category-name-error" : undefined}
                                        disabled={form.processing}
                                        onChange={(event) => form.setData("name", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                            <FormGridWide>
                                <FormField
                                    id="barrier-category-description"
                                    label="Descrição Detalhada"
                                    error={errors.description}
                                >
                                    <TextArea
                                        id="barrier-category-description"
                                        name="description"
                                        rows={4}
                                        placeholder="Descreva o que este tipo de barreira engloba..."
                                        value={form.data.description}
                                        aria-invalid={!!errors.description}
                                        aria-describedby={errors.description ? "barrier-category-description-error" : undefined}
                                        disabled={form.processing}
                                        onChange={(event) => form.setData("description", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="barrier-category-settings"
                        title="Status e Visibilidade"
                        variant="inline"
                        iconClass="fa fa-cogs"
                    >
                        <FormGrid columns={2}>
                            <FormStack>
                                <ToggleControl
                                    id="barrier-category-active"
                                    name="is_active"
                                    size="small"
                                    label="Ativar no Sistema"
                                    description="Indica se esta categoria estará disponível para seleção no cadastro de novas barreiras"
                                    checked={form.data.is_active}
                                    disabled={form.processing}
                                    onChange={(checked) => form.setData("is_active", checked)}
                                />
                            </FormStack>
                            <FormStack>
                                <ToggleControl
                                    id="barrier-category-blocks-map"
                                    name="blocks_map"
                                    size="small"
                                    label="Bloquear Mapa"
                                    description="Oculta o mapa no cadastro de novas barreiras quando esta categoria for selecionada"
                                    checked={form.data.blocks_map}
                                    disabled={form.processing}
                                    onChange={(checked) => form.setData("blocks_map", checked)}
                                />
                            </FormStack>
                        </FormGrid>
                    </FormSection>
                </CardBox>
                <FormActions
                    status={form.processing ? (editing ? "Salvando alterações…" : "Cadastrando categoria…") : undefined}
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
                            label="Salvar"
                            disabled={form.processing}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </BarrierCategoryPage>
    );
}
