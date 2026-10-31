import DeficiencyForm from "./DeficiencyForm";
import type { DeficiencyRecord } from "./Deficiency.types";

export default function InclusionDeficienciesEditPage({
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
