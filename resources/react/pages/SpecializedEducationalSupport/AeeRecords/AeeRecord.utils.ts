import type {
    AeeEvaluationFormData,
    AeeStudentEvaluationRecord,
} from "./AeeRecord.types";

export const aeeBase = "/specialized-educational-support";
export const sessionShowUrl = (id: number): string => `${aeeBase}/sessions/${id}/show`;
export const aeeRecordShowUrl = (id: number): string => `${aeeBase}/aee-records/${id}/show`;
export const aeeRecordPdfUrl = (id: number): string => `${aeeBase}/aee-records/${id}/pdf`;
export const aeeRecordsIndexUrl = `${aeeBase}/aee-records`;
export const myAeeRecordsUrl = `${aeeBase}/my-aee-records`;
export const studentAeeRecordsUrl = (studentId: number): string => `${aeeBase}/students/${studentId}/aee-records`;
export const studentAeeEvaluationUrl = (
    studentId: number,
    evaluationId: number,
    action = "show",
): string => `${studentAeeRecordsUrl(studentId)}/${evaluationId}${action ? `/${action}` : ""}`;

export const absenceFields = ["absence_reason"] as const;
export const presenceFields = [
    "student_participation",
    "adaptations_made",
    "development_evaluation",
    "progress_indicators",
    "recommendations",
    "next_session_adjustments",
] as const;

export function emptyEvaluation(studentId: number): AeeEvaluationFormData {
    return {
        student_id: studentId,
        is_present: true,
        absence_reason: "",
        student_participation: "",
        adaptations_made: "",
        development_evaluation: "",
        progress_indicators: "",
        recommendations: "",
        next_session_adjustments: "",
    };
}

export function evaluationFromRecord(
    evaluation: AeeStudentEvaluationRecord,
): AeeEvaluationFormData {
    return {
        student_id: evaluation.student_id,
        is_present: Boolean(evaluation.is_present),
        absence_reason: evaluation.absence_reason ?? "",
        student_participation: evaluation.student_participation ?? "",
        adaptations_made: evaluation.adaptations_made ?? "",
        development_evaluation: evaluation.development_evaluation ?? "",
        progress_indicators: evaluation.progress_indicators ?? "",
        recommendations: evaluation.recommendations ?? "",
        next_session_adjustments: evaluation.next_session_adjustments ?? "",
    };
}

/**
 * Reproduz o legado: os campos ocultos pela presença/ausência do aluno
 * eram limpos e desabilitados, chegando ao backend sem valor.
 */
export function evaluationPayload(
    evaluation: AeeEvaluationFormData,
): Record<string, string | number | boolean | null> {
    const payload: Record<string, string | number | boolean | null> = {
        ...evaluation,
    };
    const hidden = evaluation.is_present ? absenceFields : presenceFields;

    hidden.forEach((field) => {
        payload[field] = null;
    });

    return payload;
}
