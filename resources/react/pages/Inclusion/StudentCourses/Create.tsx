import StudentCourseForm from "./StudentCourseForm";
import type { StudentCourseCourse, StudentCourseStudent } from "./StudentCourse.types";

export default function InclusionStudentCoursesCreatePage({
    student,
    courses,
    mustBeCurrent,
}: {
    student: StudentCourseStudent;
    courses: StudentCourseCourse[];
    mustBeCurrent: boolean;
}): React.JSX.Element {
    return (
        <StudentCourseForm
            mode="create"
            student={student}
            courses={courses}
            mustBeCurrent={mustBeCurrent}
        />
    );
}
