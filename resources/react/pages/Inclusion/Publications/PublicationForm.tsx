import { useForm } from "@inertiajs/react";
import { useRef, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import Tabs from "@/components/Navigation/Tabs/Tabs";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import type {
    PublicationFormData,
    PublicationRecord,
    PublicationFormOptions,
} from "./Publication.types";
import PublicationPage from "./PublicationPage";
import PublicationContentFields from "./PublicationContentFields";
import PublicationMediaFields from "./PublicationMediaFields";
import PublicationPublishingFields from "./PublicationPublishingFields";
import "./Publications.css";

const base = "/inclusive-radar/publications";

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
    const [tab, setTab] = useState<TabId>("content");
    const form = useForm<PublicationFormData>({
        title: publication?.title ?? "",
        slug: publication?.slug ?? "",
        summary: publication?.summary ?? "",
        type: publication?.type ?? options.types[0]?.value ?? "news",
        category: publication?.category ?? "",
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
        speaker_ids: (publication?.speakers ?? []).map((speaker) => String(speaker.id)),
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
    const tabsWithErrors = new Set(Object.keys(errors).map(tabForError));
    const fieldProps = {
        data: form.data,
        errors,
        disabled: form.processing,
        update: <K extends keyof PublicationFormData>(key: K, value: PublicationFormData[K]) =>
            form.setData((data) => ({ ...data, [key]: value })),
        updateWith: (callback: (data: PublicationFormData) => PublicationFormData) =>
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
        form.post(editing && publication ? `${base}/${publication.id}` : `${base}/store`, {
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
                                            currentCoverUrl={publication?.cover_url ?? null}
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
        </PublicationPage>
    );
}
