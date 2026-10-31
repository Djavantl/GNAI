import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
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
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import AccessPasswordFields from "@/components/People/AccessPasswordFields";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { ProfessionalFormOptions, ProfessionalRecord } from "./Professional.types";
import {
    cpfMask,
    phoneMask,
    professionalsBase,
    professionalShowUrl,
} from "./Professionals.utils";

type ProfessionalFormData = {
    name: string;
    document: string;
    birth_date: string;
    gender: string;
    email: string;
    phone: string;
    address: string;
    position_id: string;
    registration: string;
    entry_date: string;
    status: string;
    is_admin: boolean;
    show_on_website: boolean;
    photo: File | null;
    remove_photo: boolean;
    password: string;
    password_confirmation: string;
};

type TextFieldName = "name" | "document" | "email" | "phone" | "registration";
type DateFieldName = "birth_date" | "entry_date";
type SelectFieldName = "gender" | "position_id" | "status";

type Props = ProfessionalFormOptions & (
    | { mode: "create"; professional?: never }
    | { mode: "edit"; professional: ProfessionalRecord }
);

export default function ProfessionalForm(props: Props): React.JSX.Element {
    const { professional, positions, genders, professionalStatuses } = props;
    const person = professional?.person;
    const editing = props.mode === "edit";
    // Somente o administrador superior concede ou retira o acesso de administrador interno.
    const canManageAdmin = props.canManageAdministrators;
    const isAdministrator = professional?.user?.is_admin === true;
    const form = useForm<ProfessionalFormData>({
        name: person?.name ?? "",
        document: cpfMask(person?.document ?? ""),
        birth_date: person?.birth_date?.slice(0, 10) ?? "",
        gender: person?.gender ?? props.defaultGender ?? "",
        email: person?.email ?? "",
        phone: phoneMask(person?.phone ?? ""),
        address: person?.address ?? "",
        position_id: professional?.position_id != null ? String(professional.position_id) : "",
        registration: professional?.registration ?? "",
        entry_date: professional?.entry_date?.slice(0, 10) ?? "",
        status: professional?.status ?? props.defaultStatus ?? "",
        is_admin: professional?.user?.is_admin === true,
        show_on_website: professional?.show_on_website === true,
        photo: null,
        remove_photo: false,
        password: "",
        password_confirmation: "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [photoValidating, setPhotoValidating] = useState(false);
    const cancelHref = professional ? professionalShowUrl(professional.id) : professionalsBase;
    const describedBy = (name: string): string | undefined => (
        errors[name] ? `professional-${name}-error` : undefined
    );

    const textField = (
        name: TextFieldName,
        label: string,
        options: {
            type?: string;
            required?: boolean;
            maxLength?: number;
            placeholder?: string;
        } = {},
    ) => (
        <FormField
            id={`professional-${name}`}
            label={label}
            error={errors[name]}
            requiredIndicator={options.required}
        >
            <TextInput
                id={`professional-${name}`}
                name={name}
                type={options.type ?? "text"}
                required={options.required}
                maxLength={options.maxLength}
                placeholder={options.placeholder}
                inputMode={name === "document" || name === "phone" ? "numeric" : undefined}
                value={form.data[name]}
                invalid={!!errors[name]}
                aria-describedby={describedBy(name)}
                onChange={(event) => form.setData(
                    name,
                    name === "document"
                        ? cpfMask(event.target.value)
                        : name === "phone"
                            ? phoneMask(event.target.value)
                            : event.target.value,
                )}
            />
        </FormField>
    );

    const dateField = (name: DateFieldName, label: string) => (
        <FormField
            id={`professional-${name}`}
            label={label}
            error={errors[name]}
            requiredIndicator
        >
            <DatePicker
                id={`professional-${name}`}
                name={name}
                label={label}
                value={form.data[name]}
                required
                disabled={form.processing}
                invalid={!!errors[name]}
                describedBy={describedBy(name)}
                onChange={(value) => form.setData(name, value)}
            />
        </FormField>
    );

    const selectField = (
        name: SelectFieldName,
        label: string,
        options: Array<{ value: string; label: string }>,
    ) => (
        <FormField
            id={`professional-${name}`}
            label={label}
            error={errors[name]}
            requiredIndicator
        >
            <SelectField
                id={`professional-${name}`}
                name={name}
                label={label}
                required
                disabled={form.processing}
                value={form.data[name]}
                options={options}
                aria-invalid={!!errors[name]}
                aria-describedby={describedBy(name)}
                onChange={(event) => form.setData(name, event.target.value)}
            />
        </FormField>
    );

    const recordOptions = (record: Record<string, string>) => Object.entries(record)
        .map(([value, label]) => ({ value, label }));

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing || photoValidating) return;
        busy.current = true;

        form.transform((data) => {
            const {
                status,
                remove_photo: removePhoto,
                is_admin: isAdmin,
                password,
                password_confirmation: passwordConfirmation,
                ...common
            } = data;
            const payload: Record<string, unknown> = { ...common };

            if (canManageAdmin) payload.is_admin = isAdmin;
            if (editing) {
                payload.status = status;
                payload.remove_photo = removePhoto;
                payload._method = "put";
            } else {
                payload.password = password;
                payload.password_confirmation = passwordConfirmation;
            }

            return payload;
        });

        form.post(
            professional ? `${professionalsBase}/${professional.id}` : `${professionalsBase}/store`,
            {
                forceFormData: true,
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
                            ? "Não foi possível salvar o profissional. Tente novamente."
                            : "Não foi possível cadastrar o profissional. Tente novamente.",
                    });
                    return false;
                },
                onFinish: () => {
                    busy.current = false;
                    form.setData((data) => ({
                        ...data,
                        password: "",
                        password_confirmation: "",
                    }));
                },
            },
        );
    }

    return (
        <FormLayout
            ref={element}
            className="professionalForm"
            aria-label={editing ? "Editar profissional" : "Cadastrar profissional"}
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="professional-person"
                        title="Dados da Pessoa"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="professionalForm__personal">
                            <PersonPhotoUpload
                                id="professional-photo"
                                name="photo"
                                label="Foto do Profissional"
                                current={person?.photo ? person.photo_url : undefined}
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
                                        remove_photo: editing,
                                    }));
                                    form.clearErrors("photo");
                                }}
                                onValidatingChange={setPhotoValidating}
                                onError={(message) => form.setError("photo", message)}
                            />
                            <div className="professionalForm__personalFields">
                                <FormGrid columns={2}>
                                    {textField("name", "Nome", { required: true, maxLength: 255 })}
                                    {textField("email", "E-mail", {
                                        type: "email",
                                        required: true,
                                        maxLength: 255,
                                    })}
                                </FormGrid>
                                <FormGrid columns={4}>
                                    {textField("document", "CPF", {
                                        maxLength: 14,
                                        placeholder: "000.000.000-00",
                                    })}
                                    {dateField("birth_date", "Nascimento")}
                                    {selectField("gender", "Gênero", recordOptions(genders))}
                                    {textField("phone", "Telefone", {
                                        type: "tel",
                                        maxLength: 15,
                                        placeholder: "(00) 00000-0000",
                                    })}
                                </FormGrid>
                            </div>
                        </div>
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <FormField
                                    id="professional-address"
                                    label="Endereço"
                                    error={errors.address}
                                >
                                    <TextArea
                                        id="professional-address"
                                        name="address"
                                        rows={2}
                                        maxLength={500}
                                        value={form.data.address}
                                        aria-invalid={!!errors.address || undefined}
                                        aria-describedby={describedBy("address")}
                                        onChange={(event) => form.setData("address", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="professional-work"
                        title="Dados do Profissional"
                        variant="inline"
                        iconClass="fa fa-id-badge"
                    >
                        <FormGrid columns={editing ? 4 : 3}>
                            {selectField(
                                "position_id",
                                "Cargo",
                                positions.map((position) => ({
                                    value: String(position.id),
                                    label: position.name,
                                })),
                            )}
                            {textField("registration", "Matrícula", { required: true, maxLength: 50 })}
                            {dateField("entry_date", "Data de Ingresso")}
                            {editing
                                ? selectField("status", "Status", recordOptions(professionalStatuses))
                                : null}
                        </FormGrid>
                        <div className="professionalForm__website">
                            <ToggleControl
                                id="professional-show-on-website"
                                name="show_on_website"
                                size="small"
                                label="Aparece no site"
                                labelAddon={(
                                    <InfoTooltip text="Exibe o profissional, com foto, nome e cargo, na página de equipe do site. Profissionais inativos não aparecem." />
                                )}
                                checked={form.data.show_on_website}
                                disabled={form.processing}
                                onChange={(checked) => form.setData("show_on_website", checked)}
                            />
                            {errors.show_on_website ? (
                                <p className="professionalForm__error" role="alert">
                                    {errors.show_on_website}
                                </p>
                            ) : null}
                        </div>
                        {canManageAdmin ? (
                            <div className="professionalForm__admin">
                                <ChoiceControl
                                    type="checkbox"
                                    id="professional-is-admin"
                                    name="is_admin"
                                    label="Definir este profissional como administrador interno da unidade"
                                    checked={form.data.is_admin}
                                    disabled={form.processing}
                                    onChange={(checked) => form.setData("is_admin", checked)}
                                />
                                <p className="formField__hint">
                                    Somente o administrador superior do Gnai pode conceder ou retirar este acesso.
                                </p>
                                {errors.is_admin ? (
                                    <p
                                        className="professionalForm__error"
                                        role="alert"
                                    >
                                        {errors.is_admin}
                                    </p>
                                ) : null}
                            </div>
                        ) : isAdministrator ? (
                            <p className="professionalForm__admin formField__hint">
                                Este profissional é administrador interno da unidade. Somente o administrador
                                superior do Gnai pode alterar esse acesso, desativá-lo ou removê-lo.
                            </p>
                        ) : null}
                    </FormSection>
                    {editing ? null : (
                        <FormSection
                            id="professional-access"
                            title="Acesso ao Sistema"
                            variant="inline"
                            iconClass="fa fa-lock"
                        >
                            <AccessPasswordFields
                                idPrefix="professional"
                                password={form.data.password}
                                confirmation={form.data.password_confirmation}
                                error={errors.password}
                                onChange={(field, value) => form.setData(field, value)}
                            />
                        </FormSection>
                    )}
                </fieldset>
            </CardBox>
            <FormActions
                status={photoValidating
                    ? "Preparando a foto selecionada..."
                    : form.processing
                        ? form.progress
                            ? `Enviando foto: ${form.progress.percentage}%`
                            : "Salvando dados do profissional..."
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
                    title="Salvar profissional"
                    aria-label={form.processing ? "Salvando profissional" : "Salvar profissional"}
                    disabled={form.processing || photoValidating}
                />
            </FormActions>
        </FormLayout>
    );
}
