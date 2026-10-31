import AeeRecordForm from "./AeeRecordForm";
import type { AeeSession } from "./AeeRecord.types";

export default function InclusionAeeRecordsCreatePage({
    session,
}: {
    session: AeeSession;
}): React.JSX.Element {
    return (
        <AeeRecordForm
            key={session.id}
            session={session}
        />
    );
}
