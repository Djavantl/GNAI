import SemesterForm from "./SemesterForm";
import type { SemesterRecord } from "./Semester.types";

export default function InclusionSemestersEditPage({
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
