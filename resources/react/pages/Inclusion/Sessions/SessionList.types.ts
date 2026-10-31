import type { SelectFieldOption } from "@/components/Forms/SelectField/SelectField.types";

export interface SessionListRow {
    id: number;
    session_date: string | null;
    start_time: string | null;
    end_time: string | null;
    type_label: string;
    status_label: string;
    status_color: string;
    is_scheduled: boolean;
    professional_name: string;
    student_names: string[];
    has_record: boolean;
    record_label: string;
    can_delete: boolean;
}

export interface SessionListPaginator {
    data: SessionListRow[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type SessionListFilters = {
    student: string;
    professional: string;
    type: string;
    status: string;
    week: string;
    student_agenda: string;
    professional_agenda: string;
};

export type SessionTableFilterKey = "student" | "professional" | "type" | "status";

export interface SessionListOptions {
    students?: SelectFieldOption[];
    professionals?: SelectFieldOption[];
    types: SelectFieldOption[];
    statuses: SelectFieldOption[];
}

export interface SessionCalendarData {
    rangeStart: string;
    rangeEnd: string;
    total: number;
    sessions: SessionListRow[];
}

export type SessionView = "calendar" | "list";

export interface SessionIndexProps {
    sessions: SessionListPaginator;
    calendar: SessionCalendarData;
    view: SessionView;
    filters: SessionListFilters;
    options: SessionListOptions;
    can: {
        create: boolean;
        view: boolean;
        mySessions: boolean;
    };
}

export interface SessionMySessionsProps {
    sessions: SessionListPaginator;
    calendar: SessionCalendarData;
    view: SessionView;
    filters: SessionListFilters;
    options: SessionListOptions;
    can: {
        create: boolean;
        view: boolean;
        viewAll: boolean;
    };
}

export interface SessionStudentIndexProps {
    sessions: SessionListPaginator;
    student: {
        id: number;
        name: string;
    };
    filters: SessionListFilters;
    options: SessionListOptions;
    can: {
        view: boolean;
    };
}

export interface SessionShowRecord {
    kind: "aee" | "pedagogical";
    show_url: string | null;
    pdf_url: string | null;
    create_url: string | null;
}

export interface SessionShowProps {
    session: {
        id: number;
        student_names: string[];
        professional_name: string;
        status_label: string;
        status_color: string;
        is_scheduled: boolean;
        session_date: string | null;
        start_time: string | null;
        end_time: string | null;
        location: string | null;
        attendance_type_label: string;
        type_label: string;
        session_objective: string | null;
        cancellation_reason: string | null;
    };
    record: SessionShowRecord | null;
    can: {
        edit: boolean;
        cancel: boolean;
        delete: boolean;
    };
    backUrl: string;
}
