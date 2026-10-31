import InspectionDetails from "@/components/Inspections/InspectionDetails/InspectionDetails";
import type {
    InspectionDetailsData,
} from "@/components/Inspections/InspectionDetails/InspectionDetails.types";
import { itemsBase as base } from "../Items.types";

type Props = {
    item: {
        id: number;
        name: string;
    };
    inspection: InspectionDetailsData;
};


export default function InclusionItemsInspectionsShowPage({
    item,
    inspection,
}: Props): React.JSX.Element {
    return (
        <InspectionDetails
            collection={{
                label: "Itens",
                href: base,
            }}
            inspection={inspection}
            subject={{
                name: item.name,
                href: `${base}/${item.id}`,
            }}
        />
    );
}
