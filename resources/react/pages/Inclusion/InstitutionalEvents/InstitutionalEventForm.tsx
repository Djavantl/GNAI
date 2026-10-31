import { useForm } from "@inertiajs/react";
import { useRef, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import Tabs from "@/components/Navigation/Tabs/Tabs";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    InstitutionalEventFormData,
    InstitutionalEventRecord,
    PublicationFormOptions,
} from "./InstitutionalEvent.types";
import InstitutionalEventPage from "./InstitutionalEventPage";
import PublicationContentFields from "./PublicationContentFields";
import PublicationMediaFields from "./PublicationMediaFields";
import PublicationPublishingFields from "./PublicationPublishingFields";
import "./InstitutionalEvents.css";

const base = "/inclusive-radar/institutional-events";

type TabId = "content" | "media" | "publishing";

const tabOrder: TabId[] = ["content", "media", "publishing"];
const tabLabels: Record<TabId, string> = {
    content: "Conteúdo",
    media: "Mídia",
    publishing: "Publicação",
};

function tabForError(key: string): TabId {
    if (/^(cover|cover_alt|remove_cover|gallery|gallery_existing)(\.|$)/.test(key)) return "media";
    if (/^(is_active|published_at|is_featured|slug|seo_title|seo_description)$/.test(key)) return "publishing";

    return "content";
}

