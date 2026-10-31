import DeficiencyForm from "./DeficiencyForm";
import type { DeficiencyRecord } from "./Deficiency.types";

export default function SpecializedEducationalSupportDeficienciesEditPage({
    deficiency,
}: {
    deficiency: DeficiencyRecord;
}): React.JSX.Element {
    return (
        <DeficiencyForm
            key={deficiency.id}
            deficiency={deficiency}
        />
    );
}
