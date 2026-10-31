import StudentContextForm from "./StudentContextForm";
import type { StudentContextFormProps, StudentContextRecord } from "./StudentContext.types";

export default function InclusionStudentContextEditPage({
    studentContext,
    ...props
}: StudentContextFormProps & {
    studentContext: StudentContextRecord;
}): React.JSX.Element {
    return (
        <StudentContextForm
            key={studentContext.id}
            {...props}
            studentContext={studentContext}
        />
    );
}
