import PublicationForm from "./PublicationForm";
import type { PublicationFormOptions } from "./Publication.types";

export default function InclusionPublicationsCreatePage({
    backRoute,
    options,
    can,
}: {
    backRoute: string;
    options: PublicationFormOptions;
    can: { submit: boolean };
}): React.JSX.Element {
    return (
        <PublicationForm
            backRoute={backRoute}
            options={options}
            canSubmit={can.submit}
        />
    );
}
