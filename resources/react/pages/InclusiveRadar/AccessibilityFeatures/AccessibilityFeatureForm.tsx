import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { AccessibilityFeatureRecord } from "./AccessibilityFeature.types";
import AccessibilityFeaturePage from "./AccessibilityFeaturePage";
import "./AccessibilityFeatureForm.css";

const base = "/inclusive-radar/accessibility-features";

export default function AccessibilityFeatureForm({
    feature,
    canSubmit,
}: {
    feature?: AccessibilityFeatureRecord;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(feature);
    const form = useForm({
        name: feature?.name ?? "",
        description: feature?.description ?? "",
        is_active: feature?.is_active ?? true,
    });
    const errors = form.errors as Record<string, string | undefined>;
    const backHref = editing && feature ? `${base}/${feature.id}` : base;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (!canSubmit || form.processing) return;

        if (feature) {
            form.put(`${base}/${feature.id}`, {
                preserveScroll: true,
                onNetworkError: () => {
                    notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                    return false;
                },
                onHttpException: () => {
                    notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível salvar o recurso. Tente novamente." });
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
                    notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível cadastrar o recurso. Tente novamente." });
                    return false;
                },
            });
        }
    }

    return (
        <AccessibilityFeaturePage
            title={editing ? "Editar Recurso de Acessibilidade" : "Novo Recurso de Acessibilidade"}
            subtitle={editing
                ? `Atualizando informações de: ${feature?.name ?? ""}`
                : "Defina uma nova categoria de acessibilidade."}
            current={editing ? "Editar" : "Cadastrar"}
            feature={feature}
        >
            <FormLayout onSubmit={submit} aria-busy={form.processing}>
                <CardBox>
                    <FormSection
                        id="accessibility-feature-identification"
                        title="Dados do Recurso"
                        variant="inline"
                        iconClass="fa fa-universal-access"
                    >
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <FormField
                                    id="accessibility-feature-name"
                                    label="Nome do Recurso"
                                    requiredIndicator
                                    error={errors.name}
                                >
                                    <TextInput
                                        id="accessibility-feature-name"
                                        name="name"
                                        required
                                        maxLength={255}
                                        placeholder="Ex: Intérprete de Libras, Piso Podotátil, etc."
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        aria-describedby={errors.name
                                            ? "accessibility-feature-name-error"
                                            : undefined}
                                        disabled={form.processing}
                                        onChange={(event) => form.setData("name", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                            <FormGridWide>
                                <FormField
                                    id="accessibility-feature-description"
                                    label="Descrição Detalhada"
                                    error={errors.description}
                                >
                                    <TextArea
                                        id="accessibility-feature-description"
                                        name="description"
                                        rows={4}
                                        maxLength={1000}
                                        placeholder="Descreva a finalidade e aplicação deste recurso de acessibilidade"
                                        value={form.data.description}
                                        aria-invalid={!!errors.description}
                                        aria-describedby={errors.description
                                            ? "accessibility-feature-description-error"
                                            : undefined}
                                        disabled={form.processing}
                                        onChange={(event) => form.setData("description", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                            <div className="accessibilityFeatureForm__active">
                                <ToggleControl
                                    id="accessibility-feature-active"
                                    name="is_active"
                                    size="small"
                                    label="Recurso Ativo"
                                    labelAddon={(
                                        <InfoTooltip
                                            label="Informações sobre o status do recurso"
                                            text="Define se este recurso estará disponível para seleção no sistema."
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
                    status={form.processing ? (editing ? "Salvando alterações…" : "Cadastrando recurso…") : undefined}
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
        </AccessibilityFeaturePage>
    );
}
