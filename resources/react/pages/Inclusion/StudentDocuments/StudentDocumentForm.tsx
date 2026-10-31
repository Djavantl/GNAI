import { useRef, useState } from "react";
import { useForm } from "@inertiajs/react";
import FileUploadButton from "@/components/Forms/FileUploadButton/FileUploadButton";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import {
    aeeBase,
    studentRecordUrl,
    studentsBase,
} from "@/components/Students/Students.utils";
import type {
    StudentDocumentFormOptions,
    StudentDocumentRecord,
} from "./StudentDocument.types";

type StudentDocumentFormData = {
    title: string;
    type: string;
    file: File | null;
};

type StudentDocumentFormProps = StudentDocumentFormOptions & {
    studentDocument?: StudentDocumentRecord;
};

const acceptedFiles = ".pdf,.doc,.docx,.jpg,.jpeg,.png";
const maxFileSize = 10 * 1024 * 1024;

export default function StudentDocumentForm({
    student,
    types,
    semester,
    studentDocument,
}: StudentDocumentFormProps): React.JSX.Element {
    const editing = Boolean(studentDocument);
    const form = useForm<StudentDocumentFormData>({
        title: studentDocument?.title ?? "",
        type: studentDocument?.type ?? "",
        file: null,
    });
    const element = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [fileInputKey, setFileInputKey] = useState(0);
    const errors = form.errors as Partial<Record<keyof StudentDocumentFormData, string>>;
    const cancelUrl = studentRecordUrl(student.id, "documents");
    const fileHint = editing
        ? "Deixe em branco para manter o arquivo atual. Formatos: PDF, DOC, DOCX, Imagens (Máx: 10MB)."
        : "Formatos aceitos: PDF, DOC, DOCX, JPG ou PNG. Tamanho máximo: 10MB.";
    const fileDescription = [
        "student-document-file-hint",
        errors.file ? "student-document-file-error" : undefined,
    ].filter(Boolean).join(" ");

    function selectFile(file: File | undefined): void {
        if (!file) return;
        if (file.size > maxFileSize) {
            form.setData("file", null);
            form.setError("file", "O arquivo não pode ultrapassar 10 MB.");
            setFileInputKey((key) => key + 1);
            return;
        }
        form.clearErrors("file");
        form.setData("file", file);
    }

    function clearFile(): void {
        form.setData("file", null);
        form.clearErrors("file");
        setFileInputKey((key) => key + 1);
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        if (busy.current || form.processing) return;
        busy.current = true;

        form.transform((data) => (editing ? { ...data, _method: "put" } : data));
        form.post(
            studentDocument
                ? `${aeeBase}/student-documents/${studentDocument.id}`
                : `${studentsBase}/${student.id}/documents`,
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
                        text: editing
                            ? "Não foi possível atualizar o documento. Atualize a página e tente novamente."
                            : "Não foi possível enviar o documento. Atualize a página e tente novamente.",
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
            className="studentDocumentForm"
            aria-label={editing ? "Editar documento" : "Cadastrar documento"}
            aria-busy={form.processing}
            encType="multipart/form-data"
            onSubmit={submit}
        >
            <CardBox>
                <fieldset disabled={form.processing}>
                    <FormSection
                        id="student-document-information"
                        title={editing ? "Informações Gerais" : "Informações do Documento"}
                        variant="inline"
                        iconClass="fa fa-file-text-o"
                    >
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <FormField
                                    id="student-document-title"
                                    label="Título do Documento"
                                    error={errors.title}
                                    requiredIndicator
                                >
                                    <TextInput
                                        id="student-document-title"
                                        name="title"
                                        required
                                        maxLength={255}
                                        placeholder={editing ? undefined : "Ex: Laudo Médico Psicológico 2024"}
                                        value={form.data.title}
                                        invalid={!!errors.title}
                                        aria-describedby={errors.title ? "student-document-title-error" : undefined}
                                        onChange={(event) => form.setData("title", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                            <FormField
                                id="student-document-type"
                                label="Tipo de Documento"
                                error={errors.type}
                                requiredIndicator
                            >
                                <SelectField
                                    id="student-document-type"
                                    name="type"
                                    label="Tipo de Documento"
                                    required
                                    disabled={form.processing}
                                    value={form.data.type}
                                    options={Object.entries(types).map(([value, label]) => ({
                                        value,
                                        label,
                                    }))}
                                    aria-invalid={!!errors.type}
                                    aria-describedby={errors.type ? "student-document-type-error" : undefined}
                                    onChange={(event) => form.setData("type", event.target.value)}
                                />
                            </FormField>
                            <FormField
                                id="student-document-semester"
                                label="Semestre Atual"
                            >
                                <TextInput
                                    id="student-document-semester"
                                    value={semester?.label ?? "Definido automaticamente"}
                                    readOnly
                                />
                            </FormField>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="student-document-file-section"
                        title="Arquivo"
                        variant="inline"
                        iconClass="fa fa-paperclip"
                    >
                        {studentDocument ? (
                            <div className="studentDocumentForm__current">
                                <i className="fa fa-file-text-o" aria-hidden="true" />
                                <p>
                                    <strong>Arquivo Atual:</strong> {studentDocument.original_name}
                                </p>
                            </div>
                        ) : null}
                        <FormField
                            id="student-document-file"
                            label={editing ? "Substituir Arquivo" : "Selecionar Arquivo"}
                            hint={fileHint}
                            error={errors.file}
                            requiredIndicator={!editing}
                        >
                            <div className="studentDocumentForm__file">
                                <FileUploadButton
                                    key={fileInputKey}
                                    id="student-document-file"
                                    name="file"
                                    label={form.data.file ? "Trocar arquivo" : "Selecionar arquivo"}
                                    accept={acceptedFiles}
                                    required={!editing}
                                    disabled={form.processing}
                                    aria-invalid={!!errors.file}
                                    aria-describedby={fileDescription}
                                    onChange={(event) => selectFile(event.currentTarget.files?.[0])}
                                />
                                {form.data.file ? (
                                    <>
                                        <span className="studentDocumentForm__fileName">
                                            <i className="fa fa-file-o" aria-hidden="true" />
                                            {form.data.file.name}
                                        </span>
                                        <Button
                                            tone="cancel"
                                            size="sm"
                                            label="Remover"
                                            title={`Remover ${form.data.file.name}`}
                                            aria-label={`Remover arquivo selecionado ${form.data.file.name}`}
                                            disabled={form.processing}
                                            onClick={clearFile}
                                        />
                                    </>
                                ) : null}
                            </div>
                        </FormField>
                    </FormSection>
                </fieldset>
            </CardBox>
            <FormActions
                status={
                    form.processing
                        ? form.progress
                            ? `Enviando arquivo: ${form.progress.percentage ?? 0}%`
                            : editing
                              ? "Salvando documento..."
                              : "Enviando documento..."
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
                {editing ? (
                    <Button
                        type="submit"
                        tone="save"
                        label="Salvar"
                        title="Salvar documento"
                        aria-label={form.processing ? "Salvando documento" : "Salvar documento"}
                        disabled={form.processing}
                    />
                ) : (
                    <Button
                        type="submit"
                        tone="save"
                        leftIconClass="fa fa-upload"
                        label="Realizar Upload"
                        title="Realizar upload do documento"
                        aria-label={form.processing ? "Enviando documento" : "Realizar upload do documento"}
                        disabled={form.processing}
                    />
                )}
            </FormActions>
        </FormLayout>
    );
}
