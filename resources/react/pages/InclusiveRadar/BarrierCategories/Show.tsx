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
import BarrierCategoryPage from "./BarrierCategoryPage";
import type { BarrierCategoryRecord } from "./BarrierCategory.types";
import "./BarrierCategories.css";

const base = "/inclusive-radar/barrier-categories";

export default function InclusiveRadarBarrierCategoriesShowPage({
    barrierCategory: category,
    display,
    can,
}: {
    barrierCategory: BarrierCategoryRecord;
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
        router.delete(`${base}/${category.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir a categoria. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <BarrierCategoryPage
            title="Detalhes da Categoria de Barreira"
            subtitle={`Visualize as informações cadastrais da categoria: ${category.name}`}
            category={category}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="barrier-category-show-information"
                    title="Informações da Categoria"
                    variant="inline"
                    iconClass="fa fa-tags"
                >
                    <dl className="barrierCategoryShow__grid">
                        <DetailField label="Nome da Categoria" wide>
                            {category.name}
                        </DetailField>
                        <DetailField label="Descrição Detalhada" wide>
                            <ExpandableContent>
                                <div dangerouslySetInnerHTML={{ __html: display.descriptionHtml }} />
                            </ExpandableContent>
                        </DetailField>
                        {category.barriers_count !== undefined ? (
                            <DetailField label="Barreiras Vinculadas">
                                {category.barriers_count}
                            </DetailField>
                        ) : null}
                    </dl>
                </FormSection>
                <FormSection
                    id="barrier-category-show-status"
                    title="Status e Visibilidade"
                    variant="inline"
                    iconClass="fa fa-cogs"
                >
                    <dl className="barrierCategoryShow__grid">
                        <DetailField label="Ativo no Sistema">
                            <TableLabel tone={category.is_active ? "success" : "inverse"}>
                                {category.is_active ? "Sim" : "Não"}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Mapa se Aplica">
                            <TableLabel tone={category.blocks_map ? "inverse" : "success"}>
                                {category.blocks_map ? "Não" : "Sim"}
                            </TableLabel>
                        </DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions>
                <Button
                    href={base}
                    tone="cancel"
                    label="Voltar"
                    aria-label="Voltar para categorias de barreiras"
                    disabled={deleting}
                />
                {can.edit ? (
                    <Button
                        href={`${base}/${category.id}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        aria-label="Editar categoria de barreira"
                        disabled={deleting}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        aria-label="Excluir categoria de barreira"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Categoria de Barreira"
                message={<>A categoria <strong>{category.name}</strong> será excluída permanentemente.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo categoria..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={() => { if (!deleting) { setDeleteOpen(false); setFailure(""); } }}
                onConfirm={remove}
            />
        </BarrierCategoryPage>
    );
}
