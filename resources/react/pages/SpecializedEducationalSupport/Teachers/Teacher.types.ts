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
