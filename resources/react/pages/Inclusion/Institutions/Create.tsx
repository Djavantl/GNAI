import InstitutionForm from "./InstitutionForm";

export default function InclusionInstitutionsCreatePage({ canSubmit }: { canSubmit: boolean }): React.JSX.Element {
    return <InstitutionForm canSubmit={canSubmit} />;
}
