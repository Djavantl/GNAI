import InstitutionalEventForm from "./InstitutionalEventForm";
import type { InstitutionalEventRecord } from "./InstitutionalEvent.types";

export default function InclusiveRadarInstitutionalEventsEditPage({
    event,
    can,
}: {
    event: InstitutionalEventRecord;
    can: { submit: boolean };
}): React.JSX.Element {
    return (
        <InstitutionalEventForm
            key={event.id}
            event={event}
            canSubmit={can.submit}
        />
    );
}
