import CourseForm from "./CourseForm";
import type { CourseDisciplineOption } from "./Course.types";

export default function SpecializedEducationalSupportCoursesCreatePage({
    disciplines,
}: {
    disciplines: CourseDisciplineOption[];
}): React.JSX.Element {
    return <CourseForm disciplines={disciplines} />;
}
