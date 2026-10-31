export interface PedagogicalRecordListRow {
    id: number;
    session_date: string | null;
    start_time: string | null;
    end_time: string | null;
    duration: number | string | null;
    is_present: boolean;
    has_guardians: boolean;
    student_name: string | null;
    course_name: string | null;
    professional_name: string | null;
    can_manage: boolean;
}

export interface PedagogicalRecordPaginator {
    data: PedagogicalRecordListRow[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type PedagogicalRecordFilterName =
    | "student"
    | "professional_id"
    | "course_id"
    | "is_present"
    | "with_guardians";

export type PedagogicalRecordFilters = Record<PedagogicalRecordFilterName, string>;

export interface PedagogicalRecordOption {
    id: number;
    name: string;
}

export interface PedagogicalRecordListAbilities {
    view: boolean;
    view_all: boolean;
    view_own: boolean;
    update: boolean;
    delete: boolean;
}

export interface PedagogicalRecordListProps {
    pedagogicalRecords: PedagogicalRecordPaginator;
    students: PedagogicalRecordOption[];
    professionals: PedagogicalRecordOption[];
    courses: PedagogicalRecordOption[];
    filters: Partial<PedagogicalRecordFilters>;
    can: PedagogicalRecordListAbilities;
}
