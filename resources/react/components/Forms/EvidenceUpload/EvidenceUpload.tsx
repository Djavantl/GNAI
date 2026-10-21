import { useEffect, useState } from "react";
import FileUploadButton from "@/components/Forms/FileUploadButton/FileUploadButton";
import FormField from "@/components/Forms/FormField/FormField";
import Button from "@/components/UI/Button/Button";
import "./EvidenceUpload.css";

const defaultAccept =
    ".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.ppt,.pptx,.odp,.odt,.ods,.xls,.xlsx,.csv,.txt";

function EvidenceFile({
    file,
    onRemove,
}: {
    file: File;
    onRemove: () => void;
}): React.JSX.Element {
    const [previewUrl, setPreviewUrl] = useState("");
    useEffect(() => {
        if (!file.type.startsWith("image/")) return;
        const url = URL.createObjectURL(file);
        setPreviewUrl(url);
        return () => URL.revokeObjectURL(url);
    }, [file]);
    return (
        <li>
            {previewUrl ? (
                <img src={previewUrl} alt="" />
            ) : (
                <i
                    className="fa fa-file-o evidenceUpload__fileIcon"
                    aria-hidden="true"
                />
            )}
            <span>{file.name}</span>
            <Button
                tone="danger"
                size="xs"
                rounded
                iconOnly
                leftIconClass="fa fa-times"
                aria-label={`Remover ${file.name}`}
                title={`Remover ${file.name}`}
                className="evidenceUpload__remove"
                onClick={onRemove}
            />
        </li>
    );
}

export default function EvidenceUpload({
    id,
    name,
    files,
    onChange,
    error,
    disabled = false,
    maxFiles = 10,
    maxFileSizeMb = 20,
    accept = defaultAccept,
}: {
    id: string;
    name: string;
    files: File[];
    onChange: (files: File[]) => void;
    error?: string;
    disabled?: boolean;
    maxFiles?: number;
    maxFileSizeMb?: number;
    accept?: string;
}): React.JSX.Element {
    const [localError, setLocalError] = useState("");
    const message = error ?? localError;
    function add(selected: FileList | null): void {
        if (!selected) return;
        const next = [...files, ...Array.from(selected)];
        if (next.length > maxFiles) {
            setLocalError(
                `Uma inspeção pode possuir no máximo ${maxFiles} evidências.`,
            );
            return;
        }
        const oversized = next.find(
            (file) => file.size > maxFileSizeMb * 1024 * 1024,
        );
        if (oversized) {
            setLocalError(
                `O arquivo ${oversized.name} ultrapassa o limite de ${maxFileSizeMb} MB.`,
            );
            return;
        }
        setLocalError("");
        onChange(next);
    }
    return (
        <FormField
            id={id}
            label="Evidências"
            error={message}
            hint={`Até ${maxFiles} arquivos. Imagens, PDF, documentos, planilhas, textos e apresentações; máximo de ${maxFileSizeMb} MB por arquivo.`}
            hintLabel="Informações sobre evidências"
        >
            <FileUploadButton
                id={id}
                name={name}
                label="Escolher arquivos"
                accept={accept}
                multiple
                disabled={disabled}
                onChange={(event) => {
                    add(event.target.files);
                    event.target.value = "";
                }}
            />
            {files.length ? (
                <ul className="evidenceUpload__files" aria-live="polite">
                    {files.map((file, index) => (
                        <EvidenceFile
                            key={`${file.name}-${file.size}-${file.lastModified}-${index}`}
                            file={file}
                            onRemove={() => {
                                setLocalError("");
                                onChange(
                                    files.filter(
                                        (_, itemIndex) => itemIndex !== index,
                                    ),
                                );
                            }}
                        />
                    ))}
                </ul>
            ) : null}
        </FormField>
    );
}
