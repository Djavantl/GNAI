export interface PedagogicalRecordGuardianOption {
    id: number;
    name: string;
    relationship_label: string;
}

export interface PedagogicalRecordFormContext {
    session_id: number;
    session_objective: string | null;
    student_name: string | null;
    professional_name: string | null;
    session_date: string | null;
    start_time: string;
    end_time: string | null;
    attendance_type_label: string;
    guardians: PedagogicalRecordGuardianOption[];
}

export interface PedagogicalRecordFormRecord {
    id: number;
    follow_up_reason: string | null;
    duration: string | null;
    is_present: boolean;
    absence_reason: string | null;
    systematic_pedagogical_follow_up_record: string | null;
    strategies_and_resources_adopted: string | null;
    referrals_made: string | null;
    complementary_observations: string | null;
    guardians?: Array<{ id: number }>;
}
