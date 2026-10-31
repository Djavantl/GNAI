import TagForm from "./TagForm";
import type { TagRecord } from "./Tag.types";

export default function InclusionTagsEditPage({
    tag,
    can,
}: {
    tag: TagRecord;
    can: { update: boolean };
}): React.JSX.Element {
    return (
        <TagForm
            tag={tag}
            canSubmit={can.update}
        />
    );
}
