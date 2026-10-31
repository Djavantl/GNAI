import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
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
import AccessPasswordFields from "@/components/People/AccessPasswordFields";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import type { TeacherRecord } from "./Teacher.types";
import {
    cpfMask,
    phoneMask,
    teacherGenderOptions,
    teachersBase,
    teacherShowUrl,
} from "./Teachers.utils";

type TeacherFormData = {
    name: string;
    document: string;
    birth_date: string;
    gender: string;
    email: string;
    phone: string;
    address: string;
    registration: string;
    photo: File | null;
    remove_photo: boolean;
    password: string;
    password_confirmation: string;
};

type TextFieldName = "name" | "document" | "email" | "phone" | "registration";

type Props =
    | { mode: "create"; teacher?: never }
    | { mode: "edit"; teacher: TeacherRecord };

export default function TeacherForm({ mode, teacher }: Props): React.JSX.Element {
    const person = teacher?.person;
    const editing = mode === "edit";
    const form = useForm<TeacherFormData>({
        name: person?.name ?? "",
        document: cpfMask(person?.document ?? ""),
        birth_date: person?.birth_date?.slice(0, 10) ?? "",
        gender: person?.gender ?? "not_specified",
        email: person?.email ?? "",
        phone: phoneMask(person?.phone ?? ""),
        address: person?.address ?? "",
        registration: teacher?.registration ?? "",
        photo: null,
        remove_photo: false,
        password: "",
        password_confirmation: "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [photoValidating, setPhotoValidating] = useState(false);
    const cancelHref = teacher ? teacherShowUrl(teacher.id) : teachersBase;
    const describedBy = (name: string): string | undefined => (
        errors[name] ? `teacher-${name}-error` : undefined
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
            id={`teacher-${name}`}
            label={label}
            error={errors[name]}
            requiredIndicator={options.required}
        >
            <TextInput
                id={`teacher-${name}`}
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

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing || photoValidating) return;
        busy.current = true;

        form.transform((data) => {
            const {
                remove_photo: removePhoto,
                password,
                password_confirmation: passwordConfirmation,
                ...common
            } = data;

            // A senha inicial é opcional e só existe no cadastro.
            return editing
                ? { ...common, remove_photo: removePhoto, _method: "put" }
                : { ...common, password, password_confirmation: passwordConfirmation };
        });

        form.post(
            teacher ? `${teachersBase}/${teacher.id}` : `${teachersBase}/store`,
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
                            ? "Não foi possível atualizar o professor. Tente novamente."
                            : "Não foi possível cadastrar o professor. Tente novamente.",
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
            className="teacherForm"
            aria-label={editing ? "Editar professor" : "Cadastrar professor"}
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="teacher-personal"
                        title="Dados Pessoais"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="teacherForm__personal">
                            <PersonPhotoUpload
                                id="teacher-photo"
                                name="photo"
                                label="Foto do Professor"
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
                            <div className="teacherForm__personalFields">
                                <FormGrid columns={2}>
                                    {textField("name", "Nome Completo", { required: true, maxLength: 255 })}
                                    {textField("email", editing ? "E-mail" : "E-mail de Acesso", {
                                        type: "email",
                                        required: true,
                                    })}
                                </FormGrid>
                                <FormGrid columns={4}>
                                    {textField("document", "CPF", {
                                        maxLength: 14,
                                        placeholder: "000.000.000-00",
                                    })}
                                    <FormField
                                        id="teacher-birth_date"
                                        label="Data de Nascimento"
                                        error={errors.birth_date}
                                        requiredIndicator
                                    >
                                        <DatePicker
                                            id="teacher-birth_date"
                                            name="birth_date"
                                            label="Data de Nascimento"
                                            value={form.data.birth_date}
                                            required
                                            disabled={form.processing}
                                            invalid={!!errors.birth_date}
                                            describedBy={describedBy("birth_date")}
                                            onChange={(value) => form.setData("birth_date", value)}
                                        />
                                    </FormField>
                                    <FormField
                                        id="teacher-gender"
                                        label="Gênero"
                                        error={errors.gender}
                                        requiredIndicator
                                    >
                                        <SelectField
                                            id="teacher-gender"
                                            name="gender"
                                            label="Gênero"
                                            required
                                            disabled={form.processing}
                                            value={form.data.gender}
                                            options={teacherGenderOptions}
                                            aria-invalid={!!errors.gender}
                                            aria-describedby={describedBy("gender")}
                                            onChange={(event) => form.setData("gender", event.target.value)}
                                        />
                                    </FormField>
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
                                    id="teacher-address"
                                    label={editing ? "Endereço" : "Endereço Residencial"}
                                    error={errors.address}
                                >
                                    <TextArea
                                        id="teacher-address"
                                        name="address"
                                        rows={2}
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
                        id="teacher-teaching"
                        title="Dados Docentes"
                        variant="inline"
                        iconClass="fa fa-graduation-cap"
                    >
                        <FormGrid columns={2}>
                            {textField(
                                "registration",
                                editing ? "Matrícula" : "Número de Matrícula",
                                { required: true, maxLength: 50 },
                            )}
                        </FormGrid>
                    </FormSection>
                    {editing ? null : (
                        <FormSection
                            id="teacher-access"
                            title="Acesso ao Sistema"
                            variant="inline"
                            iconClass="fa fa-lock"
                        >
                            <AccessPasswordFields
                                idPrefix="teacher"
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
                            : "Salvando dados do professor..."
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
                    title={editing ? "Atualizar professor" : "Salvar professor"}
                    aria-label={form.processing
                        ? "Salvando professor"
                        : editing ? "Atualizar professor" : "Salvar professor"}
                    disabled={form.processing || photoValidating}
                />
            </FormActions>
        </FormLayout>
    );
}
