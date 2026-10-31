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

export type CourseListRecord = CourseRecord & {
    disciplines_count: number;
    student_courses_count: number;
};

export type CoursePaginator = {
    data: CourseListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
};

export type CourseFilters = {
    name: string;
    is_active: string;
};
