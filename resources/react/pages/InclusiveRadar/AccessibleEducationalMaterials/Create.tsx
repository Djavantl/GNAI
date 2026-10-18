import AccessibleEducationalMaterialForm from "./AccessibleEducationalMaterialForm";
import AccessibleEducationalMaterialPage from "./AccessibleEducationalMaterialPage";
import type {
    MaterialFormOptions,
    MaterialRecord,
} from "./AccessibleEducationalMaterialForm.types";

export default function InclusiveRadarAccessibleEducationalMaterialsCreatePage(
    props: MaterialFormOptions & { cloneSource?: MaterialRecord | null },
): React.JSX.Element {
    const cloning = !!props.cloneSource;

    return (
        <AccessibleEducationalMaterialPage
            title={
                cloning
                    ? "Clonar Material Pedagógico Acessível"
                    : "Novo Material Pedagógico Acessível"
            }
            current="Cadastrar"
            subtitle="Cadastre materiais adaptados e realize a vistoria inicial."
        >
            <AccessibleEducationalMaterialForm
                mode="create"
                options={props}
                cloneSource={props.cloneSource}
            />
        </AccessibleEducationalMaterialPage>
    );
}
