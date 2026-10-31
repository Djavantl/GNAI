import TagForm from "./TagForm";

export default function InclusionTagsCreatePage({
    can,
}: {
    can: { store: boolean };
}): React.JSX.Element {
    return <TagForm canSubmit={can.store} />;
}
