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

export type TeacherFilters = {
    name: string;
    email: string;
    registration: string;
};

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

export interface TeacherCourseDiscipline {
    id: number;
    course: { id: number; name: string } | null;
    discipline: { id: number; name: string } | null;
}

export interface TeacherShowProps {
    teacher: TeacherRecord & {
        course_disciplines: TeacherCourseDiscipline[];
    };
    display: { createdAt: string | null };
    impersonationUserId: number | null;
    can: { update: boolean; delete: boolean };
}
