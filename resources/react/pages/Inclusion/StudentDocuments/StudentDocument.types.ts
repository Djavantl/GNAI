export interface StudentDocumentStudent {
    id: number;
    person: { name: string };
}

export interface StudentDocumentRecord {
    id: number;
    student_id: number;
    title: string;
    type: string;
    original_name: string;
}

export interface StudentDocumentFormOptions {
    student: StudentDocumentStudent;
    types: Record<string, string>;
    semester: { label: string } | null;
}

export type StudentDocumentListRecord = StudentDocumentRecord & {
    type_label: string;
    semester_label: string | null;
    file_size: number | null;
    created_at: string;
};

export interface StudentDocumentPaginator {
    data: StudentDocumentListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type StudentDocumentFilters = {
    title: string;
    type: string;
    semester_id: string;
};
