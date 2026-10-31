import type {
    WebsiteSection,
    WebsiteSectionItem,
    WebsiteSectionType,
} from "@/components/Website/Website.types";

export type BuilderSection = WebsiteSection & {
    image: File | null;
    remove_image: boolean;
};

export type BuilderSettings = {
    site_name: string | null;
    tagline: string | null;
    primary_color: string | null;
    logo_url: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    address: string | null;
    instagram_url: string | null;
    facebook_url: string | null;
    youtube_url: string | null;
    website_url: string | null;
    footer_text: string | null;
};

export type BuilderFormData = {
    site_name: string;
    tagline: string;
    primary_color: string;
    logo: File | null;
    remove_logo: boolean;
    contact_email: string;
    contact_phone: string;
    address: string;
    instagram_url: string;
    facebook_url: string;
    youtube_url: string;
    website_url: string;
    footer_text: string;
    sections: BuilderSection[];
};

export const identityFields = [
    "site_name",
    "tagline",
    "primary_color",
    "logo",
    "contact_email",
    "contact_phone",
    "address",
    "instagram_url",
    "facebook_url",
    "youtube_url",
    "website_url",
    "footer_text",
] as const;

/** Item fixo da lista que representa o topo e o rodapé do site (identidade). */
export const IDENTITY_ID = "__identity__";

/**
 * Elemento a editar: a identidade ou uma seção e, opcionalmente, o campo.
 * Nas seções, o campo segue o sufixo dos IDs do editor (ex.: "title",
 * "item-0-value"); na identidade, segue o nome do campo (ex.: "site_name").
 */
export type BuilderEditTarget = {
    sectionId: string;
    field?: string;
};

/** ID do controle no formulário correspondente ao alvo de edição. */
export function builderFieldId(target: BuilderEditTarget): string | undefined {
    if (!target.field) return undefined;

    return target.sectionId === IDENTITY_ID
        ? `website-${target.field}`
        : `website-section-${target.sectionId}-${target.field}`;
}

export const sectionTypeIcons: Record<WebsiteSectionType, string> = {
    hero: "fa fa-picture-o",
    about: "fa fa-info-circle",
    highlights: "fa fa-bar-chart",
    features: "fa fa-th-large",
    team: "fa fa-users",
    events: "fa fa-calendar",
    publications: "fa fa-newspaper-o",
    cta: "fa fa-bullhorn",
    contact: "fa fa-envelope-o",
};

/** Ícones do Font Awesome 4.4 disponíveis no projeto para os itens; o seletor mostra o ícone. */
export const featureIconOptions: { value: string; label: string; iconClass: string }[] = [
    { value: "fa fa-users", label: "Pessoas" },
    { value: "fa fa-wheelchair", label: "Acessibilidade" },
    { value: "fa fa-graduation-cap", label: "Formação" },
    { value: "fa fa-book", label: "Livro" },
    { value: "fa fa-lightbulb-o", label: "Ideia" },
    { value: "fa fa-heart-o", label: "Acolhimento" },
    { value: "fa fa-comments-o", label: "Conversa" },
    { value: "fa fa-calendar", label: "Calendário" },
    { value: "fa fa-university", label: "Instituição" },
    { value: "fa fa-hand-paper-o", label: "Mão" },
    { value: "fa fa-child", label: "Criança" },
    { value: "fa fa-microphone", label: "Palestra" },
    { value: "fa fa-eye", label: "Visão" },
    { value: "fa fa-star-o", label: "Destaque" },
    { value: "fa fa-sitemap", label: "Organização" },
    { value: "fa fa-globe", label: "Mundo" },
].map((option) => ({ ...option, iconClass: option.value }));

export function emptyItem(type: WebsiteSectionType): WebsiteSectionItem {
    return type === "highlights"
        ? { icon: "", value: "+10", title: "Novo destaque", text: "" }
        : { icon: "fa fa-star-o", value: "", title: "Novo item", text: "Descreva este item." };
}

const defaults: Record<WebsiteSectionType, Partial<WebsiteSection>> = {
    hero: { title: "Título do banner", subtitle: "Uma frase curta apresentando o núcleo." },
    about: { title: "Quem somos", body: "<p>Conte a história e a missão do núcleo.</p>" },
    highlights: { title: "Em números", items: [emptyItem("highlights")] },
    features: { title: "O que fazemos", items: [emptyItem("features")] },
    team: { title: "Nossa equipe", limit: 4 },
    events: { title: "Próximos eventos", limit: 3 },
    publications: { title: "Publicações", limit: 3 },
    cta: { title: "Participe", subtitle: "Convide o público para uma ação.", button_label: "Saiba mais", button_url: "/site/publicacoes" },
    contact: { title: "Fale conosco" },
};

export function newSection(type: WebsiteSectionType): BuilderSection {
    return {
        id: `${type}-${Date.now().toString(36)}`,
        type,
        enabled: true,
        title: "",
        subtitle: "",
        body: "",
        button_label: "",
        button_url: "",
        image_url: null,
        image_alt: "",
        limit: 3,
        items: [],
        image: null,
        remove_image: false,
        ...defaults[type],
    };
}

export function toBuilderSection(section: WebsiteSection): BuilderSection {
    return {
        ...section,
        title: section.title ?? "",
        subtitle: section.subtitle ?? "",
        body: section.body ?? "",
        button_label: section.button_label ?? "",
        button_url: section.button_url ?? "",
        image_alt: section.image_alt ?? "",
        items: Array.isArray(section.items) ? section.items : [],
        limit: Number(section.limit) || 3,
        image: null,
        remove_image: false,
    };
}
