import StudentContextForm from "./StudentContextForm";
import type { StudentContextFormProps } from "./StudentContext.types";

export default function InclusionStudentContextCreatePage(
    props: StudentContextFormProps,
): React.JSX.Element {
    return <StudentContextForm {...props} />;
}
