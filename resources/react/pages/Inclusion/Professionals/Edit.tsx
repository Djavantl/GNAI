import ProfessionalForm from "./ProfessionalForm";
import ProfessionalPage from "./ProfessionalPage";
import type { ProfessionalFormOptions, ProfessionalRecord } from "./Professional.types";

export default function InclusionProfessionalsEditPage(
    props: ProfessionalFormOptions & { professional: ProfessionalRecord },
): React.JSX.Element {
    const { professional } = props;

    return (
        <ProfessionalPage
            title="Editar Profissional"
            subtitle="Edite os dados pessoais e funcionais do profissional ou tutor de pares. Mesmo com o título “Profissionais”, esta seção permite a atualização de informações de tutores de pares."
            current="Editar"
            professional={{ id: professional.id, name: professional.person.name }}
        >
            <ProfessionalForm
                key={professional.id}
                mode="edit"
                {...props}
            />
        </ProfessionalPage>
    );
}
