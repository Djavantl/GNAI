import WaitlistForm from "./WaitlistForm";
import type { WaitlistFormProps } from "./Waitlist.types";

export default function InclusiveRadarWaitlistsCreatePage(
    props: WaitlistFormProps,
): React.JSX.Element {
    return <WaitlistForm {...props} />;
}
