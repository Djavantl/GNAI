import PendencyForm from './PendencyForm';
import type { PendencyFormOptions } from './PendencyForm.types';

export default function InclusionPendenciesCreatePage({
  professionals,
  priorities,
}: PendencyFormOptions): React.JSX.Element {
  return (
    <PendencyForm
      professionals={professionals}
      priorities={priorities}
    />
  );
}
