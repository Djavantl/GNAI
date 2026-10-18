import AccessibleEducationalMaterialForm from "./AccessibleEducationalMaterialForm";
import AccessibleEducationalMaterialPage from "./AccessibleEducationalMaterialPage";
import type {
    MaterialFormOptions,
    MaterialRecord,
} from "./AccessibleEducationalMaterialForm.types";

export default function InclusiveRadarAccessibleEducationalMaterialsEditPage(
    props: MaterialFormOptions & {
        material: MaterialRecord;
        activeLoans: number;
    },
): React.JSX.Element {
    return (
        <AccessibleEducationalMaterialPage
            title="Editar Material Pedagógico Acessível"
            current="Editar"
            material={props.material}
            subtitle={`Atualizando informações de: ${props.material.name}`}
        >
            <AccessibleEducationalMaterialForm
                key={props.material.id}
                mode="edit"
                options={props}
                material={props.material}
                activeLoans={props.activeLoans}
            />
        </AccessibleEducationalMaterialPage>
    );
}
