import StudentCourseForm from "./StudentCourseForm";
import type { StudentCourseRecord } from "./StudentCourse.types";

export default function InclusionStudentCoursesEditPage({
    studentCourse,
}: {
    studentCourse: StudentCourseRecord;
}): React.JSX.Element {
    return (
        <StudentCourseForm
            key={studentCourse.id}
            mode="edit"
            studentCourse={studentCourse}
        />
    );
}
