import CourseForm from "./CourseForm";
import type { CourseDisciplineOption, CourseRecord } from "./Course.types";

export default function InclusionCoursesEditPage({
    course,
    disciplines,
}: {
    course: CourseRecord;
    disciplines: CourseDisciplineOption[];
}): React.JSX.Element {
    return (
        <CourseForm
            key={course.id}
            course={course}
            disciplines={disciplines}
        />
    );
}
