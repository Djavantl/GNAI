import PositionForm from "./PositionForm";
import PositionPage from "./PositionPage";
import type { PositionFormProps } from "./Position.types";

export default function InclusionPositionsCreatePage({
    permissions,
}: PositionFormProps): React.JSX.Element {
    return (
        <PositionPage
            title="Cadastrar Novo Cargo"
            subtitle="Defina as atribuições e o status do cargo no sistema."
            current="Cadastrar"
        >
            <PositionForm permissions={permissions} />
        </PositionPage>
    );
}
