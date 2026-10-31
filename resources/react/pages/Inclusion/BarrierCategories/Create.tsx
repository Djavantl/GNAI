import BarrierCategoryForm from "./BarrierCategoryForm";

export default function InclusionBarrierCategoriesCreatePage({
    can,
}: {
    can: { store: boolean };
}): React.JSX.Element {
    return <BarrierCategoryForm canSubmit={can.store} />;
}
