import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import Badge from "@/components/UI/Badge/Badge";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import PersonPhotoUpload from "@/components/Forms/PersonPhotoUpload/PersonPhotoUpload";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { ProfileEditProps } from "./Profile.types";
import {
    cpfMask,
    dashboardUrl,
    genderOptions,
    phoneMask,
    profileUrl,
} from "./Profile.utils";

type ProfileFormData = {
    name: string;
    document: string;
    birth_date: string;
    gender: string;
    phone: string;
    email: string;
    registration: string;
    address: string;
    photo: File | null;
    remove_photo: boolean;
    password: string;
    password_confirmation: string;
};

type TextFieldName =
    | "name"
    | "document"
    | "phone"
    | "email"
    | "registration"
    | "password"
    | "password_confirmation";

export default function ProfileForm({
    person,
    professional,
    teacher,
}: ProfileEditProps): React.JSX.Element {
    const form = useForm<ProfileFormData>({
        name: person.name ?? "",
        document: person.document ?? "",
        birth_date: person.birth_date?.slice(0, 10) ?? "",
        gender: person.gender ?? "",
        phone: person.phone ?? "",
        email: person.email ?? "",
        registration:
            professional?.registration ?? teacher?.registration ?? "",
        address: person.address ?? "",
        photo: null,
        remove_photo: false,
        password: "",
        password_confirmation: "",
    });
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [photoValidating, setPhotoValidating] = useState(false);
    const errors = form.errors as Partial<Record<keyof ProfileFormData, string>>;
    const describedBy = (id: string, error?: string) =>
        error ? `${id}-error` : undefined;

    const input = (
        name: TextFieldName,
        label: string,
        options: {
            type?: string;
            required?: boolean;
            maxLength?: number;
            placeholder?: string;
            autoComplete?: string;
            inputMode?: "numeric" | "tel" | "email";
        } = {},
    ) => {
        const id = `profile-${name.replace("_", "-")}`;

        return (
            <FormField
                id={id}
                label={label}
                error={errors[name]}
                requiredIndicator={options.required}
            >
                <TextInput
                    id={id}
                    name={name}
                    type={options.type ?? "text"}
                    required={options.required}
                    maxLength={options.maxLength}
                    placeholder={options.placeholder}
                    autoComplete={options.autoComplete}
                    inputMode={options.inputMode}
                    value={form.data[name]}
                    invalid={!!errors[name]}
                    aria-describedby={describedBy(id, errors[name])}
                    onChange={(event) =>
                        form.setData(
                            name,
                            name === "document"
                                ? cpfMask(event.target.value)
                                : name === "phone"
                                  ? phoneMask(event.target.value)
                                  : event.target.value,
                        )
                    }
                />
            </FormField>
        );
    };

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();

        if (busy.current || form.processing || photoValidating) return;

        busy.current = true;
        form.transform((data) => ({ ...data, _method: "put" }));
        form.post(profileUrl, {
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
                        id="profile-personal"
                        title="Dados Pessoais"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="profileForm__personal">
                            <div className="profileForm__identity">
                                <PersonPhotoUpload
                                    id="profile-photo"
                                    name="photo"
                                    label="Alterar Foto"
                                    current={
                                        person.photo ? person.photo_url : undefined
                                    }
                                    file={form.data.photo}
                                    removed={form.data.remove_photo}
                                    error={errors.photo ?? errors.remove_photo}
                                    disabled={form.processing}
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
                                    onError={(message) =>
                                        form.setError("photo", message)
                                    }
                                />
                                <p className="profileForm__name">
                                    {person.name}
                                </p>
                                <Badge
                                    variant="purple"
                                    className="profileForm__position"
                                >
                                    {professional
                                        ? professional.position?.name ?? "---"
                                        : "Professor(a)"}
                                </Badge>
                                <dl className="profileForm__bond">
                                    <dt>Vínculo</dt>
                                    <dd>
                                        {professional
                                            ? "Profissional Apoio"
                                            : "Corpo Docente"}
                                    </dd>
                                </dl>
                            </div>
                            <div className="profileForm__fields">
                                <FormGrid columns={2}>
                                    {input("name", "Nome Completo", {
                                        required: true,
                                        maxLength: 255,
                                        autoComplete: "name",
                                    })}
                                    {input("email", "E-mail", {
                                        type: "email",
                                        required: true,
                                        maxLength: 255,
                                        autoComplete: "email",
                                    })}
                                </FormGrid>
                                <FormGrid columns={4}>
                                    {input("document", "CPF", {
                                        maxLength: 14,
                                        placeholder: "000.000.000-00",
                                        inputMode: "numeric",
                                        autoComplete: "off",
                                    })}
                                    <FormField
                                        id="profile-birth-date"
                                        label="Data de Nascimento"
                                        error={errors.birth_date}
                                        requiredIndicator
                                    >
                                        <DatePicker
                                            id="profile-birth-date"
                                            name="birth_date"
                                            label="Data de Nascimento"
                                            value={form.data.birth_date}
                                            required
                                            disabled={form.processing}
                                            invalid={!!errors.birth_date}
                                            describedBy={describedBy(
                                                "profile-birth-date",
                                                errors.birth_date,
                                            )}
                                            onChange={(value) =>
                                                form.setData("birth_date", value)
                                            }
                                        />
                                    </FormField>
                                    <FormField
                                        id="profile-gender"
                                        label="Gênero"
                                        error={errors.gender}
                                        requiredIndicator
                                    >
                                        <SelectField
                                            id="profile-gender"
                                            name="gender"
                                            label="Gênero"
                                            required
                                            disabled={form.processing}
                                            value={form.data.gender}
                                            aria-invalid={!!errors.gender}
                                            aria-describedby={describedBy(
                                                "profile-gender",
                                                errors.gender,
                                            )}
                                            options={genderOptions}
                                            onChange={(event) =>
                                                form.setData(
                                                    "gender",
                                                    event.target.value,
                                                )
                                            }
                                        />
                                    </FormField>
                                    {input("phone", "Telefone", {
                                        type: "tel",
                                        maxLength: 15,
                                        placeholder: "(00) 00000-0000",
                                        inputMode: "numeric",
                                        autoComplete: "tel",
                                    })}
                                </FormGrid>
                                <FormGrid columns={2}>
                                    {input("registration", "Matrícula", {
                                        required: true,
                                        maxLength: 50,
                                        autoComplete: "off",
                                    })}
                                    <FormGridWide>
                                        <FormField
                                            id="profile-address"
                                            label="Endereço"
                                            error={errors.address}
                                        >
                                            <TextArea
                                                id="profile-address"
                                                name="address"
                                                rows={2}
                                                maxLength={500}
                                                autoComplete="street-address"
                                                value={form.data.address}
                                                aria-invalid={!!errors.address}
                                                aria-describedby={describedBy(
                                                    "profile-address",
                                                    errors.address,
                                                )}
                                                onChange={(event) =>
                                                    form.setData(
                                                        "address",
                                                        event.target.value,
                                                    )
                                                }
                                            />
                                        </FormField>
                                    </FormGridWide>
                                </FormGrid>
                            </div>
                        </div>
                    </FormSection>
                    <FormSection
                        id="profile-password"
                        title="Alterar Senha"
                        variant="inline"
                        iconClass="fa fa-lock"
                    >
                        <FormGrid columns={2}>
                            {input("password", "Nova Senha", {
                                type: "password",
                                autoComplete: "new-password",
                            })}
                            {input(
                                "password_confirmation",
                                "Confirmar Nova Senha",
                                {
                                    type: "password",
                                    autoComplete: "new-password",
                                },
                            )}
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
                    aria-label={
                        form.processing
                            ? "Salvando alterações"
                            : "Salvar alterações"
                    }
                    disabled={form.processing || photoValidating}
                />
            </FormActions>
        </FormLayout>
    );
}
