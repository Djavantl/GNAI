import PeiDisciplineForm, { type PeiDisciplineRecord } from "./PeiDisciplineForm";

export default function InclusionPeisDisciplinesEditPage({
    pei,
    peiDiscipline,
}: {
    pei: { id: number };
    peiDiscipline: PeiDisciplineRecord;
}): React.JSX.Element {
    return (
        <PeiDisciplineForm
            key={peiDiscipline.id}
            pei={pei}
            peiDiscipline={peiDiscipline}
        />
    );
}
