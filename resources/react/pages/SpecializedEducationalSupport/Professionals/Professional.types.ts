export interface ProfessionalPerson {
    id: number;
    name: string;
    document: string | null;
    birth_date: string | null;
    gender: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    photo: string | null;
    photo_url?: string | null;
}

export interface ProfessionalRecord {
    id: number;
    person: ProfessionalPerson;
    position_id: number | null;
    registration: string;
    entry_date: string | null;
    status: string | null;
    user?: { id: number; is_admin: boolean } | null;
}

export interface ProfessionalFormOptions {
    positions: Array<{ id: number; name: string }>;
    genders: Record<string, string>;
    professionalStatuses: Record<string, string>;
    defaultGender?: string;
    defaultStatus?: string;
}
