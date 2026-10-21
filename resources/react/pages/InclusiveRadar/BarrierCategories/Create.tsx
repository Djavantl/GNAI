import BarrierCategoryForm from "./BarrierCategoryForm";

export default function InclusiveRadarBarrierCategoriesCreatePage({
    can,
}: {
    can: { store: boolean };
}): React.JSX.Element {
    return <BarrierCategoryForm canSubmit={can.store} />;
}
