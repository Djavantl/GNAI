export interface GuardianStudent {
    id: number;
    person: { name: string };
}

export interface GuardianPerson {
    name: string;
    document: string | null;
    birth_date: string | null;
    gender: string | null;
    email: string | null;
    phone: string | null;
    address: string | null;
    photo: string | null;
    photo_url?: string;
}

export interface GuardianRecord {
    id: number;
    relationship: string | null;
    person: GuardianPerson;
}

export interface GuardianFormOptions {
    student: GuardianStudent;
    genders: Record<string, string>;
    relationships: Record<string, string>;
}
