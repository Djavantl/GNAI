import PendencyForm from './PendencyForm';
import type { PendencyFormOptions, PendencyRecord } from './PendencyForm.types';

export default function InclusionPendenciesEditPage({
  pendency,
  pendencyForm,
  professionals,
  priorities,
}: PendencyFormOptions & {
  pendency: PendencyRecord;
  pendencyForm: { due_date: string };
}): React.JSX.Element {
  return (
    <PendencyForm
      key={pendency.id}
      pendency={pendency}
      dueDate={pendencyForm.due_date}
      professionals={professionals}
      priorities={priorities}
    />
  );
}
