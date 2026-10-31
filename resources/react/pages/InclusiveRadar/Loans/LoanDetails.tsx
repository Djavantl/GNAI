import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import type { LoanRecord } from "./Loan.types";

export default function LoanDetails({ loan, showObservation = true }: {
    loan: LoanRecord;
    showObservation?: boolean;
}): React.JSX.Element {
    return (
        <dl className="loanShow__grid">
            <DetailField label="Tipo do Item">
                {loan.item_type_label}
            </DetailField>
            <DetailField label="Recurso Emprestado">
                {loan.loanable.name}
            </DetailField>
            <DetailField label="Patrimônio / Tombamento" wide>
                {loan.loanable.asset_code || "Não se aplica"}
            </DetailField>
            <DetailField label="Estudante (Beneficiário)">
                {loan.student?.name ?? "Não se aplica"}
            </DetailField>
            <DetailField label="Profissional (Beneficiário)">
                {loan.professional?.name ?? "Não se aplica"}
            </DetailField>
            <DetailField label="Usuário Responsável" wide>
                {loan.user.name}
            </DetailField>
            <DetailField label="Data de Saída">
                {loan.loan_date_label}
            </DetailField>
            <DetailField label="Previsão de Devolução">
                {loan.due_date_label}
            </DetailField>
            <DetailField label="Status do Empréstimo">
                <TableLabel tone={loan.status_color}>
                    {loan.status_label}
                </TableLabel>
            </DetailField>
            <DetailField label="Data Real da Devolução">
                {loan.return_date_label ?? "Não devolvido"}
            </DetailField>
            {showObservation ? (
                <DetailField label="Observações / Estado do Item" wide>
                    {loan.observation || "Nenhuma observação registrada."}
                </DetailField>
            ) : null}
        </dl>
    );
}
