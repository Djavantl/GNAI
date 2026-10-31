export interface StudentCourseDiscipline {
    id: number;
    name: string;
    description?: string | null;
}

export interface StudentCourseCourse {
    id: number;
    name: string;
    description?: string | null;
    disciplines?: StudentCourseDiscipline[];
}

export interface StudentCourseStudent {
    id: number;
    registration: string | null;
    person: { name: string };
}

export interface StudentCourseRecord {
    id: number;
    student_id: number;
    course_id: number;
    academic_year: number | string | null;
    is_current: boolean;
    school_attendance_status: string | null;
    student: StudentCourseStudent;
    course: StudentCourseCourse;
    failed_disciplines?: { id: number }[];
    at_risk_disciplines?: { id: number }[];
}

export type StudentCourseShowRecord = Omit<
    StudentCourseRecord,
    "failed_disciplines" | "at_risk_disciplines"
> & {
    failed_disciplines: StudentCourseDiscipline[];
    at_risk_disciplines: StudentCourseDiscipline[];
    created_at: string | null;
    updated_at: string | null;
};

export interface StudentCourseListRecord {
    id: number;
    academic_year: number | string | null;
    is_current: boolean;
    course: { id: number; name: string } | null;
}

export interface StudentCoursePaginator {
    data: StudentCourseListRecord[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

export type StudentCourseFilters = {
    course_id: string;
    academic_year: string;
    is_current: string;
};
