export interface InstitutionalEventRecord {
    id: number;
    title: string;
    description: string | null;
    description_html?: string;
    start_date: string;
    end_date: string;
    start_date_label: string;
    end_date_label: string;
    start_time: string;
    end_time: string;
    location: string;
    organizer: string | null;
    audience: string | null;
    is_active: boolean;
}

export interface InstitutionalEventPaginator {
    data: InstitutionalEventRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface InstitutionalEventFormData {
    title: string;
    description: string;
    start_date: string;
    end_date: string;
    start_time: string;
    end_time: string;
    location: string;
    organizer: string;
    audience: string;
    is_active: boolean;
}
