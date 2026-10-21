import { router } from "@inertiajs/react";
import { useState } from "react";
import ChoiceControl from "@/components/Forms/ChoiceControl/ChoiceControl";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import LoanPage from "./LoanPage";
import type { LoanRecord } from "./Loan.types";
import "./Loan.css";

type Props = {
    loan: LoanRecord;
    can: {
        edit: boolean;
        return: boolean;
        destroy: boolean;
        pdf: boolean;
    };
};

const base = "/inclusive-radar/loans";

export default function InclusiveRadarLoansShowPage({
    loan,
    can,
}: Props): React.JSX.Element {
    const [action, setAction] = useState<"delete" | "return" | null>(null);
    const [processing, setProcessing] = useState(false);
    const [isDamaged, setIsDamaged] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${base}/${loan.id}`;

    function fail(message: string): void {
        setFailure(message);
    }

    function remove(): void {
        if (processing) return;

        setFailure("");
        setProcessing(true);
        router.delete(itemUrl, {
            onError: () => fail("Não foi possível excluir o empréstimo. Tente novamente."),
            onSuccess: () => setAction(null),
            onFinish: () => setProcessing(false),
        });
    }

    function returnItem(): void {
        if (processing) return;

        setFailure("");
        setProcessing(true);
        router.patch(
            `${itemUrl}/return`,
            {
                is_damaged: isDamaged,
            },
            {
                onError: () => fail("Não foi possível registrar a devolução. Tente novamente."),
                onSuccess: () => setAction(null),
                onFinish: () => setProcessing(false),
            },
        );
    }

    function closeModal(): void {
        if (processing) return;
        setAction(null);
        setFailure("");
    }

    return (
        <LoanPage
            title="Detalhes do Empréstimo"
            subtitle="Visualize as informações, os prazos e o histórico do recurso."
            loan={loan}
            className="loanShow"
        >
            {loan.is_overdue ? (
                <InlineAlert variant="warning" className="loanShow__notice">
                    <i
                        className="fa fa-exclamation-triangle loanShow__noticeIcon"
                        aria-hidden="true"
                    />
                    <div className="loanShow__noticeContent">
                        <strong>Este item está com a devolução atrasada.</strong>
                        <p>
                            O prazo encerrou em{" "}
                            <time dateTime={loan.due_date ?? undefined}>
                                {loan.due_date_label}
                            </time>
                            .
                        </p>
                    </div>
                </InlineAlert>
            ) : null}
            <CardBox className="loanShow__panel">
                <FormSection
                    id="loan-resource-details"
                    title="Identificação do Recurso"
                    variant="inline"
                    iconClass="fa fa-exchange"
                >
                    <dl className="loanShow__grid">
                        <DetailField label="Recurso Emprestado">
                            {loan.loanable.name}
                        </DetailField>
                        <DetailField label="Tipo de Recurso">
                            {loan.loanable_type_label}
                        </DetailField>
                        <DetailField label="Patrimônio / Tombamento" wide>
                            {loan.loanable.asset_code || "Não se aplica"}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="loan-people-details"
                    title="Beneficiário e Responsável"
                    variant="inline"
                    iconClass="fa fa-users"
                >
                    <dl className="loanShow__grid">
                        <DetailField label="Estudante (Beneficiário)">
                            {loan.student?.name ?? "Não se aplica"}
                        </DetailField>
                        <DetailField label="Profissional (Beneficiário)">
                            {loan.professional?.name ?? "Não se aplica"}
                        </DetailField>
                        <DetailField label="Usuário Responsável" wide>
                            {loan.user.name}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="loan-status-details"
                    title="Prazos e Status"
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <dl className="loanShow__grid">
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
                        <DetailField label="Observações" wide>
                            {loan.observation || "Nenhuma observação registrada."}
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={
                    can.pdf ? (
                        <Button
                            href={`${itemUrl}/pdf`}
                            target="_blank"
                            rel="noopener noreferrer"
                            tone="dark-red"
                            leftIconClass="fa fa-file-pdf-o"
                            label="PDF"
                            title="Exportar empréstimo em PDF"
                            aria-label="Exportar empréstimo em PDF"
                        />
                    ) : undefined
                }
            >
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para empréstimos"
                    aria-label="Voltar para empréstimos"
                    disabled={processing}
                />
                {can.edit ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar empréstimo"
                        aria-label="Editar empréstimo"
                        disabled={processing}
                    />
                ) : null}
                {can.return && loan.is_active ? (
                    <Button
                        tone="success"
                        leftIconClass="fa fa-undo"
                        label="Devolver"
                        title="Registrar devolução"
                        aria-label="Registrar devolução"
                        disabled={processing}
                        onClick={() => {
                            setFailure("");
                            setIsDamaged(false);
                            setAction("return");
                        }}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir empréstimo"
                        aria-label="Excluir empréstimo"
                        disabled={processing}
                        onClick={() => {
                            setFailure("");
                            setAction("delete");
                        }}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={action === "return"}
                title="Confirmar Devolução"
                message="Confirme a devolução deste recurso ao acervo."
                details={
                    <div className="loanReturnFields">
                        <ChoiceControl
                            type="checkbox"
                            id="loan-return-damaged"
                            name="is_damaged"
                            tone="danger"
                            label="Item devolvido com avarias ou danos"
                            checked={isDamaged}
                            disabled={processing}
                            onChange={setIsDamaged}
                        />
                    </div>
                }
                error={failure}
                processing={processing}
                confirmLabel="Confirmar devolução"
                processingLabel="Registrando devolução..."
                confirmTone="success"
                onClose={closeModal}
                onConfirm={returnItem}
            />
            <ConfirmActionModal
                open={action === "delete"}
                title="Excluir Empréstimo"
                message={
                    <>
                        Excluir permanentemente o empréstimo de <strong>{loan.loanable.name}</strong>?
                    </>
                }
                error={failure}
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo empréstimo..."
                confirmTone="dark-red"
                onClose={closeModal}
                onConfirm={remove}
            />
        </LoanPage>
    );
}
