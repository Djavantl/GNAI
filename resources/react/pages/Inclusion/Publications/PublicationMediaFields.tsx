import FileUploadButton from "@/components/Forms/FileUploadButton/FileUploadButton";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide } from "@/components/Forms/FormGrid/FormGrid";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ImageUploadField, {
    defaultImageAccept,
    useObjectUrl,
} from "@/components/Forms/ImageUploadField/ImageUploadField";
import RemovableField from "@/components/Forms/RemovableField/RemovableField";
import TextInput from "@/components/Forms/TextInput/TextInput";
import type { NewGalleryImage } from "./Publication.types";
import { describedBy, type PublicationFieldsProps } from "./PublicationFields.types";

const maxImageBytes = 4 * 1024 * 1024;
const maxGallery = 30;

function NewGalleryThumbnail({ image }: { image: NewGalleryImage }): React.JSX.Element {
    const url = useObjectUrl(image.file);

    return url ? <img src={url} alt="" /> : <span className="publicationGallery__placeholder" aria-hidden="true" />;
}

/** Capa e galeria de fotos, sempre com texto alternativo. */
export default function PublicationMediaFields({
    data,
    errors,
    disabled,
    update,
    updateWith,
    currentCoverUrl,
    onImageError,
}: PublicationFieldsProps & {
    currentCoverUrl: string | null;
    onImageError: (message: string) => void;
}): React.JSX.Element {
    const hasCover = !!data.cover || (!!currentCoverUrl && !data.remove_cover);
    const keptExisting = data.gallery_existing.filter((image) => !image.remove).length;
    const total = keptExisting + data.gallery.length;

    function addFiles(files: FileList | null): void {
        if (!files?.length) return;

        const accepted: NewGalleryImage[] = [];
        const allowed = defaultImageAccept.split(",");

        Array.from(files).forEach((file) => {
            const extension = `.${file.name.split(".").pop()?.toLowerCase() ?? ""}`;

            if (!allowed.includes(extension)) {
                onImageError(`${file.name} não foi adicionada: use JPG, PNG ou WEBP.`);
                return;
            }

            if (file.size > maxImageBytes) {
                onImageError(`${file.name} não foi adicionada: o limite é 4 MB por foto.`);
                return;
            }

            accepted.push({ key: `${Date.now()}-${file.name}-${Math.random().toString(36).slice(2)}`, file, alt: "" });
        });

        updateWith((current) => ({
            ...current,
            gallery: [...current.gallery, ...accepted].slice(0, Math.max(0, maxGallery - keptExisting)),
        }));
    }

    return (
        <>
            <FormSection id="publication-cover" title="Imagem de capa" variant="inline" iconClass="fa fa-picture-o">
                <FormGrid>
                    <FormField
                        id="publication-cover-file"
                        label="Capa"
                        error={errors.cover}
                        hint="Aparece nos cartões e no topo da publicação. JPG, PNG ou WEBP com até 4 MB."
                        hintLabel="Sobre a capa"
                    >
                        <ImageUploadField
                            id="publication-cover-file"
                            name="cover"
                            label="Capa"
                            file={data.cover}
                            currentUrl={currentCoverUrl}
                            removed={data.remove_cover}
                            previewAlt={data.cover_alt}
                            disabled={disabled}
                            invalid={!!errors.cover}
                            describedBy={describedBy(errors, "cover", "publication-cover-file")}
                            onChange={(file) => updateWith((current) => ({ ...current, cover: file, remove_cover: false }))}
                            onRemove={() => updateWith((current) => ({ ...current, cover: null, remove_cover: true }))}
                            onError={onImageError}
                        />
                    </FormField>
                    <FormField
                        id="publication-cover-alt"
                        label="Texto alternativo"
                        error={errors.cover_alt}
                        hint="Descreva a imagem para quem usa leitor de tela."
                        hintLabel="Sobre o texto alternativo"
                        requiredIndicator={hasCover}
                    >
                        <TextInput
                            id="publication-cover-alt"
                            name="cover_alt"
                            value={data.cover_alt}
                            maxLength={255}
                            placeholder="Ex.: Participantes reunidos no auditório"
                            required={hasCover}
                            invalid={!!errors.cover_alt}
                            aria-describedby={describedBy(errors, "cover_alt", "publication-cover-alt")}
                            onChange={(event) => update("cover_alt", event.target.value)}
                        />
                    </FormField>
                </FormGrid>
            </FormSection>
            <FormSection
                id="publication-gallery"
                title="Galeria de fotos"
                variant="inline"
                iconClass="fa fa-th"
            >
                <FormGrid>
                    <FormGridWide>
                        {errors.gallery ? (
                            <p className="publicationGallery__error" id="publication-gallery-error">{errors.gallery}</p>
                        ) : null}
                        {data.gallery_existing.length || data.gallery.length ? (
                            <ul className="publicationGallery">
                                {data.gallery_existing.map((image, index) => {
                                    const id = `publication-gallery-existing-${image.id}`;
                                    const errorKey = `gallery_existing.${index}.alt`;

                                    return (
                                        <li
                                            key={image.id}
                                            className={image.remove ? "publicationGallery__item publicationGallery__item--removed" : "publicationGallery__item"}
                                        >
                                            <img src={image.url} alt="" />
                                            <div className="publicationGallery__fields">
                                                <FormField id={id} label={`Texto alternativo da foto ${index + 1}`} error={errors[errorKey]} requiredIndicator={!image.remove}>
                                                    <RemovableField
                                                        removed={image.remove}
                                                        removeLabel={`Remover a foto ${index + 1}`}
                                                        restoreLabel={`Manter a foto ${index + 1}`}
                                                        disabled={disabled}
                                                        onRemove={() => updateWith((current) => ({
                                                            ...current,
                                                            gallery_existing: current.gallery_existing.map((item) => (
                                                                item.id === image.id ? { ...item, remove: !item.remove } : item
                                                            )),
                                                        }))}
                                                    >
                                                        <TextInput
                                                            id={id}
                                                            name={`gallery_existing[${index}][alt]`}
                                                            value={image.alt}
                                                            maxLength={255}
                                                            disabled={image.remove}
                                                            invalid={!!errors[errorKey]}
                                                            aria-describedby={errors[errorKey] ? `${id}-error` : undefined}
                                                            onChange={(event) => updateWith((current) => ({
                                                                ...current,
                                                                gallery_existing: current.gallery_existing.map((item) => (
                                                                    item.id === image.id ? { ...item, alt: event.target.value } : item
                                                                )),
                                                            }))}
                                                        />
                                                    </RemovableField>
                                                </FormField>
                                                {image.remove ? (
                                                    <span className="publicationGallery__status">Será removida ao salvar.</span>
                                                ) : null}
                                            </div>
                                        </li>
                                    );
                                })}
                                {data.gallery.map((image, index) => {
                                    const id = `publication-gallery-new-${index}`;
                                    const position = data.gallery_existing.length + index + 1;
                                    const fileError = errors[`gallery.${index}.file`];
                                    const altError = errors[`gallery.${index}.alt`];

                                    return (
                                        <li key={image.key} className="publicationGallery__item">
                                            <NewGalleryThumbnail image={image} />
                                            <div className="publicationGallery__fields">
                                                <FormField id={id} label={`Texto alternativo da foto ${position}`} error={altError ?? fileError} requiredIndicator>
                                                    <RemovableField
                                                        removeLabel={`Remover a foto ${position}`}
                                                        disabled={disabled}
                                                        onRemove={() => updateWith((current) => ({
                                                            ...current,
                                                            gallery: current.gallery.filter((item) => item.key !== image.key),
                                                        }))}
                                                    >
                                                        <TextInput
                                                            id={id}
                                                            name={`gallery[${index}][alt]`}
                                                            value={image.alt}
                                                            maxLength={255}
                                                            placeholder="Descreva a foto"
                                                            required
                                                            invalid={!!(altError ?? fileError)}
                                                            aria-describedby={altError ?? fileError ? `${id}-error` : undefined}
                                                            onChange={(event) => updateWith((current) => ({
                                                                ...current,
                                                                gallery: current.gallery.map((item) => (
                                                                    item.key === image.key ? { ...item, alt: event.target.value } : item
                                                                )),
                                                            }))}
                                                        />
                                                    </RemovableField>
                                                </FormField>
                                                <span className="publicationGallery__status">Nova foto: {image.file.name}</span>
                                            </div>
                                        </li>
                                    );
                                })}
                            </ul>
                        ) : null}
                        <FileUploadButton
                            id="publication-gallery-files"
                            name="gallery_files"
                            label="Adicionar fotos"
                            accept={defaultImageAccept}
                            multiple
                            disabled={disabled || total >= maxGallery}
                            aria-describedby={errors.gallery ? "publication-gallery-error" : undefined}
                            onChange={(event) => {
                                addFiles(event.target.files);
                                event.target.value = "";
                            }}
                        />
                    </FormGridWide>
                </FormGrid>
            </FormSection>
        </>
    );
}
