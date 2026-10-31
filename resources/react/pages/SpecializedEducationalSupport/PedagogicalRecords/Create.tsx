import PedagogicalRecordForm from "./PedagogicalRecordForm";
import type { PedagogicalRecordFormContext } from "./PedagogicalRecordForm.types";

export default function SpecializedEducationalSupportPedagogicalRecordsCreatePage({
    context,
}: {
    context: PedagogicalRecordFormContext;
}): React.JSX.Element {
    return <PedagogicalRecordForm context={context} />;
}
