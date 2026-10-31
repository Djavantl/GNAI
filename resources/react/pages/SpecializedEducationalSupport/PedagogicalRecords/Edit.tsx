import PedagogicalRecordForm from "./PedagogicalRecordForm";
import type {
    PedagogicalRecordFormContext,
    PedagogicalRecordFormRecord,
} from "./PedagogicalRecordForm.types";

export default function SpecializedEducationalSupportPedagogicalRecordsEditPage({
    pedagogicalRecord,
    context,
    returnStudentId,
}: {
    pedagogicalRecord: PedagogicalRecordFormRecord;
    context: PedagogicalRecordFormContext;
    returnStudentId?: number;
}): React.JSX.Element {
    return (
        <PedagogicalRecordForm
            key={pedagogicalRecord.id}
            record={pedagogicalRecord}
            context={context}
            returnStudentId={returnStudentId || undefined}
        />
    );
}
