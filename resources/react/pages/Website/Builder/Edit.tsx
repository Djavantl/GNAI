import { useForm } from "@inertiajs/react";
import { useEffect, useMemo, useRef, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import { useObjectUrl } from "@/components/Forms/ImageUploadField/ImageUploadField";
import BusinessPage from "@/components/Navigation/BusinessPage/BusinessPage";
import Tabs from "@/components/Navigation/Tabs/Tabs";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import { notify } from "@/components/UI/NotificationToast/notify";
import { isWebsiteSectionVisible } from "@/components/Website/WebsiteSections";
import { websiteRoutes } from "@/components/Website/Website.types";
import type {
    PillarCardAppearance,
    WebsiteContent,
    WebsiteSection,
    WebsiteSectionType,
    WebsiteSite,
} from "@/components/Website/Website.types";
import BuilderIdentityFields from "./BuilderIdentityFields";
import BuilderPreview from "./BuilderPreview";
import BuilderSectionEditor from "./BuilderSectionEditor";
import BuilderSectionList from "./BuilderSectionList";
import {
    builderFieldId,
    IDENTITY_ID,
    identityFields,
    newSection,
    sectionTypeIcons,
    toBuilderSection,
    type BuilderEditTarget,
    type BuilderFormData,
    type BuilderSection,
    type BuilderSettings,
} from "./websiteBuilder";
import "./WebsiteBuilder.css";

type Props = {
    settings: BuilderSettings;
    sections: WebsiteSection[];
    site: WebsiteSite;
    content: WebsiteContent;
    sectionTypes: { value: WebsiteSectionType; label: string }[];
    sectionTemplates: Record<WebsiteSectionType, WebsiteSection>;
    pillarCardTemplate: PillarCardAppearance;
    guidanceStepTemplate: PillarCardAppearance;
    publicUrl: string;
    can: { submit: boolean };
};

type BuilderTab = "sections" | "edit";

/** Foco pendente no editor: um controle específico, o primeiro inválido ou o primeiro campo. */
type FocusRequest = { elementId?: string; invalid?: boolean };

const focusableSelector = [
    'input:not([type="hidden"]):not([disabled])',
    "textarea:not([disabled])",
    "select:not([disabled])",
    '[contenteditable="true"]',
].join(", ");

const endpoint = "/website/builder";
const fileIds = new WeakMap<File, number>();
let nextFileId = 0;

function fileKey(file: File): number {
    if (!fileIds.has(file)) {
        nextFileId += 1;
        fileIds.set(file, nextFileId);
    }

    return fileIds.get(file) ?? 0;
}

/** URLs temporárias das imagens novas das seções, liberadas quando os arquivos mudam. */
function useSectionImageUrls(sections: BuilderSection[]): Record<string, string> {
    const [urls, setUrls] = useState<Record<string, string>>({});
    const signature = sections
        .flatMap((section) => [
            section.image ? `${section.id}:${fileKey(section.image)}` : "",
            ...section.slides.map((slide) => slide.image ? `${section.id}:${slide.id}:${fileKey(slide.image)}` : ""),
        ])
        .join("|");
    const files = useRef(sections);
    files.current = sections;

    useEffect(() => {
        const next: Record<string, string> = {};

        files.current.forEach((section) => {
            if (section.image) next[section.id] = URL.createObjectURL(section.image);
            section.slides.forEach((slide) => {
                if (slide.image) next[`${section.id}:${slide.id}`] = URL.createObjectURL(slide.image);
            });
        });
        setUrls(next);

        return () => Object.values(next).forEach((url) => URL.revokeObjectURL(url));
    }, [signature]);

    return urls;
}

export default function WebsiteBuilderEditPage({
    settings,
    sections: initialSections,
    site,
    content,
    sectionTypes,
    sectionTemplates,
    pillarCardTemplate,
    guidanceStepTemplate,
    publicUrl,
    can,
}: Props): React.JSX.Element {
    const formElement = useRef<HTMLFormElement>(null);
    const busy = useRef(false);
    const [editorFailed, setEditorFailed] = useState(false);
    const editorBody = useRef<HTMLDivElement>(null);
    const pendingFocus = useRef<FocusRequest | null>(null);
    const [focusRequest, setFocusRequest] = useState(0);
    const [editTarget, setEditTarget] = useState<BuilderEditTarget | null>(null);
    const [selectedId, setSelectedId] = useState<string>(IDENTITY_ID);
    const [tab, setTab] = useState<BuilderTab>("sections");
    const form = useForm<BuilderFormData>({
        site_name: settings.site_name ?? "",
        tagline: settings.tagline ?? "",
        primary_color: settings.primary_color ?? "#322b75",
        logo: null,
        remove_logo: false,
        contact_email: settings.contact_email ?? "",
        contact_phone: settings.contact_phone ?? "",
        address: settings.address ?? "",
        instagram_url: settings.instagram_url ?? "",
        facebook_url: settings.facebook_url ?? "",
        youtube_url: settings.youtube_url ?? "",
        website_url: settings.website_url ?? "",
        footer_text: settings.footer_text ?? "",
        sections: initialSections.map(toBuilderSection),
    });
    const errors = form.errors as Record<string, string | undefined>;
    const sections = form.data.sections;
    const selectedIndex = sections.findIndex((section) => section.id === selectedId);
    const selected = selectedIndex >= 0 ? sections[selectedIndex] : null;
    const identitySelected = selectedId === IDENTITY_ID;
    const logoUrl = useObjectUrl(form.data.logo);
    const sectionImageUrls = useSectionImageUrls(sections);
    const typeLabels = Object.fromEntries(sectionTypes.map((type) => [type.value, type.label]));

    const invalidIds = useMemo(() => {
        const ids = new Set<string>();

        Object.keys(errors).forEach((key) => {
            const match = /^sections\.(\d+)\./.exec(key);
            const section = match ? sections[Number(match[1])] : undefined;

            if (section) ids.add(section.id);
        });

        return ids;
    }, [errors, sections]);

    const identityInvalid = identityFields.some((key) => !!errors[key]);

    const previewSite = useMemo<WebsiteSite>(() => ({
        ...site,
        name: form.data.site_name.trim() || "Nome do site",
        tagline: form.data.tagline.trim() || null,
        logo_url: logoUrl ?? (form.data.remove_logo ? null : settings.logo_url),
        primary_color: /^#[0-9a-f]{6}$/i.test(form.data.primary_color) ? form.data.primary_color : site.primary_color,
        contact_email: form.data.contact_email.trim() || null,
        contact_phone: form.data.contact_phone.trim() || null,
        address: form.data.address.trim() || null,
        social: {
            instagram: form.data.instagram_url.trim() || null,
            facebook: form.data.facebook_url.trim() || null,
            youtube: form.data.youtube_url.trim() || null,
            website: form.data.website_url.trim() || null,
        },
        footer_text: form.data.footer_text.trim() || null,
    }), [form.data, logoUrl, settings.logo_url, site]);

    const previewSections = useMemo<WebsiteSection[]>(() => sections.map((section) => ({
        ...section,
        image_url: sectionImageUrls[section.id] ?? (section.remove_image ? null : section.image_url),
        slides: section.slides.map((slide) => ({
            ...slide,
            image_url: sectionImageUrls[`${section.id}:${slide.id}`] ?? (slide.remove_image ? null : slide.image_url),
        })),
    })), [sections, sectionImageUrls]);

    function updateSection(index: number, patch: Partial<BuilderSection>): void {
        form.setData((data) => ({
            ...data,
            sections: data.sections.map((section, current) => (current === index ? { ...section, ...patch } : section)),
        }));
    }

    function moveSection(index: number, offset: -1 | 1): void {
        form.setData((data) => {
            const next = [...data.sections];
            const target = index + offset;

            if (target < 0 || target >= next.length) return data;

            [next[index], next[target]] = [next[target], next[index]];

            return { ...data, sections: next };
        });
    }

    function removeSection(index: number): void {
        const removed = sections[index];
        const remaining = sections.filter((_, current) => current !== index);

        form.setData((data) => ({ ...data, sections: remaining }));

        if (removed?.id === selectedId) {
            setSelectedId(remaining[Math.min(index, remaining.length - 1)]?.id ?? IDENTITY_ID);
        }
    }

    function addSection(type: WebsiteSectionType): void {
        const section = newSection(type, sectionTemplates[type]);

        form.setData((data) => ({ ...data, sections: [...data.sections, section] }));
        openEditor({ sectionId: section.id });
    }

    function requestFocus(request: FocusRequest): void {
        pendingFocus.current = request;
        setFocusRequest((current) => current + 1);
    }

    /** Abre a aba de edição com o item escolhido na lista ou na prévia. */
    function openEditor(target: BuilderEditTarget): void {
        setEditTarget(target);
        setSelectedId(target.sectionId);
        setTab("edit");
        requestFocus({ elementId: builderFieldId(target) });
    }

    // O formulário só existe depois que a aba de edição renderiza (e o editor
    // de texto inicializa); por isso o foco aguarda alguns quadros.
    useEffect(() => {
        const request = pendingFocus.current;

        if (!request) return;

        let frame = 0;
        let attempts = 0;

        const run = (): void => {
            const body = editorBody.current;
            const anchor = body
                ? request.invalid
                    ? body.querySelector<HTMLElement>('[aria-invalid="true"]')
                    : request.elementId
                        ? body.querySelector<HTMLElement>(`#${CSS.escape(request.elementId)}`)
                        : body.querySelector<HTMLElement>(focusableSelector)
                : null;
            const control = anchor?.matches(focusableSelector)
                ? anchor
                : anchor?.querySelector<HTMLElement>(focusableSelector) ?? null;

            attempts += 1;

            if (!control && attempts < 60) {
                frame = requestAnimationFrame(run);
                return;
            }

            pendingFocus.current = null;

            const fallback = body?.querySelector<HTMLElement>(focusableSelector) ?? null;
            const target = control ?? fallback;
            const visible = anchor ?? target ?? body;
            let parent = visible?.parentElement;
            while (parent && parent !== body) {
                if (parent instanceof HTMLDetailsElement) parent.open = true;
                parent = parent.parentElement;
            }

            visible?.scrollIntoView({ block: "center", behavior: "smooth" });
            target?.focus({ preventScroll: true });
        };

        frame = requestAnimationFrame(run);

        return () => cancelAnimationFrame(frame);
    }, [focusRequest]);

    function imageError(message: string): void {
        notify({ tone: "warning", title: "Imagem não aceita", text: message });
    }

    function handleValidationErrors(nextErrors: Record<string, string>): void {
        const keys = Object.keys(nextErrors);
        const identityError = keys.some((key) => (identityFields as readonly string[]).includes(key));
        const sectionMatch = keys.map((key) => /^sections\.(\d+)\./.exec(key)).find(Boolean);

        const section = !identityError && sectionMatch ? form.data.sections[Number(sectionMatch[1])] : undefined;

        if (!identityError && !section) {
            setTab("sections");
            return;
        }

        setSelectedId(section ? section.id : IDENTITY_ID);
        setTab("edit");
        requestFocus({ invalid: true });
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();

        if (!can.submit || busy.current || form.processing || editorFailed) return;

        busy.current = true;
        form.transform((data) => ({
            ...data,
            _method: "put",
            sections: data.sections.map(({ image_url: _url, image_path: _path, ...section }) => ({
                ...section,
                slides: section.slides.map(({ image_url: _slideUrl, image_path: _slidePath, ...slide }) => slide),
                items: section.items.map(({ editor_key: _editorKey, ...item }) => item),
            })),
        }));
        form.post(endpoint, {
            forceFormData: true,
            preserveScroll: true,
            // Após salvar, a página remonta com as imagens já gravadas; com erros, mantém a edição.
            preserveState: "errors",
            onError: handleValidationErrors,
            onHttpException: () => {
                notify({
                    tone: "error",
                    title: "Não foi possível salvar",
                    text: "Não foi possível salvar o site. Tente novamente.",
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

    const sectionsTab = (
        <FormSection
            id="website-structure"
            title="Estrutura da página"
            variant="inline"
            iconClass="fa fa-sitemap"
        >
            <BuilderSectionList
                sections={sections}
                siteName={form.data.site_name.trim()}
                identityInvalid={identityInvalid}
                selectedId={selectedId}
                sectionTypes={sectionTypes}
                invalidIds={invalidIds}
                disabled={form.processing}
                onSelect={(id) => openEditor({ sectionId: id })}
                onMove={moveSection}
                onToggle={(index) => updateSection(index, { enabled: !sections[index].enabled })}
                onRemove={removeSection}
                onAdd={addSection}
            />
        </FormSection>
    );

    const editingLabel = identitySelected
        ? "Identidade"
        : selected
            ? typeLabels[selected.type] ?? selected.type
            : null;

    const editTab = (
        <div ref={editorBody}>
            <div className="websiteBuilder__editBar">
                <Button
                    size="sm"
                    tone="white"
                    leftIconClass="fa fa-arrow-left"
                    label="Voltar às seções"
                    title="Voltar para a lista de seções"
                    onClick={() => setTab("sections")}
                />
            </div>
            {identitySelected ? (
                <BuilderIdentityFields
                    data={form.data}
                    currentLogoUrl={settings.logo_url}
                    errors={errors}
                    disabled={form.processing}
                    onChange={(key, value) => form.setData((data) => ({ ...data, [key]: value }))}
                    onLogoChange={(file, remove) => form.setData((data) => ({ ...data, logo: file, remove_logo: remove }))}
                    onImageError={imageError}
                />
            ) : selected ? (
                <FormSection
                    id="website-section-editor"
                    title={`Editar: ${editingLabel}`}
                    variant="inline"
                    iconClass={sectionTypeIcons[selected.type] ?? "fa fa-pencil"}
                >
                    <BuilderSectionEditor
                        pillarCardTemplate={pillarCardTemplate}
                        guidanceStepTemplate={guidanceStepTemplate}
                        linkOptions={[
                            ...form.data.sections
                                .filter((section) => isWebsiteSectionVisible(section, content))
                                .map((section) => ({
                                    value: `#${section.id}`,
                                    label: section.title || sectionTypes.find((type) => type.value === section.type)?.label || section.id,
                                })),
                            { value: websiteRoutes.team, label: "Página da equipe" },
                            { value: websiteRoutes.publications, label: "Página de publicações" },
                        ]}
                        editTarget={editTarget}
                        onEditTarget={openEditor}
                        key={selected.id}
                        section={selected}
                        index={selectedIndex}
                        errors={errors}
                        disabled={form.processing}
                        onChange={(patch) => updateSection(selectedIndex, patch)}
                        onImageError={imageError}
                        onEditorError={() => {
                            setEditorFailed(true);
                            form.setError(
                                `sections.${selectedIndex}.body` as keyof BuilderFormData,
                                "Não foi possível carregar o editor de texto. Atualize a página e tente novamente.",
                            );
                        }}
                    />
                </FormSection>
            ) : (
                <p className="websiteBuilder__note">
                    Selecione um item na aba Seções ou clique duas vezes em um elemento da prévia.
                </p>
            )}
        </div>
    );

    return (
        <BusinessPage
            title="Site: página inicial"
            subtitle="Monte a página inicial do site e acompanhe o resultado em tempo real antes de salvar."
            breadcrumbItems={[
                { label: "Home", href: "/auth/dashboard" },
                { label: "Site" },
                { label: "Página inicial" },
            ]}
            className="websiteBuilderPage"
        >
            <FormLayout
                ref={formElement}
                aria-label="Construtor da página inicial do site"
                aria-busy={form.processing}
                encType="multipart/form-data"
                onSubmit={submit}
            >
                <CardBox className="websiteBuilder__content">
                    <div className="websiteBuilder">
                        <div className="websiteBuilder__editor">
                            <fieldset disabled={form.processing}>
                                <Tabs
                                    initialActiveId={tab}
                                    onChange={(id) => setTab(id as BuilderTab)}
                                    items={[
                                        {
                                            id: "sections",
                                            label: "Seções",
                                            icon: "fa fa-th-list",
                                            content: sectionsTab,
                                        },
                                        {
                                            id: "edit",
                                            label: "Edição",
                                            icon: "fa fa-pencil",
                                            content: editTab,
                                        },
                                    ]}
                                />
                            </fieldset>
                        </div>
                        <BuilderPreview
                            site={previewSite}
                            sections={previewSections}
                            content={content}
                            selectedId={selectedId}
                            onEdit={openEditor}
                        />
                    </div>
                </CardBox>
                <FormActions
                    secondary={(
                        <Button
                            href={publicUrl}
                            target="_blank"
                            rel="noopener noreferrer"
                            tone="cancel"
                            leftIconClass="fa fa-external-link"
                            label="Ver site"
                            title="Abrir o site publicado em nova aba"
                            aria-label="Abrir o site publicado em nova aba"
                        />
                    )}
                    status={form.processing
                        ? (form.progress ? `Enviando imagens... ${form.progress.percentage ?? 0}%` : "Salvando...")
                        : undefined}
                >
                    <Button
                        href={endpoint}
                        tone="cancel"
                        label="Cancelar"
                        title="Descartar alterações não salvas"
                        aria-label="Descartar alterações não salvas"
                        disabled={form.processing}
                    />
                    {can.submit ? (
                        <Button
                            type="submit"
                            tone="save"
                            label={form.processing ? "Salvando..." : "Salvar"}
                            title="Salvar e publicar a página inicial"
                            aria-label="Salvar e publicar a página inicial"
                            disabled={form.processing || editorFailed}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </BusinessPage>
    );
}
