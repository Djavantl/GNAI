export type StudentContextOptionKey =
    | "learningLevels"
    | "attentionLevels"
    | "memoryLevels"
    | "reasoningLevels"
    | "communicationTypes"
    | "interactionLevels"
    | "socializationLevels"
    | "autonomyLevels";

export type StudentContextOptions = Record<StudentContextOptionKey, Record<string, string>>;

export interface StudentContextStudent {
    id: number;
}

export interface StudentContextProfile {
    name: string;
    registration: string | null;
    photo_url: string;
    status_label: string;
    status_color: string;
    deficiencies: {
        id: number;
        name: string | null;
        severity: string | null;
        severity_label: string | null;
    }[];
}

export interface StudentContextRecord {
    id: number;
    student_id: number;
    history: string | null;
    specific_educational_needs: string | null;
    knowledge: string | null;
    difficulties: string | null;
    learning_level: string | null;
    attention_level: string | null;
    memory_level: string | null;
    reasoning_level: string | null;
    learning_observations: string | null;
    communication_type: string | null;
    interaction_level: string | null;
    socialization_level: string | null;
    shows_aggressive_behavior: boolean | null;
    shows_withdrawn_behavior: boolean | null;
    behavior_notes: string | null;
    autonomy_level: string | null;
    needs_mobility_support: string | null;
    needs_communication_support: string | null;
    needs_pedagogical_adaptation: string | null;
    uses_assistive_technology: string | null;
    has_medical_report: boolean | null;
    uses_medication: boolean | null;
    medical_notes: string | null;
}

export interface StudentContextFormProps {
    student: StudentContextStudent;
    studentProfile: StudentContextProfile;
    studentContextOptions: StudentContextOptions;
}
