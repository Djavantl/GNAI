export type SessionOptionMap = Record<string, string>;

export interface SessionPersonOption {
    id: number;
    person?: {
        name?: string | null;
    } | null;
}

export interface SessionRecord {
    id: number;
    professional_id: number;
    status: string | null;
    type: string;
    location: string | null;
    session_objective: string | null;
    students: SessionPersonOption[];
    professional?: SessionPersonOption | null;
}

export interface SessionFormValues {
    attendance_type: string;
    session_date: string;
    start_time: string;
    end_time: string;
}

export interface SessionTimeOptions {
    startTimes: SessionOptionMap;
    endTimes: SessionOptionMap;
    attendanceTypes: SessionOptionMap;
    sessionTypes: SessionOptionMap;
}

export interface SessionCreateProps extends SessionTimeOptions {
    students: SessionPersonOption[];
    professionals: SessionPersonOption[];
    defaults: {
        attendance_type: string;
        type: string;
    };
}

export interface SessionEditProps extends SessionTimeOptions {
    session: SessionRecord;
    sessionForm: SessionFormValues;
}

export type SessionFormData = {
    attendance_type: string;
    type: string;
    professional_id: string;
    student_ids: string[];
    session_date: string;
    start_time: string;
    end_time: string;
    location: string;
    session_objective: string;
};

export interface SessionAvailabilitySlot {
    time: string;
    busy: boolean;
    busy_type: string;
}
