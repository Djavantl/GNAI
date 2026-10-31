export interface StudentCourseDiscipline {
    id: number;
    name: string;
}

export interface StudentCourseCourse {
    id: number;
    name: string;
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
