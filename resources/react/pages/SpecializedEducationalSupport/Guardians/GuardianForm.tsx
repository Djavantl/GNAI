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
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import {
    cpfMask,
    phoneMask,
    studentRecordUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
import type { GuardianFormOptions, GuardianRecord } from "./Guardian.types";

type GuardianFormData = {
    name: string;
    document: string;
    birth_date: string;
    gender: string;
    email: string;
    phone: string;
    relationship: string;
    address: string;
    photo: File | null;
    remove_photo: boolean;
};

type GuardianFormProps = GuardianFormOptions &
    (
        | { mode: "create"; defaultGender?: string; guardian?: never }
        | { mode: "edit"; guardian: GuardianRecord; defaultGender?: never }
    );

const photoTypes = ["image/jpeg", "image/png", "image/webp"] as const;

function toOptions(options: Record<string, string>) {
    return Object.entries(options).map(([value, label]) => ({ value, label }));
}

export default function GuardianForm(props: GuardianFormProps): React.JSX.Element {
    const { student, genders, relationships, guardian } = props;
    const person = guardian?.person;
    const form = useForm<GuardianFormData>({
        name: person?.name ?? "",
        document: person?.document ?? "",
        birth_date: person?.birth_date?.slice(0, 10) ?? "",
        gender: person?.gender ?? props.defaultGender ?? "",
        email: person?.email ?? "",
        phone: person?.phone ?? "",
        relationship: guardian?.relationship ?? "",
        address: person?.address ?? "",
        photo: null,
        remove_photo: false,
    });
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [photoValidating, setPhotoValidating] = useState(false);
    const cancelUrl = studentRecordUrl(student.id, "guardians");
    const errors = form.errors as Partial<Record<keyof GuardianFormData, string>>;
    const describedBy = (field: keyof GuardianFormData) =>
        errors[field] ? `guardian-${field.replace("_", "-")}-error` : undefined;

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing || photoValidating) return;
        busy.current = true;

        form.transform((data) => {
            if (props.mode === "edit") return { ...data, _method: "put" };
            const { remove_photo: _removePhoto, ...creation } = data;
            return creation;
        });

        form.post(
            guardian
                ? `${studentsBase}/${student.id}/guardians/${guardian.id}`
                : `${studentsBase}/${student.id}/guardians/store`,
            {
                forceFormData: true,
                preserveScroll: true,
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
                        text: guardian
                            ? "Não foi possível atualizar o responsável. Atualize a página e tente novamente."
                            : "Não foi possível cadastrar o responsável. Atualize a página e tente novamente.",
                    });
                    return false;
                },
                onFinish: () => {
                    busy.current = false;
                },
            },
        );
    }

    return (
        <FormLayout
            ref={element}
            className="guardianForm"
            aria-label={guardian ? "Editar responsável" : "Cadastrar responsável"}
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="guardian-personal"
                        title="Dados Pessoais"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="guardianForm__personal">
                            <PersonPhotoUpload
                                id="guardian-photo"
                                name="photo"
                                label="Foto do Responsável"
                                current={person?.photo ? person.photo_url : undefined}
                                file={form.data.photo}
                                removed={form.data.remove_photo}
                                acceptedTypes={photoTypes}
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
                                        remove_photo: props.mode === "edit",
                                    }));
                                    form.clearErrors("photo");
                                }}
                                onValidatingChange={setPhotoValidating}
                                onError={(message) => form.setError("photo", message)}
                            />
                            <FormGrid columns={2}>
                                <FormField
                                    id="guardian-name"
                                    label="Nome Completo"
                                    error={errors.name}
                                    requiredIndicator
                                >
                                    <TextInput
                                        id="guardian-name"
                                        name="name"
                                        required
                                        minLength={3}
                                        maxLength={255}
                                        value={form.data.name}
                                        invalid={!!errors.name}
                                        aria-describedby={describedBy("name")}
                                        onChange={(event) => form.setData("name", event.target.value)}
                                    />
                                </FormField>
                                <FormField
                                    id="guardian-document"
                                    label="CPF"
                                    error={errors.document}
                                >
                                    <TextInput
                                        id="guardian-document"
                                        name="document"
                                        inputMode="numeric"
                                        maxLength={14}
                                        placeholder="000.000.000-00"
                                        value={form.data.document}
                                        invalid={!!errors.document}
                                        aria-describedby={describedBy("document")}
                                        onChange={(event) => form.setData("document", cpfMask(event.target.value))}
                                    />
                                </FormField>
                                <FormField
                                    id="guardian-birth-date"
                                    label="Data de Nascimento"
                                    error={errors.birth_date}
                                    requiredIndicator
                                >
                                    <DatePicker
                                        id="guardian-birth-date"
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
                                    id="guardian-gender"
                                    label="Gênero"
                                    error={errors.gender}
                                    requiredIndicator
                                >
                                    <SelectField
                                        id="guardian-gender"
                                        name="gender"
                                        label="Gênero"
                                        required
                                        disabled={form.processing}
                                        value={form.data.gender}
                                        options={toOptions(genders)}
                                        aria-invalid={!!errors.gender}
                                        aria-describedby={describedBy("gender")}
                                        onChange={(event) => form.setData("gender", event.target.value)}
                                    />
                                </FormField>
                            </FormGrid>
                        </div>
                    </FormSection>
                    <FormSection
                        id="guardian-contact"
                        title="Contato e Vínculo"
                        variant="inline"
                        iconClass="fa fa-address-book"
                    >
                        <FormGrid columns={3}>
                            <FormField
                                id="guardian-email"
                                label="E-mail"
                                error={errors.email}
                            >
                                <TextInput
                                    id="guardian-email"
                                    name="email"
                                    type="email"
                                    maxLength={255}
                                    value={form.data.email}
                                    invalid={!!errors.email}
                                    aria-describedby={describedBy("email")}
                                    onChange={(event) => form.setData("email", event.target.value)}
                                />
                            </FormField>
                            <FormField
                                id="guardian-phone"
                                label="Telefone / WhatsApp"
                                error={errors.phone}
                                requiredIndicator
                            >
                                <TextInput
                                    id="guardian-phone"
                                    name="phone"
                                    type="tel"
                                    inputMode="numeric"
                                    required
                                    maxLength={15}
                                    placeholder="(00) 00000-0000"
                                    value={form.data.phone}
                                    invalid={!!errors.phone}
                                    aria-describedby={describedBy("phone")}
                                    onChange={(event) => form.setData("phone", phoneMask(event.target.value))}
                                />
                            </FormField>
                            <FormField
                                id="guardian-relationship"
                                label="Parentesco / Vínculo"
                                error={errors.relationship}
                                requiredIndicator
                            >
                                <SelectField
                                    id="guardian-relationship"
                                    name="relationship"
                                    label="Parentesco / Vínculo"
                                    required
                                    disabled={form.processing}
                                    value={form.data.relationship}
                                    options={toOptions(relationships)}
                                    aria-invalid={!!errors.relationship}
                                    aria-describedby={describedBy("relationship")}
                                    onChange={(event) => form.setData("relationship", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField
                                    id="guardian-address"
                                    label="Endereço Completo"
                                    error={errors.address}
                                >
                                    <TextArea
                                        id="guardian-address"
                                        name="address"
                                        rows={2}
                                        maxLength={500}
                                        value={form.data.address}
                                        aria-invalid={!!errors.address}
                                        aria-describedby={describedBy("address")}
                                        onChange={(event) => form.setData("address", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
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
                              ? `Enviando dados: ${form.progress.percentage ?? 0}%`
                              : "Salvando dados do responsável..."
                          : undefined
                }
            >
                <Button
                    href={cancelUrl}
                    tone="cancel"
                    label="Cancelar"
                    title="Cancelar e voltar para o prontuário do aluno"
                    aria-label="Cancelar e voltar para o prontuário do aluno"
                    disabled={form.processing}
                />
                <Button
                    type="submit"
                    tone="save"
                    label="Salvar"
                    title="Salvar responsável"
                    aria-label={form.processing ? "Salvando responsável" : "Salvar responsável"}
                    disabled={form.processing || photoValidating}
                />
            </FormActions>
        </FormLayout>
    );
}
