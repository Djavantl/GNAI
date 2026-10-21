import BarrierCategoryForm from "./BarrierCategoryForm";
import type { BarrierCategoryRecord } from "./BarrierCategory.types";

export default function InclusiveRadarBarrierCategoriesEditPage({
    barrierCategory,
    can,
}: {
    barrierCategory: BarrierCategoryRecord;
    can: { update: boolean };
}): React.JSX.Element {
    return <BarrierCategoryForm category={barrierCategory} canSubmit={can.update} />;
}
