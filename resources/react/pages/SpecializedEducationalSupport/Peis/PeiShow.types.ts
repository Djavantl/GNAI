export interface PeiShowRecord {
    id: number;
    version: number | null;
    is_finished: boolean;
    created_at: string | null;
    course_name: string | null;
    semester_label: string | null;
    responsible: string;
}

export interface PeiShowStudent {
    id: number;
    name: string;
    registration: string | null;
    photo_url: string | null;
    status_label: string | null;
    status_color: string | null;
    deficiencies: string[];
}

export interface PeiStudentContext {
    id: number;
    semester_label: string | null;
    evaluation_type_label: string | null;
    version: number | null;
    is_current: boolean;
    history_html: string;
    specific_educational_needs_html: string;
    knowledge_html: string;
    difficulties_html: string;
    learning_level_label: string | null;
    attention_level_label: string | null;
    memory_level_label: string | null;
    reasoning_level_label: string | null;
    learning_observations_html: string;
    communication_type_label: string | null;
    interaction_level_label: string | null;
    socialization_level_label: string | null;
    shows_aggressive_behavior: boolean;
    shows_withdrawn_behavior: boolean;
    behavior_notes_html: string;
    autonomy_level_label: string | null;
    needs_mobility_support_html: string;
    needs_communication_support_html: string;
    needs_pedagogical_adaptation_html: string;
    uses_assistive_technology_html: string;
    has_medical_report: boolean;
    uses_medication: boolean;
    medical_notes_html: string;
    evaluator_name: string | null;
    created_at: string | null;
    updated_at: string | null;
}

export interface PeiDisciplineSummary {
    id: number;
    discipline_name: string | null;
    teacher_name: string | null;
    created_at: string | null;
    can_update: boolean;
    can_delete: boolean;
}

export interface PeiShowPermissions {
    viewContext: boolean;
    viewDisciplines: boolean;
    createDiscipline: boolean;
    finish: boolean;
    delete: boolean;
    pdf: boolean;
}

export interface PeiShowProps {
    pei: PeiShowRecord;
    student: PeiShowStudent;
    studentContext: PeiStudentContext | null;
    peiDisciplines: PeiDisciplineSummary[] | null;
    activeTab?: string;
    can: PeiShowPermissions;
}

export interface PeiDisciplineDetails {
    id: number;
    discipline_name: string | null;
    teacher_name: string | null;
    specific_objectives_html: string;
    content_programmatic_html: string;
    methodologies_html: string;
    evaluations_html: string;
    complementary_records_html: string;
    opinion_html: string;
}

export interface PeiDisciplineShowProps {
    pei: {
        id: number;
        version: number | null;
        is_finished: boolean;
    };
    student: {
        id: number;
        name: string;
    };
    peiDiscipline: PeiDisciplineDetails;
    can: {
        update: boolean;
        delete: boolean;
        pdf: boolean;
    };
}
