export interface SemesterRecord {
    id: number;
    year: number;
    term: number;
    label: string | null;
    start_date: string | null;
    end_date: string | null;
    is_current: boolean;
}
