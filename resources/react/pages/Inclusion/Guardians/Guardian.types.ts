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

export type GuardianFilters = {
    name: string;
    email: string;
    relationship: string;
};

export interface GuardianPaginator {
    data: GuardianRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export interface GuardianIndexProps {
    student: GuardianStudent;
    guardians: GuardianPaginator;
    relationships: Record<string, string>;
    filters: Partial<GuardianFilters>;
    can: { create: boolean; view: boolean; delete: boolean };
}

export interface GuardianShowProps {
    guardian: GuardianRecord;
    student: GuardianStudent;
    display: {
        relationshipLabel: string | null;
        genderLabel: string | null;
        age: number | null;
    };
    can: { update: boolean; delete: boolean };
}
