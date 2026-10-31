import DisciplineForm from "./DisciplineForm";
import type { DisciplineRecord } from "./Discipline.types";

export default function InclusionDisciplinesEditPage({
    discipline,
}: {
    discipline: DisciplineRecord;
}): React.JSX.Element {
    return (
        <DisciplineForm
            key={discipline.id}
            discipline={discipline}
        />
    );
}
