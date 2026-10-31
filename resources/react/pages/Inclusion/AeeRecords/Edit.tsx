import AeeRecordForm from "./AeeRecordForm";
import type { AeeRecordRecord, AeeSession } from "./AeeRecord.types";

export default function InclusionAeeRecordsEditPage({
    aeeRecord,
    session,
}: {
    aeeRecord: AeeRecordRecord;
    session: AeeSession;
}): React.JSX.Element {
    return (
        <AeeRecordForm
            key={aeeRecord.id}
            session={session}
            aeeRecord={aeeRecord}
        />
    );
}
