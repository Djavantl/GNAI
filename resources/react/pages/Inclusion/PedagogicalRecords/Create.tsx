import PedagogicalRecordForm from "./PedagogicalRecordForm";
import type { PedagogicalRecordFormContext } from "./PedagogicalRecordForm.types";

export default function InclusionPedagogicalRecordsCreatePage({
    context,
}: {
    context: PedagogicalRecordFormContext;
}): React.JSX.Element {
    return <PedagogicalRecordForm context={context} />;
}
