import ProfessionalForm from "./ProfessionalForm";
import ProfessionalPage from "./ProfessionalPage";
import type { ProfessionalFormOptions } from "./Professional.types";

export default function InclusionProfessionalsCreatePage(
    props: ProfessionalFormOptions,
): React.JSX.Element {
    return (
        <ProfessionalPage
            title="Novo Profissional"
            subtitle="Cadastre os dados pessoais e funcionais de um profissional ou tutor de pares. Mesmo com o título “Profissionais”, esta seção também permite o cadastro de tutores de pares, utilizando a mesma estrutura."
            current="Cadastrar"
        >
            <ProfessionalForm
                mode="create"
                {...props}
            />
        </ProfessionalPage>
    );
}
