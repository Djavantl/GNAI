import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import FormField from "@/components/Forms/FormField/FormField";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import TextInput from "@/components/Forms/TextInput/TextInput";
import DatePicker from "@/components/Forms/DatePicker/DatePicker";
import SummernoteEditor from "@/components/Forms/SummernoteEditor/SummernoteEditor";
import SelectField from "@/components/Forms/SelectField/SelectField";
import FormSection from "@/components/Forms/FormSection/FormSection";
import FormActions from "@/components/Forms/FormActions/FormActions";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import PersonPhotoUpload from "@/components/Forms/PersonPhotoUpload/PersonPhotoUpload";
import { cpfMask, phoneMask, studentsBase, studentUrl } from "./Students.utils";
import type { Student, StudentFormOptions } from "./Students.types";
import { useStudentRequest } from "./useStudentRequest";

type FormData = {
    name: string;
    document: string;
    birth_date: string;
    gender: string;
    email: string;
    phone: string;
    address: string;
    registration: string;
    entry_date: string;
    status: string;
    photo: File | null;
    remove_photo: boolean;
};
type Props = StudentFormOptions &
    ({ mode: "create"; student?: never } | { mode: "edit"; student: Student });

export default function StudentForm(props: Props): React.JSX.Element {
    const { student, genders, studentStatuses } = props;
    const person = student?.person;
    const form = useForm<FormData>({
        name: person?.name ?? "",
        document: person?.document ?? "",
        birth_date: person?.birth_date?.slice(0, 10) ?? "",
        gender: person?.gender ?? props.defaultGender ?? "",
        email: person?.email ?? "",
        phone: person?.phone ?? "",
        address: person?.address ?? "",
        registration: student?.registration ?? "",
        entry_date: student?.entry_date?.slice(0, 10) ?? "",
        status: student?.status ?? props.defaultStatus ?? "",
        photo: null,
        remove_photo: false,
    });
    const busy = useRef(false);
    const [editorFailed, setEditorFailed] = useState(false);
    const [photoValidating, setPhotoValidating] = useState(false);
    const element = useRef<HTMLFormElement>(null);
    const request = useStudentRequest();
    const cancelUrl = student ? studentUrl(student.id) : studentsBase;
    const input = (
        name: "name" | "document" | "email" | "phone" | "registration",
        label: string,
        type = "text",
        required = false,
        maxLength?: number,
    ) => (
        <FormField
            id={`student-${name}`}
            label={label}
            error={form.errors[name]}
            requiredIndicator={required}
        >
            <TextInput
                id={`student-${name}`}
                name={name}
                type={type}
                required={required}
                maxLength={maxLength}
                minLength={name === "name" ? 3 : undefined}
                value={form.data[name]}
                invalid={!!form.errors[name]}
                inputMode={
                    name === "document" || name === "phone"
                        ? "numeric"
                        : undefined
                }
                placeholder={
                    name === "document"
                        ? "000.000.000-00"
                        : name === "phone"
                          ? "(00) 00000-0000"
                          : undefined
                }
                aria-describedby={
                    form.errors[name] ? `student-${name}-error` : undefined
                }
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
    const date = (
        name: "birth_date" | "entry_date",
        label: string,
        required = false,
    ) => (
        <FormField
            id={`student-${name}`}
            label={label}
            error={form.errors[name]}
            requiredIndicator={required}
        >
            <DatePicker
                id={`student-${name}`}
                name={name}
                label={label}
                value={form.data[name]}
                required={required}
                disabled={form.processing}
                invalid={!!form.errors[name]}
                describedBy={
                    form.errors[name] ? `student-${name}-error` : undefined
                }
                onChange={(value) => form.setData(name, value)}
            />
        </FormField>
    );
    const select = (
        name: "gender" | "status",
        label: string,
        options: Record<string, string>,
    ) => (
        <FormField
            id={`student-${name}`}
            label={label}
            error={form.errors[name]}
            requiredIndicator
        >
            <SelectField
                id={`student-${name}`}
                name={name}
                label={label}
                disabled={form.processing}
                required
                value={form.data[name]}
                aria-invalid={!!form.errors[name]}
                aria-describedby={
                    form.errors[name] ? `student-${name}-error` : undefined
                }
                options={Object.entries(options).map(([value, text]) => ({
                    value,
                    label: text,
                }))}
                onChange={(event) => form.setData(name, event.target.value)}
            />
        </FormField>
    );
    return (
        <FormLayout
            ref={element}
            className="studentForm"
            aria-label={
                props.mode === "create" ? "Cadastrar aluno" : "Editar aluno"
            }
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={(event) => {
                event.preventDefault();
                if (
                    busy.current ||
                    form.processing ||
                    editorFailed ||
                    photoValidating
                )
                    return;
                busy.current = true;
                form.transform((data) => {
                    if (props.mode === "edit")
                        return { ...data, _method: "put" };
                    const {
                        status: _status,
                        remove_photo: _removePhoto,
                        ...creation
                    } = data;
                    return creation;
                });
                form.post(
                    student
                        ? studentUrl(student.id, "")
                        : `${studentsBase}/store`,
                    {
                        ...request.options,
                        forceFormData: true,
                        preserveScroll: true,
                        onError: () =>
                            requestAnimationFrame(() =>
                                element.current
                                    ?.querySelector<HTMLElement>(
                                        '[aria-invalid="true"]',
                                    )
                                    ?.focus(),
                            ),
                        onFinish: () => {
                            busy.current = false;
                        },
                    },
                );
            }}
        >
            <CardBox>
                {request.error ? (
                    <div role="alert">
                        <InlineAlert variant="danger">
                            {request.error}
                        </InlineAlert>
                    </div>
                ) : null}
                {Object.keys(form.errors).length ? (
                    <div role="alert">
                        <InlineAlert variant="danger">
                            Confira os campos indicados antes de salvar.
                        </InlineAlert>
                    </div>
                ) : null}
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="student-personal"
                        title="Dados Pessoais"
                        variant="inline"
                        iconClass="fa fa-user"
                    >
                        <div className="studentForm__personal">
                            <PersonPhotoUpload
                                id="student-photo"
                                name="photo"
                                label="Foto do Aluno"
                                current={
                                    person?.photo ? person.photo_url : undefined
                                }
                                file={form.data.photo}
                                removed={form.data.remove_photo}
                                error={
                                    form.errors.photo ??
                                    form.errors.remove_photo
                                }
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
                                onError={(message) =>
                                    form.setError("photo", message)
                                }
                            />
                            <div className="studentForm__personalFields">
                                <div className="studentsPage__grid">
                                    {input(
                                        "name",
                                        "Nome Completo",
                                        "text",
                                        true,
                                        255,
                                    )}
                                    {input(
                                        "email",
                                        "E-mail",
                                        "email",
                                        true,
                                        255,
                                    )}
                                </div>
                                <div className="studentsPage__grid studentsPage__grid--four">
                                    {input(
                                        "document",
                                        "CPF",
                                        "text",
                                        false,
                                        14,
                                    )}
                                    {date(
                                        "birth_date",
                                        "Data de Nascimento",
                                        true,
                                    )}
                                    {select("gender", "Gênero", genders)}
                                    {input(
                                        "phone",
                                        "Telefone",
                                        "tel",
                                        false,
                                        15,
                                    )}
                                </div>
                            </div>
                        </div>
                        <FormField
                            id="student-address-input"
                            label="Endereço"
                            error={form.errors.address}
                        >
                            <SummernoteEditor
                                id="student-address"
                                label="Endereço"
                                defaultValue={person?.address_html ?? ""}
                                height={90}
                                disabled={form.processing}
                                invalid={!!form.errors.address}
                                describedBy={
                                    form.errors.address
                                        ? "student-address-input-error"
                                        : undefined
                                }
                                onLoadError={() => {
                                    setEditorFailed(true);
                                    form.setError(
                                        "address",
                                        "Não foi possível carregar o editor de endereço. Atualize a página e tente novamente.",
                                    );
                                }}
                                onChange={(html) =>
                                    form.setData("address", html)
                                }
                            />
                        </FormField>
                    </FormSection>
                    <FormSection
                        id="student-academic"
                        title="Dados Acadêmicos"
                        variant="inline"
                        iconClass="fa fa-graduation-cap"
                    >
                        <div className="studentsPage__grid studentsPage__grid--three">
                            {input(
                                "registration",
                                "Matrícula",
                                "text",
                                true,
                                50,
                            )}
                            {date("entry_date", "Data de Ingresso")}
                            {props.mode === "edit"
                                ? select(
                                      "status",
                                      "Status do Aluno",
                                      studentStatuses,
                                  )
                                : null}
                        </div>
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
                              : "Salvando dados do aluno..."
                          : ""
                }
            >
                <Button
                    href={cancelUrl}
                    tone="cancel"
                    iconOnly
                    aria-label="Cancelar e voltar"
                    disabled={form.processing}
                />
                <Button
                    type="submit"
                    tone="save"
                    leftIconClass="fa fa-check"
                    iconOnly
                    aria-label={
                        form.processing ? "Salvando aluno" : "Salvar aluno"
                    }
                    disabled={
                        form.processing || editorFailed || photoValidating
                    }
                />
            </FormActions>
        </FormLayout>
    );
}
