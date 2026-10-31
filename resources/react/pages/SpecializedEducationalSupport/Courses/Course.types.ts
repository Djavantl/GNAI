export interface CourseDisciplineOption {
    id: number;
    name: string;
}

export interface CourseRecord {
    id: number;
    name: string;
    description: string | null;
    is_active: boolean;
    disciplines?: CourseDisciplineOption[];
}
