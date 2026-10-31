import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import Badge from "@/components/UI/Badge/Badge";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import PersonPhotoUpload from "@/components/Forms/PersonPhotoUpload/PersonPhotoUpload";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { ProfileAccountProps } from "./Profile.types";
import { dashboardUrl, profileUrl } from "./Profile.utils";

type AccountFormData = {
    name: string;
    email: string;
    photo: File | null;
    remove_photo: boolean;
    current_password: string;
    password: string;
    password_confirmation: string;
};

type TextFieldName = Exclude<keyof AccountFormData, "photo" | "remove_photo">;

const accountProfileUrl = `${profileUrl}/conta`;

export default function AccountProfileForm({
    account,
}: ProfileAccountProps): React.JSX.Element {
    const form = useForm<AccountFormData>({
        name: account.name ?? "",
        email: account.email ?? "",
        photo: null,
        remove_photo: false,
        current_password: "",
        password: "",
        password_confirmation: "",
    });
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [photoValidating, setPhotoValidating] = useState(false);
    const errors = form.errors as Partial<Record<keyof AccountFormData, string>>;

    const input = (
        name: TextFieldName,
        label: string,
        options: {
            type?: string;
            required?: boolean;
            maxLength?: number;
            autoComplete?: string;
            hint?: string;
        } = {},
    ) => {
        const id = `account-${name.replaceAll("_", "-")}`;
        const describedBy = [
            options.hint ? `${id}-hint` : null,
            errors[name] ? `${id}-error` : null,
        ]
            .filter(Boolean)
            .join(" ");

        return (
            <FormField
                id={id}
                label={label}
                hint={options.hint}
                error={errors[name]}
                requiredIndicator={options.required}
            >
                <TextInput
                    id={id}
                    name={name}
                    type={options.type ?? "text"}
                    required={options.required}
                    maxLength={options.maxLength}
                    autoComplete={options.autoComplete}
                    value={form.data[name]}
                    invalid={!!errors[name]}
                    aria-describedby={describedBy || undefined}
                    onChange={(event) => form.setData(name, event.target.value)}
                />
            </FormField>
        );
    };

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();

        if (busy.current || form.processing || photoValidating) return;

        busy.current = true;
        form.transform((data) => ({ ...data, _method: "put" }));
        form.post(accountProfileUrl, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: (page) => {
                const flash = (page.props as { flash?: { error?: string | null } })
                    .flash;

                if (flash?.error) return;

                form.setData((data) => ({
                    ...data,
                    photo: null,
                    remove_photo: false,
                    current_password: "",
                    password: "",
                    password_confirmation: "",
                }));
            },
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
                    text: "Não foi possível salvar o perfil. Atualize a página e tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        });
    }

    return (
        <FormLayout
            ref={element}
            className="profileForm"
            aria-label="Editar meu perfil"
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="account-data"
                        title="Dados da Conta"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="profileForm__personal">
                            <div className="profileForm__identity">
                                <PersonPhotoUpload
                                    id="account-photo"
                                    name="photo"
                                    label="Alterar Foto"
                                    current={account.photo ? account.photo_url : undefined}
                                    file={form.data.photo}
                                    removed={form.data.remove_photo}
                                    error={errors.photo ?? errors.remove_photo}
                                    disabled={form.processing}
                                    acceptedTypes={["image/jpeg", "image/png"]}
                                    maxSizeBytes={2 * 1024 * 1024}
                                    onChange={(photo) => {
                                        form.setData((data) => ({
                                            ...data,
                                            photo,
                                            remove_photo: false,
                                        }));
                                        form.clearErrors("photo", "remove_photo");
                                    }}
                                    onRemove={() => {
                                        form.setData((data) => ({
                                            ...data,
                                            photo: null,
                                            remove_photo: true,
                                        }));
                                        form.clearErrors("photo");
                                    }}
                                    onValidatingChange={setPhotoValidating}
                                    onError={(message) => form.setError("photo", message)}
                                />
                                <p className="profileForm__name">{account.name}</p>
                                <Badge
                                    variant="purple"
                                    className="profileForm__position"
                                >
                                    {account.is_super_admin
                                        ? "Administrador superior"
                                        : account.is_admin
                                          ? "Administrador"
                                          : "Conta de acesso"}
                                </Badge>
                                <dl className="profileForm__bond">
                                    <dt>Vínculo</dt>
                                    <dd>Conta administrativa do Gnai</dd>
                                </dl>
                            </div>
                            <div className="profileForm__fields">
                                <FormGrid columns={2}>
                                    {input("name", "Nome", {
                                        required: true,
                                        maxLength: 255,
                                        autoComplete: "name",
                                    })}
                                    {input("email", "E-mail de acesso", {
                                        type: "email",
                                        required: true,
                                        maxLength: 255,
                                        autoComplete: "email",
                                        hint: "Usado para entrar no Gnai. Para alterá-lo, informe a senha atual.",
                                    })}
                                </FormGrid>
                            </div>
                        </div>
                    </FormSection>
                    <FormSection
                        id="account-password"
                        title="Segurança"
                        variant="inline"
                        iconClass="fa fa-lock"
                    >
                        <FormGrid columns={3}>
                            {input("current_password", "Senha atual", {
                                type: "password",
                                autoComplete: "current-password",
                                hint: "Obrigatória para alterar o e-mail ou a senha.",
                            })}
                            {input("password", "Nova senha", {
                                type: "password",
                                autoComplete: "new-password",
                                hint: "Mínimo de 12 caracteres, com maiúsculas, minúsculas, números e símbolos. Deixe em branco para manter.",
                            })}
                            {input("password_confirmation", "Confirmar nova senha", {
                                type: "password",
                                autoComplete: "new-password",
                            })}
                        </FormGrid>
                    </FormSection>
                </fieldset>
            </CardBox>
            <FormActions
                status={
                    photoValidating
                        ? "Preparando a foto selecionada..."
                        : form.processing
                          ? form.progress
                              ? `Enviando foto: ${form.progress.percentage}%`
                              : "Salvando alterações..."
                          : undefined
                }
            >
                <Button
                    href={dashboardUrl}
                    tone="cancel"
                    label="Cancelar"
                    title="Cancelar e voltar ao início"
                    aria-label="Cancelar e voltar ao início"
                    disabled={form.processing}
                />
                <Button
                    type="submit"
                    tone="save"
                    label="Salvar"
                    title="Salvar alterações"
                    aria-label={form.processing ? "Salvando alterações" : "Salvar alterações"}
                    disabled={form.processing || photoValidating}
                />
            </FormActions>
        </FormLayout>
    );
}
