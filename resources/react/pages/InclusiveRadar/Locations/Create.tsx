import LocationForm from "./LocationForm";
import type { InstitutionMapData, InstitutionOption } from "./Location.types";

export default function InclusiveRadarLocationsCreatePage({
    institutions,
    institutionsData,
    selectedInstitution,
    canSubmit,
}: {
    institutions: InstitutionOption[];
    institutionsData: InstitutionMapData[];
    selectedInstitution: { id: number } | null;
    canSubmit: boolean;
}): React.JSX.Element {
    return (
        <LocationForm
            institutions={institutions}
            institutionsData={institutionsData}
            selectedInstitutionId={selectedInstitution?.id ?? null}
            canSubmit={canSubmit}
        />
    );
}
