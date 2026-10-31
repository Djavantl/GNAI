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
