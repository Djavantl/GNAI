import SemesterForm from "./SemesterForm";
import type { SemesterRecord } from "./Semester.types";

export default function SpecializedEducationalSupportSemestersEditPage({
    semester,
}: {
    semester: SemesterRecord;
}): React.JSX.Element {
    return (
        <SemesterForm
            key={semester.id}
            semester={semester}
        />
    );
}
