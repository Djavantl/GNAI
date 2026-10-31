import CourseForm from "./CourseForm";
import type { CourseDisciplineOption } from "./Course.types";

export default function InclusionCoursesCreatePage({
    disciplines,
}: {
    disciplines: CourseDisciplineOption[];
}): React.JSX.Element {
    return <CourseForm disciplines={disciplines} />;
}
