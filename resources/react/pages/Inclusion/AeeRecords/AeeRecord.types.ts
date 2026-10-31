export interface AeePerson {
    id?: number;
    name: string;
}

export interface AeeStudent {
    id: number;
    person?: AeePerson | null;
}

export interface AeeSession {
    id: number;
    session_date?: string | null;
    session_objective?: string | null;
    students?: AeeStudent[];
    professional?: {
        id: number;
        person?: AeePerson | null;
    } | null;
}

export interface AeeStudentEvaluationRecord {
    id: number;
    aee_record_id: number;
    student_id: number;
    is_present: boolean;
    absence_reason: string | null;
    student_participation: string | null;
    adaptations_made: string | null;
    development_evaluation: string | null;
    progress_indicators: string | null;
    recommendations: string | null;
    next_session_adjustments: string | null;
    student?: AeeStudent | null;
}

export interface AeeRecordRecord {
    id: number;
    attendance_session_id: number;
    duration: string;
    activities_performed: string;
    strategies_used: string | null;
    resources_used: string | null;
    general_observations: string | null;
    student_evaluations?: AeeStudentEvaluationRecord[];
    attendance_session?: AeeSession | null;
}

export interface AeeEvaluationFormData {
    student_id: number;
    is_present: boolean;
    absence_reason: string;
    student_participation: string;
    adaptations_made: string;
    development_evaluation: string;
    progress_indicators: string;
    recommendations: string;
    next_session_adjustments: string;
}

export type AeeEvaluationTextField = Exclude<
    keyof AeeEvaluationFormData,
    "student_id" | "is_present"
>;
