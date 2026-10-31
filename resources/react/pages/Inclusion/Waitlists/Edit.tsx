import WaitlistForm from "./WaitlistForm";
import type { WaitlistFormProps } from "./Waitlist.types";

export default function InclusionWaitlistsEditPage(
    props: WaitlistFormProps,
): React.JSX.Element {
    return <WaitlistForm key={props.waitlist?.id} {...props} />;
}
