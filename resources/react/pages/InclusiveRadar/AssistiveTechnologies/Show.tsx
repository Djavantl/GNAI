import { Head, router } from "@inertiajs/react";
import { useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import PageBreadcrumb from "@/components/Navigation/PageBreadcrumb/PageBreadcrumb";
import PageTitle from "@/components/Navigation/PageTitle/PageTitle";
import FlashNotifications from "@/components/UI/FlashNotifications/FlashNotifications";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import InlineAlert from "@/components/UI/InlineAlert/InlineAlert";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import InspectionHistory, { type InspectionDetail } from "./InspectionHistory";
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
        inspectionDetails: InspectionDetail[];
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
            className={`technologyShow__detail${wide ? " technologyShow__detail--wide" : ""}`}
        >
            <dt>{label}:</dt>
            <dd>{children}</dd>
        </div>
    );
}

export default function InclusiveRadarAssistiveTechnologiesShowPage({
    assistiveTechnology: technology,
    deficiencies,
    display,
    can,
}: Props): React.JSX.Element {
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");
    const itemUrl = `${base}/${technology.id}`;

    function remove(): void {
        if (
            deleting ||
            !window.confirm(
                `Excluir Tecnologia Assistiva\n\nA tecnologia ${technology.name} será removida do sistema.\n\nConfirmar exclusão?`,
            )
        )
            return;
        setDeleting(true);
        setFailure("");
        router.delete(itemUrl, {
            onError: () =>
                setFailure(
                    "Não foi possível excluir a tecnologia. Tente novamente.",
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
                actions={
                    <div
                        className="technologyShow__actions"
                        aria-label="Ações principais"
                    >
                        {can.clone && (
                            <Button
                                href={`${itemUrl}/clone`}
                                tone="primary"
                                leftIconClass="fa fa-copy"
                            >
                                Clonar
                            </Button>
                        )}
                        {can.edit && (
                            <Button
                                href={`${itemUrl}/edit`}
                                tone="warning"
                                leftIconClass="fa fa-edit"
                            >
                                Editar
                            </Button>
                        )}
                        <Button href={base} tone="cancel">
                            Voltar
                        </Button>
                    </div>
                }
            />
            <FlashNotifications />
            {failure && (
                <div role="alert">
                    <InlineAlert variant="danger">{failure}</InlineAlert>
                </div>
            )}
            <CardBox className="technologyShow__panel">
                <FormSection
                    id="technology-identification"
                    title="Identificação do Recurso"
                    variant="inline"
                    iconClass="fa fa-wheelchair"
                >
                    <dl className="technologyShow__grid">
                        <Detail label="Tipo da Tecnologia">
                            {technology.name}
                        </Detail>
                        <Detail label="Natureza do Recurso">
                            {technology.is_digital
                                ? "Recurso Digital"
                                : "Recurso Físico"}
                        </Detail>
                        <Detail label="Patrimônio / Tombamento">
                            {technology.asset_code || "Não se aplica"}
                        </Detail>
                        <Detail label="Descrição Detalhada" wide>
                            <ExpandableContent>
                                <div
                                    className="technologyShow__rich"
                                    dangerouslySetInnerHTML={{
                                        __html: display.notesHtml,
                                    }}
                                />
                            </ExpandableContent>
                        </Detail>
                    </dl>
                </FormSection>
                <FormSection
                    id="technology-management"
                    title="Gestão e Público"
                    variant="inline"
                    iconClass="fa fa-cogs"
                >
                    <dl className="technologyShow__grid">
                        <Detail label="Quantidade Total">
                            {technology.quantity ?? "---"}
                        </Detail>
                        <Detail label="Quantidade Disponível">
                            {technology.quantity_available ?? "---"}
                        </Detail>
                        <Detail label="Status do Recurso">
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
                        </Detail>
                        <Detail label="Permite Empréstimos">
                            {technology.is_loanable ? "Sim" : "Não"}
                        </Detail>
                        <Detail label="Status no Sistema">
                            <TableLabel
                                tone={
                                    technology.is_active ? "success" : "inverse"
                                }
                            >
                                {technology.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </Detail>
                        <Detail label="Público Alvo" wide>
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
                        </Detail>
                    </dl>
                </FormSection>
                {can.inspection && (
                    <InspectionHistory
                        technologyId={technology.id}
                        inspections={display.inspectionDetails}
                    />
                )}
            </CardBox>
            <FormActions>
                {can.pdf ? (
                    <Button
                        href={`${itemUrl}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        labelIconClass="fa fa-file-pdf-o"
                        aria-label="Exportar em PDF"
                    >
                        Exportar em PDF
                    </Button>
                ) : (
                    <span />
                )}
                <div className="technologyShow__actions">
                    {can.destroy && (
                        <Button
                            tone="dark-red"
                            leftIconClass="fa fa-trash-o"
                            disabled={deleting}
                            onClick={remove}
                        >
                            {deleting ? "Excluindo…" : "Excluir"}
                        </Button>
                    )}
                    <Button href={base} tone="cancel" disabled={deleting}>
                        Voltar
                    </Button>
                </div>
            </FormActions>
        </main>
    );
}
