import InstitutionalEventForm from "./InstitutionalEventForm";

export default function InclusiveRadarInstitutionalEventsCreatePage({
    backRoute,
    can,
}: {
    backRoute: string;
    can: { submit: boolean };
}): React.JSX.Element {
    return <InstitutionalEventForm backRoute={backRoute} canSubmit={can.submit} />;
}
