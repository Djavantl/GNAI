import PeiDisciplineForm, { type PeiDisciplineTeacherOption } from "./PeiDisciplineForm";

export default function InclusionPeisDisciplinesCreatePage({
    pei,
    teachers,
    loggedTeacher,
}: {
    pei: { id: number };
    teachers: PeiDisciplineTeacherOption[];
    loggedTeacher?: { id: number; name: string | null } | null;
}): React.JSX.Element {
    return (
        <PeiDisciplineForm
            pei={pei}
            teachers={teachers}
            loggedTeacher={loggedTeacher ?? null}
        />
    );
}
