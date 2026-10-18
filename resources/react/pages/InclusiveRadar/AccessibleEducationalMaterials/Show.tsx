import { Head, router } from "@inertiajs/react";
import { useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import PageBreadcrumb from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import PageTitle from "@/components/Navigation/PageTitle/PageTitle";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import InspectionHistoryTable from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable";
import type { InspectionHistoryItem } from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable.types";
import "./Show.css";

type Material = {
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
    material: Material;
    deficiencies: { id: number; name: string }[];
    features: { id: number; name: string }[];
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

const base = "/inclusive-radar/accessible-educational-materials";

function Detail({
    label,
    children,
    wide = false,
}: {
    label: string;
    children: React.ReactNode;
    wide?: boolean;
}): React.JSX.Element {
    return (
        <div
            className={`materialShow__detail${wide ? " materialShow__detail--wide" : ""}`}
        >
            <dt>{label}:</dt>
            <dd>{children}</dd>
        </div>
    );
}

function Tags({
    items,
    emptyMessage,
}: {
    items: { id: number; name: string }[];
    emptyMessage: string;
}): React.JSX.Element | string {
    return items.length ? (
        <ul className="materialShow__tags">
            {items.map((item) => (
                <li key={item.id}>{item.name}</li>
            ))}
        </ul>
    ) : emptyMessage;
}

export default function InclusiveRadarAccessibleEducationalMaterialsShowPage({
    material,
    deficiencies,
    features,
    display,
    can,
}: Props): React.JSX.Element {
    const [deleting, setDeleting] = useState(false);
    const [deleteModalOpen, setDeleteModalOpen] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${base}/${material.id}`;

    function remove(): void {
        if (deleting) {
            return;
        }

        setDeleting(true);
        setFailure("");
        router.delete(itemUrl, {
            onError: () =>
                setFailure(
                    "Não foi possível excluir o material. Tente novamente.",
                ),
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
        material.status === "available"
            ? "success"
            : material.status === "damaged"
              ? "danger"
              : material.status === "under_maintenance"
                ? "warning"
                : "inverse";

    return (
        <main className="materialShow" aria-busy={deleting}>
            <Head title={`${material.name} - Gnai`} />
            <nav aria-label="Localização">
                <PageBreadcrumb
                    items={[
                        { label: "Home", href: "/auth/dashboard" },
                        {
                            label: "Materiais Pedagógicos Acessíveis",
                            href: base,
                        },
                        { label: material.name },
                    ]}
                />
            </nav>
            <PageTitle
                title="Detalhes do Material Pedagógico Acessível"
                subtitle="Visualize as informações cadastrais, a disponibilidade e o histórico de vistorias do material."
            />
            {failure && !deleteModalOpen ? (
                <div role="alert">
                    <InlineAlert variant="danger">{failure}</InlineAlert>
                </div>
            ) : null}
            <CardBox className="materialShow__panel">
                <FormSection
                    id="material-identification"
                    title="Identificação do Material"
                    variant="inline"
                    iconClass="fa fa-book"
                >
                    <dl className="materialShow__grid">
                        <Detail label="Título do Material">
                            {material.name}
                        </Detail>
                        <Detail label="Natureza do Recurso">
                            {material.is_digital ? "Recurso Digital" : "Recurso Físico"}
                        </Detail>
                        <Detail label="Patrimônio / Tombamento">
                            {material.asset_code || "Não se aplica"}
                        </Detail>
                        <Detail label="Descrição Detalhada" wide>
                            <ExpandableContent>
                                <div
                                    className="materialShow__rich"
                                    dangerouslySetInnerHTML={{
                                        __html: display.notesHtml,
                                    }}
                                />
                            </ExpandableContent>
                        </Detail>
                        <Detail label="Recursos de Acessibilidade" wide>
                            <Tags
                                items={features}
                                emptyMessage="Nenhum recurso definido."
                            />
                        </Detail>
                    </dl>
                </FormSection>
                <FormSection
                    id="material-management"
                    title="Gestão e Público"
                    variant="inline"
                    iconClass="fa fa-cogs"
                >
                    <dl className="materialShow__grid">
                        <Detail label="Quantidade Total">
                            {material.quantity ?? "---"}
                        </Detail>
                        <Detail label="Quantidade Disponível">
                            {material.quantity_available ?? "---"}
                        </Detail>
                        <Detail label="Status do Recurso">
                            <TableLabel tone={statusTone}>
                                {display.statusLabel}
                            </TableLabel>
                        </Detail>
                        <Detail label="Permite Empréstimos">
                            {material.is_loanable ? "Sim" : "Não"}
                        </Detail>
                        <Detail label="Status no Sistema">
                            <TableLabel tone={material.is_active ? "success" : "inverse"}>
                                {material.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </Detail>
                        <Detail label="Público Alvo" wide>
                            <Tags
                                items={deficiencies}
                                emptyMessage="Nenhum público-alvo definido."
                            />
                        </Detail>
                    </dl>
                </FormSection>
                {can.inspection ? (
                    <InspectionHistoryTable
                        id="material-inspections"
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
                        iconOnly
                        aria-label="Exportar material em PDF"
                    />
                ) : undefined}
            >
                <Button
                    href={base}
                    tone="cancel"
                    iconOnly
                    aria-label="Voltar para materiais pedagógicos acessíveis"
                    disabled={deleting}
                />
                {can.clone ? (
                    <Button
                        href={`${itemUrl}/clone`}
                        tone="primary"
                        leftIconClass="fa fa-copy"
                        iconOnly
                        aria-label="Clonar material"
                    />
                ) : null}
                {can.edit ? (
                    <Button
                        href={`${itemUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        iconOnly
                        aria-label="Editar material"
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        iconOnly
                        aria-label="Excluir material"
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
                title="Excluir Material Pedagógico Acessível"
                message={
                    <>
                        Excluir o material <strong>{material.name}</strong>?
                    </>
                }
                error={failure || undefined}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo material..."
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
