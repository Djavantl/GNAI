import InspectionDetails from "@/components/Inspections/InspectionDetails/InspectionDetails";
import type {
    InspectionDetailsData,
} from "@/components/Inspections/InspectionDetails/InspectionDetails.types";

type Props = {
    assistiveTechnology: {
        id: number;
        name: string;
    };
    inspection: InspectionDetailsData;
};

const base = "/inclusive-radar/assistive-technologies";

export default function InclusiveRadarAssistiveTechnologiesInspectionsShowPage({
    assistiveTechnology,
    inspection,
}: Props): React.JSX.Element {
    return (
        <InspectionDetails
            collection={{
                label: "Tecnologias Assistivas",
                href: base,
            }}
            inspection={inspection}
            subject={{
                name: assistiveTechnology.name,
                href: `${base}/${assistiveTechnology.id}`,
            }}
        />
    );
}
