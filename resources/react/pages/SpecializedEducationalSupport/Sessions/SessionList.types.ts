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

export interface SessionWeeklyAgendaDay {
    date: string;
    label: string;
    sessions: SessionListRow[];
}

export interface SessionWeeklyAgendaData {
    weekStart: string;
    weekEnd: string;
    total: number;
    days: SessionWeeklyAgendaDay[];
}

export interface SessionWeekNavigation {
    previous: string;
    current: string;
    next: string;
}

export interface SessionIndexProps {
    sessions: SessionListPaginator;
    agenda: SessionWeeklyAgendaData;
    weekNavigation: SessionWeekNavigation;
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
    agenda: SessionWeeklyAgendaData;
    weekNavigation: SessionWeekNavigation;
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
