import { useRef } from "react";
import { useForm } from "@inertiajs/react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { GroupedPositionPermissions, PositionRecord } from "./Position.types";
import { positionsBase } from "./PositionPage";

type PositionFormData = {
    name: string;
    description: string;
    permissions: number[];
};

function groupLabel(group: string): string {
    const label = group.replace(/-/g, " ");

    return label.charAt(0).toLocaleUpperCase("pt-BR") + label.slice(1);
}

function groupSlug(group: string, index: number): string {
    const slug = group
        .normalize("NFD")
        .replace(/[̀-ͯ]/g, "")
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-|-$/g, "");

    return `${slug || "grupo"}-${index}`;
}

export default function PositionForm({
    position,
    permissions,
}: {
    position?: PositionRecord;
    permissions: GroupedPositionPermissions;
}): React.JSX.Element {
    const editing = Boolean(position);
    const form = useForm<PositionFormData>({
        name: position?.name ?? "",
        description: position?.description ?? "",
        permissions: position?.permissions?.map((permission) => permission.id) ?? [],
    });
    const errors = form.errors as Record<string, string | undefined>;
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const cancelHref = position ? `${positionsBase}/${position.id}/show` : positionsBase;
    const groups = Object.entries(permissions);
    const permissionsError = errors.permissions
        ?? Object.entries(errors).find(([key]) => key.startsWith("permissions."))?.[1];

    function togglePermissions(ids: number[], checked: boolean): void {
        form.setData((current) => ({
            ...current,
            permissions: checked
                ? Array.from(new Set([...current.permissions, ...ids]))
                : current.permissions.filter((id) => !ids.includes(id)),
        }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        const options = {
            preserveScroll: true,
            onError: () => {
                requestAnimationFrame(() => {
                    element.current
                        ?.querySelector<HTMLElement>('[aria-invalid="true"]')
                        ?.focus();
                });
            },
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
                        ? "Não foi possível salvar o cargo. Tente novamente."
                        : "Não foi possível cadastrar o cargo. Tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        };

        if (position) {
            form.put(`${positionsBase}/${position.id}`, options);
        } else {
            form.post(`${positionsBase}/store`, options);
        }
    }

    return (
        <FormLayout
            ref={element}
            className="positionForm"
            aria-label={editing ? "Editar cargo" : "Cadastrar cargo"}
            aria-busy={form.processing}
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="position-information"
                        title="Informações do Cargo"
                        variant="inline"
                        iconClass="fa fa-briefcase"
                    >
                        <FormGrid columns={2}>
                            <FormField
                                id="position-name"
                                label="Nome do Cargo"
                                requiredIndicator
                                error={errors.name}
                            >
                                <TextInput
                                    id="position-name"
                                    name="name"
                                    required
                                    maxLength={255}
                                    placeholder="Ex: Professor AEE, Psicólogo..."
                                    value={form.data.name}
                                    invalid={!!errors.name}
                                    aria-describedby={errors.name ? "position-name-error" : undefined}
                                    onChange={(event) => form.setData("name", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField
                                    id="position-description"
                                    label="Descrição"
                                    error={errors.description}
                                >
                                    <TextArea
                                        id="position-description"
                                        name="description"
                                        rows={3}
                                        placeholder="Breve descrição das responsabilidades..."
                                        value={form.data.description}
                                        aria-invalid={!!errors.description || undefined}
                                        aria-describedby={errors.description ? "position-description-error" : undefined}
                                        onChange={(event) => form.setData("description", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="position-permissions"
                        title="Permissões do Cargo"
                        variant="inline"
                        iconClass="fa fa-key"
                    >
                        {permissionsError ? (
                            <p
                                id="position-permissions-error"
                                className="positionForm__error"
                                role="alert"
                            >
                                {permissionsError}
                            </p>
                        ) : null}
                        {groups.length === 0 ? (
                            <p className="positionForm__empty">
                                Nenhuma permissão cadastrada.
                            </p>
                        ) : (
                            <div className="positionForm__groups">
                                {groups.map(([group, items], index) => {
                                    const slug = groupSlug(group, index);
                                    const ids = items.map((permission) => permission.id);
                                    const allChecked = ids.length > 0
                                        && ids.every((id) => form.data.permissions.includes(id));
                                    const label = groupLabel(group);

                                    return (
                                        <div
                                            key={group}
                                            className="positionForm__group"
                                            role="group"
                                            aria-labelledby={`position-group-${slug}-title`}
                                        >
                                            <div className="positionForm__groupHeader">
                                                <h3
                                                    id={`position-group-${slug}-title`}
                                                    className="positionForm__groupTitle"
                                                >
                                                    {label}
                                                </h3>
                                                <ChoiceControl
                                                    type="checkbox"
                                                    id={`position-group-${slug}-all`}
                                                    label="Selecionar Todas"
                                                    aria-label={`Selecionar todas as permissões de ${label}`}
                                                    checked={allChecked}
                                                    disabled={form.processing}
                                                    className="positionForm__checkAll"
                                                    onChange={(checked) => togglePermissions(ids, checked)}
                                                />
                                            </div>
                                            <div className="positionForm__groupBody">
                                                <FormGrid columns={4}>
                                                    {items.map((permission) => (
                                                        <ChoiceControl
                                                            key={permission.id}
                                                            type="checkbox"
                                                            id={`position-permission-${permission.id}`}
                                                            name="permissions[]"
                                                            label={permission.name}
                                                            checked={form.data.permissions.includes(permission.id)}
                                                            disabled={form.processing}
                                                            className="positionForm__permission"
                                                            onChange={(checked) => togglePermissions([permission.id], checked)}
                                                        />
                                                    ))}
                                                </FormGrid>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}
                    </FormSection>
                </fieldset>
            </CardBox>
            <FormActions
                status={form.processing
                    ? (editing ? "Salvando alterações do cargo..." : "Cadastrando cargo...")
                    : undefined}
            >
                <Button
                    href={cancelHref}
                    tone="cancel"
                    label="Cancelar"
                    title="Cancelar e voltar"
                    aria-label="Cancelar e voltar"
                    disabled={form.processing}
                />
                <Button
                    type="submit"
                    tone="save"
                    leftIconClass="fa fa-check"
                    label="Salvar"
                    title="Salvar cargo"
                    aria-label={form.processing ? "Salvando cargo" : "Salvar cargo"}
                    disabled={form.processing}
                />
            </FormActions>
        </FormLayout>
    );
}
