import type {
    AeePerson,
    AeeRecordRecord,
    AeeSession,
    AeeStudent,
    AeeStudentEvaluationRecord,
} from "./AeeRecord.types";

export interface AeePaginator<T> {
    data: T[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export interface AeeListFilters {
    student: string;
    professional_id: string;
    course_id: string;
    is_present: string;
}

export type AeeListFilterName = keyof AeeListFilters;

export interface AeeFilterOptions {
    students: AeeStudent[];
    professionals: {
        id: number;
        person?: AeePerson | null;
    }[];
    courses: {
        id: number;
        name: string;
    }[];
}

export interface AeeListStudent extends AeeStudent {
    current_course?: {
        course?: {
            id: number;
            name: string;
        } | null;
    } | null;
}

export interface AeeListEvaluation
    extends Pick<AeeStudentEvaluationRecord, "id" | "is_present"> {
    student?: AeeListStudent | null;
}

export interface AeeListRecord
    extends Pick<AeeRecordRecord, "id" | "duration"> {
    attendance_session?: AeeSession | null;
    student_evaluations?: AeeListEvaluation[];
}

export interface AeeRecordListPageProps extends AeeFilterOptions {
    aeeRecords: AeePaginator<AeeListRecord>;
    filters: Partial<AeeListFilters>;
    can: {
        view: boolean;
        view_all: boolean;
        view_own: boolean;
    };
}

export interface AeeStudentEvaluationListItem
    extends Pick<AeeStudentEvaluationRecord, "id" | "is_present"> {
    can_manage: boolean;
    aee_record?: {
        id: number;
        duration: string | null;
        attendance_session?: AeeSession | null;
    } | null;
}
