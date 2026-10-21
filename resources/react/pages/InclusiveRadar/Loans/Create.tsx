import LoanForm from "./LoanForm";
import LoanPage from "./LoanPage";
import type { LoanFormProps, LoanableItem } from "./Loan.types";

type Props = LoanFormProps & {
    assistive_technologies: LoanableItem[];
    educational_materials: LoanableItem[];
    selectedStudentId: number | null;
    selectedProfessionalId: number | null;
    selectedItemId: number | null;
    selectedItemType: string | null;
};

export default function InclusiveRadarLoansCreatePage(
    props: Props,
): React.JSX.Element {
    return (
        <LoanPage
            title="Registrar Novo Empréstimo"
            subtitle="Vincule um recurso de acessibilidade a um beneficiário e defina os prazos de devolução."
            current="Cadastrar"
        >
            <LoanForm
                mode="create"
                students={props.students}
                professionals={props.professionals}
                authUser={props.authUser}
                can={props.can}
                assistiveTechnologies={props.assistive_technologies}
                educationalMaterials={props.educational_materials}
                selectedStudentId={props.selectedStudentId}
                selectedProfessionalId={props.selectedProfessionalId}
                selectedItemId={props.selectedItemId}
                selectedItemType={props.selectedItemType}
            />
        </LoanPage>
    );
}
