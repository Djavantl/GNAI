export type ScheduleStatus = "upcoming" | "ongoing" | "finished";

export interface PublicationGalleryItem {
    id: number;
    url: string;
    alt: string;
}

export interface PublicationSpeaker {
    id: number;
    professional_id: number | null;
    name: string | null;
    guest_name: string | null;
    role: string | null;
    participation: string | null;
    photo_url: string | null;
}

/** Publicação do site (antiga agenda institucional). */
export interface PublicationRecord {
    id: number;
    title: string;
    slug: string | null;
    summary: string | null;
    type: string;
    type_label: string;
    tags: { id: number; name: string }[];
    has_schedule: boolean;
    description: string | null;
    description_html?: string;
    start_date: string | null;
    end_date: string | null;
    start_date_label: string;
    end_date_label: string;
    start_time: string | null;
    end_time: string | null;
    location: string | null;
    organizer: string | null;
    audience: string | null;
    is_active: boolean;
    is_featured: boolean;
    published_at: string | null;
    published_at_label: string;
    author_id: number | null;
    author_name: string | null;
    schedule_status: ScheduleStatus | null;
    public_url: string | null;
    link_url?: string | null;
    link_label?: string | null;
    cover_url?: string | null;
    cover_alt?: string | null;
    seo_title?: string | null;
    seo_description?: string | null;
    gallery?: PublicationGalleryItem[];
    speakers?: PublicationSpeaker[];
}

export interface PublicationPaginator {
    data: PublicationRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface SelectOption {
    value: string;
    label: string;
}

export interface PublicationFormOptions {
    types: SelectOption[];
    authors: SelectOption[];
    speakers: SelectOption[];
    tags: SelectOption[];
    defaultAuthorId: number | null;
    publicBaseUrl: string;
}

export interface NewGalleryImage {
    key: string;
    file: File;
    alt: string;
}

/** Linha dinâmica de participante; `key` mantém a identidade ao remover linhas. */
export interface SpeakerRow {
    key: string;
    /** Profissional cadastrado; vazio quando o participante é avulso. */
    id: string;
    /** Nome digitado do participante avulso. */
    name: string;
    guest: boolean;
    participation: string;
}

export interface ExistingGalleryImage {
    id: number;
    url: string;
    alt: string;
    remove: boolean;
}

export interface PublicationFormData {
    title: string;
    slug: string;
    summary: string;
    type: string;
    tag_ids: string[];
    has_schedule: boolean;
    description: string;
    start_date: string;
    end_date: string;
    start_time: string;
    end_time: string;
    location: string;
    organizer: string;
    audience: string;
    author_id: string;
    speaker_rows: SpeakerRow[];
    link_url: string;
    link_label: string;
    cover: File | null;
    cover_alt: string;
    remove_cover: boolean;
    gallery: NewGalleryImage[];
    gallery_existing: ExistingGalleryImage[];
    is_active: boolean;
    published_at: string;
    is_featured: boolean;
    seo_title: string;
    seo_description: string;
}

export const scheduleStatusLabels: Record<ScheduleStatus, string> = {
    upcoming: "Em breve",
    ongoing: "Acontecendo",
    finished: "Encerrado",
};
