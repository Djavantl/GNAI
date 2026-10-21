export interface WaitlistResource {
    id: number;
    name?: string | null;
    title?: string | null;
    asset_code?: string | null;
    description?: string | null;
}

export interface WaitlistPerson {
    name: string;
    registration?: string | null;
}

export interface WaitlistRecord {
    id: number;
    waitlistable_id: number;
    waitlistable_type: "assistive_technology" | "accessible_educational_material";
    item_name: string;
    asset_code?: string | null;
    student: WaitlistPerson | null;
    professional: WaitlistPerson | null;
    user: string;
    requested_at: string | null;
    requested_at_label: string;
    status: "waiting" | "notified" | "fulfilled" | "cancelled";
    status_label: string;
    status_color: string;
    observation: string | null;
    observation_html?: string;
}

export interface WaitlistPaginator {
    data: WaitlistRecord[];
    current_page: number;
    last_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface WaitlistEventRecord extends Omit<WaitlistRecord, "student" | "professional" | "user"> {
    student_id: number | null;
    professional_id: number | null;
}

export interface WaitlistStudentOption {
    id: number;
    name: string;
    registration: string;
}

export interface WaitlistProfessionalOption {
    id: number;
    name: string;
}

export interface WaitlistFormProps {
    students: Record<string, string> | WaitlistStudentOption[];
    professionals: Record<string, string> | WaitlistProfessionalOption[];
    authUser: { id: number; name: string };
    assistive_technologies?: WaitlistResource[];
    educational_materials?: WaitlistResource[];
    waitlist?: WaitlistRecord & {
        student_id: number | null;
        professional_id: number | null;
    };
    statusLabel?: string;
    can: { submit: boolean };
}
