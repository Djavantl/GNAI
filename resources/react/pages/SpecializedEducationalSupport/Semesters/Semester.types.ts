export interface SemesterRecord {
    id: number;
    year: number;
    term: number;
    label: string | null;
    start_date: string | null;
    end_date: string | null;
    is_current: boolean;
}

export type SemesterPaginator = {
    data: SemesterRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type SemesterFilters = {
    year: string;
    term: string;
    label: string;
    is_current: string;
};
