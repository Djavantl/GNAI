import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import LoanForm from "./LoanForm";
import LoanPage from "./LoanPage";
import type { LoanFormProps, LoanRecord } from "./Loan.types";
import "./Loan.css";

type Props = LoanFormProps & {
    display: LoanRecord;
};

export default function InclusiveRadarLoansEditPage({
    display: loan,
    ...props
}: Props): React.JSX.Element {
    return (
        <LoanPage
            title="Editar Registro de Empréstimo"
            subtitle="Atualize as observações registradas para este empréstimo."
            current="Editar"
            loan={loan}
        >
            {loan.is_returned ? (
                <InlineAlert variant="info" className="loanForm__notice">
                    <strong>Este empréstimo já foi finalizado.</strong> Apenas as
                    observações podem ser atualizadas.
                </InlineAlert>
            ) : null}
            {loan.is_overdue ? (
                <InlineAlert variant="warning" className="loanForm__notice">
                    <strong>Este item está com a devolução atrasada.</strong> O
                    prazo encerrou em {loan.due_date_label}.
                </InlineAlert>
            ) : null}
            <LoanForm
                mode="edit"
                students={props.students}
                professionals={props.professionals}
                authUser={props.authUser}
                can={props.can}
                loan={loan}
            />
        </LoanPage>
    );
}
