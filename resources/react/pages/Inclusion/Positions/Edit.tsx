import PositionForm from "./PositionForm";
import PositionPage from "./PositionPage";
import type { PositionFormProps, PositionRecord } from "./Position.types";

export default function InclusionPositionsEditPage({
    position,
    permissions,
}: PositionFormProps & {
    position: PositionRecord;
}): React.JSX.Element {
    return (
        <PositionPage
            title="Editar Cargo"
            subtitle={`Alterando informações do cargo: ${position.name}`}
            current="Editar"
            position={position}
        >
            <PositionForm
                key={position.id}
                position={position}
                permissions={permissions}
            />
        </PositionPage>
    );
}
