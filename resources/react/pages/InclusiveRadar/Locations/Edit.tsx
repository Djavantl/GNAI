import LocationForm from "./LocationForm";
import type { InstitutionMapData, InstitutionOption, LocationRecord } from "./Location.types";

export default function InclusiveRadarLocationsEditPage({
    location,
    institutions,
    institutionsData,
    selectedInstitution,
    canSubmit,
}: {
    location: LocationRecord;
    institutions: InstitutionOption[];
    institutionsData: InstitutionMapData[];
    selectedInstitution: { id: number } | null;
    canSubmit: boolean;
}): React.JSX.Element {
    return (
        <LocationForm
            location={location}
            institutions={institutions}
            institutionsData={institutionsData}
            selectedInstitutionId={selectedInstitution?.id ?? null}
            canSubmit={canSubmit}
        />
    );
}
