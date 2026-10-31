import { useForm } from "@inertiajs/react";
import { useRef, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    PublicationFormData,
    PublicationRecord,
    PublicationFormOptions,
} from "./Publication.types";
import PublicationPage from "./PublicationPage";
import {
    newSpeakerRow,
    PublicationPeopleFields,
    PublicationScheduleFields,
    PublicationTextFields,
} from "./PublicationContentFields";
import PublicationMediaFields from "./PublicationMediaFields";
import PublicationPublishingFields from "./PublicationPublishingFields";
import "./Publications.css";

const base = "/inclusive-radar/publications";

export default function PublicationForm({
    publication,
    backRoute = base,
    options,
    canSubmit,
}: {
    publication?: PublicationRecord;
    backRoute?: string;
    options: PublicationFormOptions;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(publication);
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [editorFailed, setEditorFailed] = useState(false);
    const form = useForm<PublicationFormData>({
        title: publication?.title ?? "",
        slug: publication?.slug ?? "",
        summary: publication?.summary ?? "",
        type: publication?.type ?? options.types[0]?.value ?? "news",
        tag_ids: (publication?.tags ?? []).map((tag) => String(tag.id)),
        has_schedule: publication?.has_schedule ?? false,
        description: publication?.description_html ?? publication?.description ?? "",
        start_date: publication?.start_date ?? "",
        end_date: publication?.end_date ?? "",
        start_time: publication?.start_time ?? "",
        end_time: publication?.end_time ?? "",
        location: publication?.location ?? "",
        organizer: publication?.organizer ?? "",
        audience: publication?.audience ?? "",
        author_id: publication
            ? (publication.author_id ? String(publication.author_id) : "")
            : (options.defaultAuthorId ? String(options.defaultAuthorId) : ""),
        speaker_rows: (publication?.speakers ?? []).map((speaker) => newSpeakerRow({
            id: speaker.professional_id ? String(speaker.professional_id) : "",
            name: speaker.guest_name ?? "",
            guest: speaker.professional_id === null,
            participation: speaker.participation ?? "",
        })),
        link_url: publication?.link_url ?? "",
        link_label: publication?.link_label ?? "",
        cover: null,
        cover_alt: publication?.cover_alt ?? "",
        remove_cover: false,
        gallery: [],
        gallery_existing: (publication?.gallery ?? []).map((image) => ({ ...image, remove: false })),
        is_active: publication?.is_active ?? true,
        published_at: publication?.published_at ?? "",
        is_featured: publication?.is_featured ?? false,
        seo_title: publication?.seo_title ?? "",
        seo_description: publication?.seo_description ?? "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const fieldProps = {
        data: form.data,
        errors,
        disabled: form.processing,
        update: <K extends keyof PublicationFormData>(key: K, value: PublicationFormData[K]) =>
            form.setData((data) => ({ ...data, [key]: value })),
        updateWith: (callback: (data: PublicationFormData) => PublicationFormData) =>
            form.setData(callback),
    };

    function imageError(message: string): void {
        notify({ tone: "warning", title: "Imagem não aceita", text: message });
    }

    function submit(submitEvent: React.FormEvent<HTMLFormElement>): void {
        submitEvent.preventDefault();

        if (!canSubmit || busy.current || form.processing || editorFailed) return;

        busy.current = true;
        form.transform(({ speaker_rows: speakerRows, ...data }) => ({
            ...data,
            ...(editing ? { _method: "put" } : {}),
            speakers: speakerRows.map((row) => ({
                id: row.guest ? "" : row.id,
                name: row.guest ? row.name : "",
                participation: row.participation,
            })),
            gallery: data.gallery.map((image) => ({ file: image.file, alt: image.alt })),
            gallery_existing: data.gallery_existing.map((image) => ({
                id: image.id,
                alt: image.alt,
                remove: image.remove,
            })),
        }));
        form.post(editing && publication ? `${base}/${publication.id}` : `${base}/store`, {
            forceFormData: true,
            preserveScroll: true,
            onError: () => {
                requestAnimationFrame(() =>
                    formElement.current?.querySelector<HTMLElement>('[aria-invalid="true"]')?.focus(),
                );
            },
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível concluir",
                    text: "Não foi possível salvar a publicação. Tente novamente.",
                });
                return false;
            },
            onNetworkError: () => {
                notify({
                    tone: "error",
                    title: "Falha de conexão",
                    text: "Confira sua conexão e tente novamente.",
                });
                return false;
            },
            onFinish: () => {
                busy.current = false;
            },
        });
    }

    return (
        <PublicationPage
            title={editing ? "Editar publicação" : "Nova publicação"}
            subtitle="Escreva o conteúdo, anexe as mídias e defina como a publicação vai ao ar no site."
            current={editing ? "Editar" : "Cadastrar"}
            publicationId={publication?.id}
            publicationTitle={publication?.title}
            backRoute={backRoute}
        >
            <FormLayout
                ref={formElement}
                onSubmit={submit}
                aria-label={editing ? "Editar publicação" : "Cadastrar publicação"}
                aria-busy={form.processing}
                encType="multipart/form-data"
                className="publicationForm"
            >
                <CardBox className="publicationForm__card">
                    <fieldset disabled={form.processing}>
                        <PublicationTextFields
                            {...fieldProps}
                            options={options}
                            onEditorError={() => {
                                setEditorFailed(true);
                                form.setError(
                                    "description",
                                    "Não foi possível carregar o editor de texto. Atualize a página e tente novamente.",
                                );
                            }}
                        />
                        <PublicationMediaFields
                            {...fieldProps}
                            currentCoverUrl={publication?.cover_url ?? null}
                            onImageError={imageError}
                        />
                        <PublicationScheduleFields {...fieldProps} />
                        <PublicationPeopleFields {...fieldProps} options={options} />
                        <PublicationPublishingFields
                            {...fieldProps}
                            publicBaseUrl={options.publicBaseUrl}
                        />
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (form.progress ? `Enviando imagens... ${form.progress.percentage ?? 0}%` : "Salvando...")
                        : undefined}
                >
                    <Button
                        href={backRoute}
                        tone="cancel"
                        label="Cancelar"
                        title="Cancelar e voltar para as publicações"
                        aria-label="Cancelar e voltar para as publicações"
                        disabled={form.processing}
                    />
                    {canSubmit ? (
                        <Button
                            type="submit"
                            tone="save"
                            label={form.processing ? "Salvando..." : "Salvar"}
                            title={editing ? "Salvar alterações da publicação" : "Cadastrar publicação"}
                            aria-label={editing ? "Salvar alterações da publicação" : "Cadastrar publicação"}
                            disabled={form.processing || editorFailed}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </PublicationPage>
    );
}
