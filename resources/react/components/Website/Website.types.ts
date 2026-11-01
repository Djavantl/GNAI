export interface WebsiteSocialLinks {
    instagram: string | null;
    facebook: string | null;
    youtube: string | null;
    website: string | null;
}

/** Identidade white label do site, preparada pelo WebsitePresenter. */
export interface WebsiteSite {
    name: string;
    tagline: string | null;
    logo_url: string | null;
    primary_color: string;
    contact_email: string | null;
    contact_phone: string | null;
    address: string | null;
    social: WebsiteSocialLinks;
    footer_text: string | null;
    login_url: string;
}

export type WebsiteSectionType =
    | "hero"
    | "about"
    | "pillars"
    | "audience"
    | "guidance"
    | "faq"
    | "resources"
    | "features"
    | "team"
    | "events"
    | "publications"
    | "contact";

export interface WebsiteSectionItem {
    icon: string;
    url: string;
    title: string;
    text: string;
}

export type HeroAlignment = "left" | "center" | "right";

export interface HeroAppearance {
    mode: "text" | "banners";
    title_align: HeroAlignment;
    subtitle_align: HeroAlignment;
    badge_align: HeroAlignment;
    button_align: HeroAlignment;
    title_size: number;
    subtitle_size: number;
    badge_radius: number;
    button_radius: number;
    button_shadow: number;
    overlay_opacity: number;
    gradient_end_opacity: number;
    height: number;
    content_width: number;
    interval: number;
    background_color: string;
    title_color: string;
    subtitle_color: string;
    badge_color: string;
    badge_background: string;
    button_color: string;
    button_background: string;
    overlay_color: string;
    gradient_end_color: string;
    badge_text: string;
    badge_visible: boolean;
    gradient_enabled: boolean;
    autoplay: boolean;
    gradient_direction: "to bottom" | "to top" | "to right" | "to left";
    image_fit: "cover" | "contain";
    image_position: "center" | "top" | "bottom" | "left" | "right";
}

export interface HeroSlide {
    id: string;
    image_url: string | null;
    image_path?: string | null;
    alt: string;
    url: string;
}

export interface WebsiteSection {
    id: string;
    type: WebsiteSectionType;
    enabled: boolean;
    title: string;
    subtitle: string;
    body: string;
    button_label: string;
    button_url: string;
    image_path?: string | null;
    image_url: string | null;
    image_alt: string;
    limit: number;
    items: WebsiteSectionItem[];
    hero?: HeroAppearance;
    slides?: HeroSlide[];
}

export interface PublicationSchedule {
    status: "upcoming" | "ongoing" | "finished";
    status_label: string;
    starts_at: string | null;
    day: string;
    month: string;
    date_label: string;
    time_label: string;
    location: string | null;
}

export interface PublicationCardData {
    id: number;
    slug: string;
    url: string;
    title: string;
    summary: string;
    type: string;
    type_label: string;
    tags: string[];
    published_at: string | null;
    published_at_label: string | null;
    cover_url: string | null;
    cover_alt: string;
    is_featured: boolean;
    schedule: PublicationSchedule | null;
}

export interface TeamMemberData {
    id: number;
    name: string;
    role: string | null;
    photo_url: string;
}

export interface PublicationGalleryImage {
    id: number;
    url: string;
    alt: string;
}

export interface PublicationDetailData extends PublicationCardData {
    content_html: string;
    author: string | null;
    organizer: string | null;
    audience: string | null;
    link_url: string | null;
    link_label: string | null;
    seo_title: string;
    seo_description: string | null;
    gallery: PublicationGalleryImage[];
    speakers: TeamMemberData[];
}

export interface WebsiteContent {
    team: TeamMemberData[];
    events: PublicationCardData[];
    publications: PublicationCardData[];
}

export interface WebsiteSeo {
    title: string;
    description?: string | null;
    image?: string | null;
}

export interface WebsitePaginator<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export const websiteRoutes = {
    home: "/site",
    team: "/site/equipe",
    publications: "/site/publicacoes",
} as const;
