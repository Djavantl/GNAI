import InstitutionalEventForm from "./InstitutionalEventForm";
import type { PublicationFormOptions } from "./InstitutionalEvent.types";

export default function InclusionInstitutionalEventsCreatePage({
    backRoute,
    options,
    can,
}: {
    backRoute: string;
    options: PublicationFormOptions;
    can: { submit: boolean };
}): React.JSX.Element {
    return (
        <InstitutionalEventForm
            backRoute={backRoute}
            options={options}
            canSubmit={can.submit}
        />
    );
}
