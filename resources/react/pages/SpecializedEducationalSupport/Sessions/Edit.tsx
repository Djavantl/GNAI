import SessionForm from './SessionForm';
import type { SessionEditProps } from './SessionForm.types';

export default function SpecializedEducationalSupportSessionsEditPage(
  props: SessionEditProps,
): React.JSX.Element {
  return (
    <SessionForm
      key={props.session.id}
      mode="edit"
      {...props}
    />
  );
}
