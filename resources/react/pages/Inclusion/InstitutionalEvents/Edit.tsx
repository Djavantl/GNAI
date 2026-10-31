import InstitutionalEventForm from "./InstitutionalEventForm";
import type {
    InstitutionalEventRecord,
    PublicationFormOptions,
} from "./InstitutionalEvent.types";

export default function InclusionInstitutionalEventsEditPage({
    event,
    options,
    can,
}: {
    event: InstitutionalEventRecord;
    options: PublicationFormOptions;
    can: { submit: boolean };
}): React.JSX.Element {
    return (
        <InstitutionalEventForm
            key={event.id}
            event={event}
            options={options}
            canSubmit={can.submit}
        />
    );
}
