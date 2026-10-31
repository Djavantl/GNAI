import PublicationForm from "./PublicationForm";
import type {
    PublicationRecord,
    PublicationFormOptions,
} from "./Publication.types";

export default function InclusionPublicationsEditPage({
    publication,
    options,
    can,
}: {
    publication: PublicationRecord;
    options: PublicationFormOptions;
    can: { submit: boolean };
}): React.JSX.Element {
    return (
        <PublicationForm
            key={publication.id}
            publication={publication}
            options={options}
            canSubmit={can.submit}
        />
    );
}
