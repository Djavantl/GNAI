import InstitutionForm from "./InstitutionForm";
import type { InstitutionRecord } from "./Institution.types";

export default function InclusiveRadarInstitutionsEditPage({ institution, canSubmit }: { institution: InstitutionRecord; canSubmit: boolean }): React.JSX.Element {
    return <InstitutionForm institution={institution} canSubmit={canSubmit} />;
}
