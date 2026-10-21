import { Head, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import PageBreadcrumb from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import PageTitle from "@/components/Navigation/PageTitle/PageTitle";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import { notify } from "@/components/UI/NotificationToast/notify";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import InspectionHistoryTable from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable";
import type { InspectionHistoryItem } from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable.types";
import "./Show.css";

type Technology = {
    id: number;
    name: string;
    is_digital: boolean;
    asset_code: string | null;
    quantity: number | null;
    quantity_available: number | null;
    status: string | null;
    is_loanable: boolean;
    is_active: boolean;
};
type Props = {
    assistiveTechnology: Technology;
    deficiencies: { id: number; name: string }[];
    display: {
        notesHtml: string;
        statusLabel: string;
        inspectionDetails: InspectionHistoryItem[];
    };
    can: {
        clone: boolean;
        edit: boolean;
        inspection: boolean;
        pdf: boolean;
        destroy: boolean;
    };
};
const base = "/inclusive-radar/assistive-technologies";

export default function InclusiveRadarAssistiveTechnologiesShowPage({
    assistiveTechnology: technology,
    deficiencies,
    display,
    can,
}: Props): React.JSX.Element {
    const [deleting, setDeleting] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${base}/${technology.id}`;

    useEffect(() => {
        if (!failure) return;

        notify({
            tone: "error",
            title: "Não foi possível concluir",
            text: failure,
        });
    }, [failure]);

    function remove(): void {
        if (deleting) {
            return;
        }

        setDeleting(true);
        setFailure("");
        router.delete(itemUrl, {
            onHttpException: () => {
                setFailure(
                    "Não foi possível concluir a exclusão. Tente novamente.",
                );
                return false;
            },
            onNetworkError: () => {
                setFailure(
                    "Falha de conexão. Confira sua conexão e tente novamente.",
                );
                return false;
            },
            onSuccess: () => setDeleteModalOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <main className="technologyShow" aria-busy={deleting}>
            <Head title={`${technology.name} - Gnai`} />
            <nav aria-label="Localização">
                <PageBreadcrumb
                    items={[
                        { label: "Home", href: "/auth/dashboard" },
                        { label: "Tecnologias Assistivas", href: base },
                        { label: technology.name },
                    ]}
                />
            </nav>
            <PageTitle
                title="Detalhes da Tecnologia Assistiva"
                subtitle="Visualize as informações cadastrais, a disponibilidade e o histórico de vistorias do recurso."
            />
            <CardBox className="technologyShow__panel">
                <FormSection
                    id="technology-identification"
                    title="Identificação do Recurso"
                    variant="inline"
                    iconClass="fa fa-wheelchair"
                >
                    <dl className="technologyShow__grid">
                        <DetailField label="Tipo da Tecnologia">
                            {technology.name}
                        </DetailField>
                        <DetailField label="Natureza do Recurso">
                            {technology.is_digital
                                ? "Recurso Digital"
                                : "Recurso Físico"}
                        </DetailField>
                        <DetailField label="Patrimônio / Tombamento">
                            {technology.asset_code || "Não se aplica"}
                        </DetailField>
                        <DetailField label="Descrição Detalhada" wide>
                            <ExpandableContent>
                                <div
                                    className="technologyShow__rich"
                                    dangerouslySetInnerHTML={{
                                        __html: display.notesHtml,
                                    }}
                                />
                            </ExpandableContent>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="technology-management"
                    title="Gestão e Público"
                    variant="inline"
                    iconClass="fa fa-cogs"
                >
                    <dl className="technologyShow__grid">
                        <DetailField label="Quantidade Total">
                            {technology.quantity ?? "---"}
                        </DetailField>
                        <DetailField label="Quantidade Disponível">
                            {technology.quantity_available ?? "---"}
                        </DetailField>
                        <DetailField label="Status do Recurso">
                            <TableLabel
                                tone={
                                    technology.status === "available"
                                        ? "success"
                                        : technology.status === "damaged"
                                          ? "danger"
                                          : technology.status ===
                                              "under_maintenance"
                                            ? "warning"
                                            : "inverse"
                                }
                            >
                                {display.statusLabel}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Permite Empréstimos">
                            {technology.is_loanable ? "Sim" : "Não"}
                        </DetailField>
                        <DetailField label="Status no Sistema">
                            <TableLabel
                                tone={
                                    technology.is_active ? "success" : "inverse"
                                }
                            >
                                {technology.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Público Alvo" wide>
                            {deficiencies.length ? (
                                <ul className="technologyShow__tags">
                                    {deficiencies.map((deficiency) => (
                                        <li key={deficiency.id}>
                                            {deficiency.name}
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                "Nenhum público-alvo definido."
                            )}
                        </DetailField>
                    </dl>
                </FormSection>
                {can.inspection && (
                    <InspectionHistoryTable
                        id="technology-inspections"
                        baseUrl={itemUrl}
                        inspections={display.inspectionDetails}
                    />
                )}
            </CardBox>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${itemUrl}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Exportar tecnologia assistiva em PDF"
                        aria-label="Exportar tecnologia assistiva em PDF"
                    />
                ) : undefined}
            >
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para tecnologias assistivas"
                    aria-label="Voltar para tecnologias assistivas"
                    disabled={deleting}
                />
                {can.clone ? (
                    <Button
                        href={`${itemUrl}/clone`}
                        tone="primary"
                        leftIconClass="fa fa-copy"
                        label="Clonar"
                        title="Clonar tecnologia assistiva"
                        aria-label="Clonar tecnologia assistiva"
                    />
                ) : null}
                {can.edit ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar tecnologia assistiva"
                        aria-label="Editar tecnologia assistiva"
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir tecnologia assistiva"
                        aria-label="Excluir tecnologia assistiva"
                        disabled={deleting}
                        onClick={() => {
                            setFailure("");
                            setDeleteModalOpen(true);
                        }}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteModalOpen}
                title="Excluir Tecnologia Assistiva"
                message={
                    <>
                        Excluir a tecnologia <strong>{technology.name}</strong>?
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo tecnologia..."
                confirmTone="dark-red"
                onClose={() => {
                    setFailure("");
                    setDeleteModalOpen(false);
                }}
                onConfirm={remove}
            />
        </main>
    );
}
