import { Head, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import PageBreadcrumb from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import PageTitle from "@/components/Navigation/PageTitle/PageTitle";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import { notify } from "@/components/UI/NotificationToast/notify";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import InspectionHistoryTable from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable";
import type { InspectionHistoryItem } from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable.types";
import { itemsBase as base } from "./Items.types";
import "./Show.css";

type Item = {
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
    item: Item;
    deficiencies: { id: number; name: string }[];
    features: { id: number; name: string }[];
    display: {
        itemTypeLabel: string;
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


function Tags({
    items,
    emptyMessage,
}: {
    items: { id: number; name: string }[];
    emptyMessage: string;
}): React.JSX.Element | string {
    return items.length ? (
        <ul className="itemShow__tags">
            {items.map((entry) => (
                <li key={entry.id}>{entry.name}</li>
            ))}
        </ul>
    ) : emptyMessage;
}

export default function InclusionItemsShowPage({
    item,
    deficiencies,
    features,
    display,
    can,
}: Props): React.JSX.Element {
    const [deleting, setDeleting] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${base}/${item.id}`;

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

    const statusTone =
        item.status === "available"
            ? "success"
            : item.status === "damaged"
              ? "danger"
              : item.status === "under_maintenance"
                ? "warning"
                : "inverse";

    return (
        <main className="itemShow" aria-busy={deleting}>
            <Head title={`${item.name} - Gnai`} />
            <nav aria-label="Localização">
                <PageBreadcrumb
                    items={[
                        { label: "Home", href: "/auth/dashboard" },
                        {
                            label: "Itens",
                            href: base,
                        },
                        { label: item.name },
                    ]}
                />
            </nav>
            <PageTitle
                title="Detalhes do Item"
                subtitle="Visualize as informações cadastrais, a disponibilidade e o histórico de vistorias do item."
            />
            <CardBox className="itemShow__panel">
                <FormSection
                    id="item-identification"
                    title="Identificação"
                    variant="inline"
                    iconClass="fa fa-cube"
                >
                    <dl className="itemShow__grid">
                        <DetailField label="Tipo do Item">
                            {display.itemTypeLabel}
                        </DetailField>
                        <DetailField label="Descrição do Item">
                            {item.name}
                        </DetailField>
                        <DetailField label="Natureza do Recurso">
                            {item.is_digital ? "Recurso Digital" : "Recurso Físico"}
                        </DetailField>
                        <DetailField label="Patrimônio / Tombamento">
                            {item.asset_code || "Não se aplica"}
                        </DetailField>
                        <DetailField label="Descrição Detalhada" wide>
                            <ExpandableContent>
                                <RichTextContent html={display.notesHtml} />
                            </ExpandableContent>
                        </DetailField>
                        <DetailField label="Recursos de Acessibilidade" wide>
                            <Tags
                                items={features}
                                emptyMessage="Nenhum recurso definido."
                            />
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="item-management"
                    title="Gestão"
                    variant="inline"
                    iconClass="fa fa-tasks"
                >
                    <dl className="itemShow__grid">
                        <DetailField label="Quantidade Total">
                            {item.is_digital ? "Não se aplica" : item.quantity ?? "---"}
                        </DetailField>
                        <DetailField label="Quantidade Disponível">
                            {item.is_digital ? "Não se aplica" : item.quantity_available ?? "---"}
                        </DetailField>
                        <DetailField label="Status do Recurso">
                            <TableLabel tone={statusTone}>
                                {display.statusLabel}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Permite Empréstimos">
                            {item.is_loanable ? "Sim" : "Não"}
                        </DetailField>
                        <DetailField label="Status no Sistema">
                            <TableLabel tone={item.is_active ? "success" : "inverse"}>
                                {item.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Público Alvo" wide>
                            <Tags
                                items={deficiencies}
                                emptyMessage="Nenhum público-alvo definido."
                            />
                        </DetailField>
                    </dl>
                </FormSection>
                {can.inspection ? (
                    <InspectionHistoryTable
                        id="item-inspections"
                        baseUrl={itemUrl}
                        inspections={display.inspectionDetails}
                    />
                ) : null}
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
                        title="Exportar item em PDF"
                        aria-label="Exportar item em PDF"
                    />
                ) : undefined}
            >
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para itens"
                    aria-label="Voltar para itens"
                    disabled={deleting}
                />
                {can.clone ? (
                    <Button
                        href={`${itemUrl}/clone`}
                        tone="primary"
                        leftIconClass="fa fa-copy"
                        label="Clonar"
                        title="Clonar item"
                        aria-label="Clonar item"
                    />
                ) : null}
                {can.edit ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar item"
                        aria-label="Editar item"
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir item"
                        aria-label="Excluir item"
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
                title="Excluir Item"
                message={
                    <>
                        Excluir o item <strong>{item.name}</strong>?
                    </>
                }
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo item..."
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
