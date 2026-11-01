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
    appearance?: PillarCardAppearance;
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

export interface AboutAppearance {
    title_size: number;
    subtitle_size: number;
    body_size: number;
    body_line_height: number;
    image_width: number;
    image_height: number;
    image_radius: number;
    image_shadow: number;
    button_radius: number;
    button_shadow: number;
    padding_vertical: number;
    gap: number;
    title_color: string;
    subtitle_color: string;
    body_color: string;
    button_color: string;
    button_background: string;
    background_color: string;
    divider_color: string;
    gradient_end_color: string;
    title_align: HeroAlignment;
    subtitle_align: HeroAlignment;
    body_align: HeroAlignment | "justify";
    button_align: HeroAlignment;
    image_side: "right" | "left" | "top" | "bottom";
    image_fit: "cover" | "contain";
    image_position: "center" | "top" | "bottom" | "left" | "right";
    gradient_direction: "to bottom" | "to top" | "to right" | "to left";
    image_visible: boolean;
    title_divider: boolean;
    gradient_enabled: boolean;
}

export interface PillarsAppearance {
    title_size: number;
    subtitle_size: number;
    padding_vertical: number;
    gap: number;
    columns: number;
    title_color: string;
    subtitle_color: string;
    background_color: string;
    gradient_end_color: string;
    divider_color: string;
    title_align: "center" | "left" | "right";
    subtitle_align: "center" | "left" | "right";
    layout: "grid" | "list";
    gradient_direction: "to bottom" | "to top" | "to right" | "to left";
    title_divider: boolean;
    gradient_enabled: boolean;
}

export interface PillarCardAppearance {
    icon_size: number;
    icon_width: number;
    icon_height: number;
    icon_radius: number;
    icon_gap: number;
    title_size: number;
    text_size: number;
    text_line_height: number;
    card_radius: number;
    card_padding: number;
    card_border_width: number;
    card_shadow: number;
    card_min_height: number;
    card_width: number;
    hover_lift: number;
    hover_shadow: number;
    icon_color: string;
    icon_background: string;
    title_color: string;
    text_color: string;
    card_background: string;
    card_border_color: string;
    hover_background: string;
    hover_border_color: string;
    gradient_end_color: string;
    icon_align: "left" | "center" | "right";
    title_align: "left" | "center" | "right";
    text_align: "left" | "center" | "right" | "justify";
    icon_position: "top" | "left" | "right";
    gradient_direction: "to bottom" | "to top" | "to right" | "to left";
    hover_enabled: boolean;
    gradient_enabled: boolean;
}

export interface GuidanceAppearance {
    title_size: number;
    subtitle_size: number;
    padding_vertical: number;
    gap: number;
    columns: number;
    interval: number;
    button_size: number;
    button_radius: number;
    button_shadow: number;
    hover_lift: number;
    hover_shadow: number;
    title_color: string;
    subtitle_color: string;
    divider_color: string;
    background_color: string;
    gradient_end_color: string;
    timeline_color: string;
    button_color: string;
    button_background: string;
    hover_color: string;
    hover_background: string;
    title_align: "left" | "center" | "right";
    subtitle_align: "left" | "center" | "right";
    layout: "grid" | "list" | "timeline" | "carousel";
    button_align: "left" | "center" | "right";
    action_style: "link" | "button";
    gradient_direction: "to bottom" | "to top" | "to right" | "to left";
    title_divider: boolean;
    gradient_enabled: boolean;
    autoplay: boolean;
    hover_enabled: boolean;
    button_icon: boolean;
}

export interface PublicationsAppearance extends GuidanceAppearance {
    badge_radius: number;
    date_badge_width: number;
    date_badge_radius: number;
    date_badge_color: string;
    date_badge_background: string;
    card_radius: number;
    card_padding: number;
    card_title_size: number;
    card_text_size: number;
    image_height: number;
    card_background: string;
    card_title_color: string;
    card_text_color: string;
    card_border_color: string;
    show_image: boolean;
    show_summary: boolean;
    show_date: boolean;
    show_tags: boolean;
}

export interface TeamAppearance extends GuidanceAppearance {
    photo_size: number;
    photo_radius: number;
    photo_border_width: number;
    photo_border_color: string;
    card_radius: number;
    card_padding: number;
    card_title_size: number;
    card_text_size: number;
    card_background: string;
    card_title_color: string;
    card_text_color: string;
    card_border_color: string;
    card_align: "left" | "center" | "right";
    show_photo: boolean;
    show_role: boolean;
}

export interface ContactAppearance extends GuidanceAppearance {
    icon_size: number;
    icon_radius: number;
    icon_border_width: number;
    icon_color: string;
    icon_background: string;
    card_radius: number;
    card_padding: number;
    card_title_size: number;
    card_text_size: number;
    card_background: string;
    card_title_color: string;
    card_text_color: string;
    card_border_color: string;
    card_align: "left" | "center" | "right";
    show_icons: boolean;
    show_social: boolean;
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
    about?: AboutAppearance;
    guidance?: GuidanceAppearance;
    publications?: PublicationsAppearance;
    events?: PublicationsAppearance;
    team?: TeamAppearance;
    contact?: ContactAppearance;
    /** Aparência compartilhada das seções com itens, incluindo perguntas e materiais. */
    pillars?: PillarsAppearance;
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
