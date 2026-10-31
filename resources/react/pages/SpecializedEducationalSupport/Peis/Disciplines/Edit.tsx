import PeiDisciplineForm, { type PeiDisciplineRecord } from "./PeiDisciplineForm";

export default function SpecializedEducationalSupportPeisDisciplinesEditPage({
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
