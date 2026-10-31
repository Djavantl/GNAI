export interface TeacherPerson {
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

export interface TeacherRecord {
    id: number;
    person: TeacherPerson;
    registration: string;
}

export interface TeacherFilters {
    name: string;
    email: string;
    registration: string;
}

export interface TeacherPaginator {
    data: TeacherRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export interface TeacherIndexProps {
    teachers: TeacherPaginator;
    filters: Partial<TeacherFilters>;
    can: { create: boolean; view: boolean; update: boolean; delete: boolean };
}
