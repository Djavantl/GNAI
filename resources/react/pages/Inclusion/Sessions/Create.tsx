import SessionForm from './SessionForm';
import type { SessionCreateProps } from './SessionForm.types';

export default function InclusionSessionsCreatePage(
  props: SessionCreateProps,
): React.JSX.Element {
  return (
    <SessionForm
      mode="create"
      {...props}
    />
  );
}
