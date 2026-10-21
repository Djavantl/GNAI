import BarrierForm from "./BarrierForm";
import type { BarrierCategory, BarrierDeficiency, BarrierInstitution, BarrierOption } from "./Barrier.types";

export default function InclusiveRadarBarriersCreatePage({
    institutions,
    categories,
    deficiencies,
    students,
    professionals,
    priorities,
    barrierStatuses,
    selectedInstitution,
    canSubmit,
}: {
    institutions: BarrierInstitution[];
    categories: BarrierCategory[];
    deficiencies: BarrierDeficiency[];
    students: Record<string, string>;
    professionals: Record<string, string>;
    priorities: BarrierOption[];
    barrierStatuses: BarrierOption[];
    selectedInstitution: BarrierInstitution | null;
    canSubmit: boolean;
}): React.JSX.Element {
    return (
        <BarrierForm
            institutions={institutions}
            categories={categories}
            deficiencies={deficiencies}
            students={students}
            professionals={professionals}
            priorities={priorities}
            barrierStatuses={barrierStatuses}
            selectedInstitution={selectedInstitution}
            canSubmit={canSubmit}
        />
    );
}
