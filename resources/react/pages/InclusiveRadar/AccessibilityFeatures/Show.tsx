import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import { notify } from "@/components/UI/NotificationToast/notify";
import AccessibilityFeaturePage from "./AccessibilityFeaturePage";
import type { AccessibilityFeatureRecord } from "./AccessibilityFeature.types";
import "./AccessibilityFeatures.css";

const base = "/inclusive-radar/accessibility-features";

export default function InclusiveRadarAccessibilityFeaturesShowPage({
    feature,
    display,
    can,
}: {
    feature: AccessibilityFeatureRecord;
    display: { descriptionHtml: string };
    can: { edit: boolean; destroy: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");

    useEffect(() => {
        if (!failure) return;
        notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function remove(): void {
        if (deleting) return;
        setFailure("");
        setDeleting(true);
        router.delete(`${base}/${feature.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o recurso. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <AccessibilityFeaturePage
            title="Detalhes do Recurso de Acessibilidade"
            subtitle={`Visualize as informações cadastrais e status do recurso: ${feature.name}`}
            feature={feature}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="accessibility-feature-show-identification"
                    title="Identificação do Recurso"
                    variant="inline"
                    iconClass="fa fa-universal-access"
                >
                    <dl className="accessibilityFeatureShow__grid">
                        <DetailField label="Nome do Recurso" wide>
                            {feature.name}
                        </DetailField>
                        <DetailField label="Descrição Detalhada" wide>
                            <ExpandableContent>
                                <div dangerouslySetInnerHTML={{ __html: display.descriptionHtml }} />
                            </ExpandableContent>
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="accessibility-feature-show-status"
                    title="Configurações de Status"
                    variant="inline"
                    iconClass="fa fa-toggle-on"
                >
                    <dl className="accessibilityFeatureShow__grid">
                        <DetailField label="Recurso Ativo">
                            <TableLabel tone={feature.is_active ? "success" : "inverse"}>
                                {feature.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                        {feature.materials_count !== undefined ? (
                            <DetailField label="Materiais Vinculados">
                                {feature.materials_count}
                            </DetailField>
                        ) : null}
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    aria-label="Voltar para recursos de acessibilidade"
                    disabled={deleting}
                />
                {can.edit ? (
                    <Button
                        href={`${base}/${feature.id}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        aria-label="Editar recurso de acessibilidade"
                        disabled={deleting}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        aria-label="Excluir recurso de acessibilidade"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Recurso de Acessibilidade"
                message={<>O recurso <strong>{feature.name}</strong> será excluído permanentemente.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo recurso..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={() => { if (!deleting) { setDeleteOpen(false); setFailure(""); } }}
                onConfirm={remove}
            />
        </AccessibilityFeaturePage>
    );
}
