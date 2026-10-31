import LoanForm from "./LoanForm";
import LoanPage from "./LoanPage";
import type { LoanFormProps, LoanableItem } from "./Loan.types";

type Props = LoanFormProps & {
    itemTypes: Record<string, string>;
    items: LoanableItem[];
    selectedStudentId: number | null;
    selectedProfessionalId: number | null;
    selectedItemId: number | null;
    selectedItemType: string | null;
};

export default function InclusionLoansCreatePage(
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
                itemTypes={props.itemTypes}
                items={props.items}
                selectedStudentId={props.selectedStudentId}
                selectedProfessionalId={props.selectedProfessionalId}
                selectedItemId={props.selectedItemId}
                selectedItemType={props.selectedItemType}
            />
        </LoanPage>
    );
}
