export type PeiListVariant = "all" | "student";

export interface PeiListOption {
    value: string;
    label: string;
}

export interface PeiListRecord {
    id: number;
    version: number | null;
    is_finished: boolean;
    is_current: boolean;
    created_at: string | null;
    semester?: {
        id: number;
        label: string | null;
    } | null;
    course?: {
        id: number;
        name: string | null;
    } | null;
    student?: {
        id: number;
        registration?: string | null;
        person?: {
            name?: string | null;
        } | null;
    } | null;
}

export interface PeiPaginator {
    data: PeiListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type PeiListFilters = {
    student_id: string;
    semester_id: string;
    version: string;
    is_finished: string;
};

export interface PeiListPermissions {
    create: boolean;
    view: boolean;
    delete: boolean;
}

export interface PeiListProps {
    peis: PeiPaginator;
    semesters: PeiListOption[];
    students?: PeiListOption[];
    filters: Partial<PeiListFilters>;
    can: PeiListPermissions;
}

export interface PeiStudentSummary {
    id: number;
    name: string;
}
