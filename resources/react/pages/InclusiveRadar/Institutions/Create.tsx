import InstitutionForm from "./InstitutionForm";

export default function InclusiveRadarInstitutionsCreatePage({ canSubmit }: { canSubmit: boolean }): React.JSX.Element {
    return <InstitutionForm canSubmit={canSubmit} />;
}
