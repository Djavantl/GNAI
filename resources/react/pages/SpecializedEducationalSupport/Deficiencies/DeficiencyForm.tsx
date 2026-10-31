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
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { DeficiencyRecord } from "./Deficiency.types";
import DeficiencyPage, { deficiencyBase } from "./DeficiencyPage";

type DeficiencyFormData = {
    name: string;
    cid_code: string;
    description: string;
};

export default function DeficiencyForm({
    deficiency,
}: {
    deficiency?: DeficiencyRecord;
}): React.JSX.Element {
    const editing = Boolean(deficiency);
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const form = useForm<DeficiencyFormData>({
        name: deficiency?.name ?? "",
        cid_code: deficiency?.cid_code ?? "",
        description: deficiency?.description ?? "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const backHref = deficiency
        ? `${deficiencyBase}/${deficiency.id}/show`
        : deficiencyBase;

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
                        ? "Não foi possível salvar o perfil de atendimento. Tente novamente."
                        : "Não foi possível cadastrar o perfil de atendimento. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (deficiency) {
            form.put(`${deficiencyBase}/${deficiency.id}`, options);
        } else {
            form.post(`${deficiencyBase}/store`, options);
        }
    }

    return (
        <DeficiencyPage
            title={editing ? "Editar Perfil de Atendimento" : "Cadastrar Novo Perfil de Atendimento"}
            subtitle={editing
                ? `Atualizando informações de: ${deficiency?.name ?? ""}`
                : "Cadastro das condições, características e necessidades educacionais dos estudantes, com a finalidade de apoiar o acompanhamento, os encaminhamentos e a oferta de atendimentos adequados, como deficiência, TEA, TDAH, altas habilidades/superdotação e dificuldades de aprendizagem."}
            current={editing ? "Editar" : "Cadastrar"}
            deficiency={deficiency}
        >
            <FormLayout
                ref={element}
                onSubmit={submit}
                aria-label={editing ? "Editar perfil de atendimento" : "Cadastrar perfil de atendimento"}
                aria-busy={form.processing}
            >
                <CardBox>
                    <fieldset disabled={form.processing}>
                        <FormSection
                            id="deficiency-information"
                            title={editing ? "Atualizar Dados" : "Identificação"}
                            variant="inline"
                            iconClass="fa fa-universal-access"
                        >
                            <FormGrid columns={2}>
                                <FormField
                                    id="deficiency-name"
                                    label="Nome do Perfil"
                                    requiredIndicator
                                    error={errors.name}
                                >
                                    <TextInput
                                        id="deficiency-name"
                                        name="name"
                                        required
                                        maxLength={255}
                                        placeholder={editing ? undefined : "Ex: Deficiência Visual"}
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        aria-describedby={errors.name ? "deficiency-name-error" : undefined}
                                        onChange={(event) => form.setData("name", event.target.value)}
                                    />
                                </FormField>
                                <FormField
                                    id="deficiency-cid-code"
                                    label="Código CID"
                                    error={errors.cid_code}
                                >
                                    <TextInput
                                        id="deficiency-cid-code"
                                        name="cid_code"
                                        maxLength={20}
                                        placeholder={editing ? undefined : "Ex: F84.0"}
                                        value={form.data.cid_code}
                                        invalid={!!errors.cid_code}
                                        aria-describedby={errors.cid_code ? "deficiency-cid-code-error" : undefined}
                                        onChange={(event) => form.setData("cid_code", event.target.value)}
                                    />
                                </FormField>
                                <FormGridWide>
                                    <FormField
                                        id="deficiency-description"
                                        label="Descrição / Detalhes"
                                        error={errors.description}
                                    >
                                        <TextArea
                                            id="deficiency-description"
                                            name="description"
                                            rows={3}
                                            value={form.data.description}
                                            aria-invalid={!!errors.description}
                                            aria-describedby={errors.description ? "deficiency-description-error" : undefined}
                                            onChange={(event) => form.setData("description", event.target.value)}
                                        />
                                    </FormField>
                                </FormGridWide>
                            </FormGrid>
                        </FormSection>
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (editing ? "Salvando alterações…" : "Cadastrando perfil de atendimento…")
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
        </DeficiencyPage>
    );
}
