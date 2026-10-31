export type ScheduleStatus = "upcoming" | "ongoing" | "finished";

export interface PublicationGalleryItem {
    id: number;
    url: string;
    alt: string;
}

export interface PublicationSpeaker {
    id: number;
    name: string | null;
    role: string | null;
    photo_url: string | null;
}

/** Publicação do site (antiga agenda institucional). */
export interface InstitutionalEventRecord {
    id: number;
    title: string;
    slug: string | null;
    summary: string | null;
    type: string;
    type_label: string;
    category: string | null;
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

export interface InstitutionalEventPaginator {
    data: InstitutionalEventRecord[];
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
    categories: string[];
    defaultAuthorId: number | null;
    publicBaseUrl: string;
}

export interface NewGalleryImage {
    key: string;
    file: File;
    alt: string;
}

export interface ExistingGalleryImage {
    id: number;
    url: string;
    alt: string;
    remove: boolean;
}

export interface InstitutionalEventFormData {
    title: string;
    slug: string;
    summary: string;
    type: string;
    category: string;
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
    speaker_ids: string[];
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
