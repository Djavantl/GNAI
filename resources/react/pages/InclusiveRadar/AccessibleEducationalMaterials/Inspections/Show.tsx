import InspectionDetails from "@/components/Inspections/InspectionDetails/InspectionDetails";
import type {
    InspectionDetailsData,
} from "@/components/Inspections/InspectionDetails/InspectionDetails.types";

type Props = {
    material: {
        id: number;
        name: string;
    };
    inspection: InspectionDetailsData;
};

const base = "/inclusive-radar/accessible-educational-materials";

export default function InclusiveRadarAccessibleEducationalMaterialsInspectionsShowPage({
    material,
    inspection,
}: Props): React.JSX.Element {
    return (
        <InspectionDetails
            collection={{
                label: "Materiais Pedagógicos Acessíveis",
                href: base,
            }}
            inspection={inspection}
            subject={{
                name: material.name,
                href: `${base}/${material.id}`,
            }}
        />
    );
}
