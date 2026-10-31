import CourseForm from "./CourseForm";
import type { CourseDisciplineOption, CourseRecord } from "./Course.types";

export default function SpecializedEducationalSupportCoursesEditPage({
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