export default function InstitutionalEventForm({
    event,
    backRoute = base,
    options,
    canSubmit,
}: {
    event?: InstitutionalEventRecord;
    backRoute?: string;
    options: PublicationFormOptions;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(event);
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [editorFailed, setEditorFailed] = useState(false);
    const [tab, setTab] = useState<TabId>("content");
    const form = useForm<InstitutionalEventFormData>({
        title: event?.title ?? "",
        slug: event?.slug ?? "",
        summary: event?.summary ?? "",
        type: event?.type ?? options.types[0]?.value ?? "news",
        category: event?.category ?? "",
        has_schedule: event?.has_schedule ?? false,
        description: event?.description_html ?? event?.description ?? "",
        start_date: event?.start_date ?? "",
        end_date: event?.end_date ?? "",
        start_time: event?.start_time ?? "",
        end_time: event?.end_time ?? "",
        location: event?.location ?? "",
        organizer: event?.organizer ?? "",
        audience: event?.audience ?? "",
        author_id: event
            ? (event.author_id ? String(event.author_id) : "")
            : (options.defaultAuthorId ? String(options.defaultAuthorId) : ""),
        speaker_ids: (event?.speakers ?? []).map((speaker) => String(speaker.id)),
        link_url: event?.link_url ?? "",
        link_label: event?.link_label ?? "",
        cover: null,
        cover_alt: event?.cover_alt ?? "",
        remove_cover: false,
        gallery: [],
        gallery_existing: (event?.gallery ?? []).map((image) => ({ ...image, remove: false })),
        is_active: event?.is_active ?? true,
        published_at: event?.published_at ?? "",
        is_featured: event?.is_featured ?? false,
        seo_title: event?.seo_title ?? "",
        seo_description: event?.seo_description ?? "",
    });
    const errors = form.errors as Record<string, string | undefined>;
    const tabsWithErrors = new Set(Object.keys(errors).map(tabForError));
    const fieldProps = {
        data: form.data,
        errors,
        disabled: form.processing,
        update: <K extends keyof InstitutionalEventFormData>(key: K, value: InstitutionalEventFormData[K]) =>
            form.setData((data) => ({ ...data, [key]: value })),
        updateWith: (callback: (data: InstitutionalEventFormData) => InstitutionalEventFormData) =>
            form.setData(callback),
    };

    function showTab(next: TabId): void {
        setTab(next);
    }

    function imageError(message: string): void {
        notify({ tone: "warning", title: "Imagem não aceita", text: message });
    }

    function submit(submitEvent: React.FormEvent<HTMLFormElement>): void {
        submitEvent.preventDefault();

        if (!canSubmit || busy.current || form.processing || editorFailed) return;

        busy.current = true;
        form.transform((data) => ({
            ...data,
            ...(editing ? { _method: "put" } : {}),
            gallery: data.gallery.map((image) => ({ file: image.file, alt: image.alt })),
            gallery_existing: data.gallery_existing.map((image) => ({
                id: image.id,
                alt: image.alt,
                remove: image.remove,
            })),
        }));
        form.post(editing && event ? `${base}/${event.id}` : `${base}/store`, {
            forceFormData: true,
            preserveScroll: true,
            onError: (nextErrors) => {
                const first = Object.keys(nextErrors)[0];

                if (first) showTab(tabForError(first));

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

    const tabLabel = (id: TabId) => (
        <>
            {tabLabels[id]}
            {tabsWithErrors.has(id) ? <span className="publicationForm__tabAlert"> (revisar)</span> : null}
        </>
    );
    const currentIndex = tabOrder.indexOf(tab);
    const previousTab = tabOrder[currentIndex - 1];
    const nextTab = tabOrder[currentIndex + 1];

    return (
        <InstitutionalEventPage
            title={editing ? "Editar publicação" : "Nova publicação"}
            subtitle="Escreva o conteúdo, anexe as mídias e defina como a publicação vai ao ar no site."
            current={editing ? "Editar" : "Cadastrar"}
            eventId={event?.id}
            eventTitle={event?.title}
            backRoute={backRoute}
        >
            <FormLayout
                ref={formElement}
                onSubmit={submit}
                aria-label={editing ? "Editar publicação" : "Cadastrar publicação"}
                aria-busy={form.processing}
                encType="multipart/form-data"
                className="institutionalEventForm"
            >
                <CardBox className="institutionalEventForm__card">
                    <fieldset disabled={form.processing}>
                        <Tabs
                            initialActiveId={tab}
                            onChange={(id) => setTab(id as TabId)}
                            items={[
                                {
                                    id: "content",
                                    label: tabLabel("content"),
                                    accessibleLabel: tabsWithErrors.has("content") ? "Conteúdo, com campos para revisar" : undefined,
                                    icon: "fa fa-file-text-o",
                                    content: (
                                        <PublicationContentFields
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
                                    ),
                                },
                                {
                                    id: "media",
                                    label: tabLabel("media"),
                                    accessibleLabel: tabsWithErrors.has("media") ? "Mídia, com campos para revisar" : undefined,
                                    icon: "fa fa-picture-o",
                                    content: (
                                        <PublicationMediaFields
                                            {...fieldProps}
                                            currentCoverUrl={event?.cover_url ?? null}
                                            onImageError={imageError}
                                        />
                                    ),
                                },
                                {
                                    id: "publishing",
                                    label: tabLabel("publishing"),
                                    accessibleLabel: tabsWithErrors.has("publishing") ? "Publicação, com campos para revisar" : undefined,
                                    icon: "fa fa-bullhorn",
                                    content: (
                                        <PublicationPublishingFields
                                            {...fieldProps}
                                            publicBaseUrl={options.publicBaseUrl}
                                        />
                                    ),
                                },
                            ]}
                        />
                    </fieldset>
                </CardBox>
                <FormActions
                    status={form.processing
                        ? (form.progress ? `Enviando imagens... ${form.progress.percentage ?? 0}%` : "Salvando...")
                        : undefined}
                    secondary={(
                        <>
                            {previousTab ? (
                                <Button
                                    tone="white"
                                    leftIconClass="fa fa-angle-left"
                                    label={tabLabels[previousTab]}
                                    title={`Ir para a aba ${tabLabels[previousTab]}`}
                                    disabled={form.processing}
                                    onClick={() => showTab(previousTab)}
                                />
                            ) : null}
                            {nextTab ? (
                                <Button
                                    tone="white"
                                    rightIconClass="fa fa-angle-right"
                                    label={tabLabels[nextTab]}
                                    title={`Ir para a aba ${tabLabels[nextTab]}`}
                                    disabled={form.processing}
                                    onClick={() => showTab(nextTab)}
                                />
                            ) : null}
                        </>
                    )}
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
        </InstitutionalEventPage>
    );
}
