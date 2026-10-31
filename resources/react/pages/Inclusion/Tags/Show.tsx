import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { notify } from "@/components/UI/NotificationToast/notify";
import TagPage from "./TagPage";
import type { TagRecord } from "./Tag.types";
import "./Tags.css";

const base = "/inclusive-radar/tags";

export default function InclusionTagsShowPage({
    tag,
    can,
}: {
    tag: TagRecord;
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
        router.delete(`${base}/${tag.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir a etiqueta. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <TagPage
            title="Detalhes da Etiqueta"
            subtitle={`Visualize as informações cadastrais e status da etiqueta: ${tag.name}`}
            tag={tag}
        >
            <CardBox aria-busy={deleting}>
                <FormSection
                    id="tag-show-identification"
                    title="Identificação"
                    variant="inline"
                    iconClass="fa fa-tags"
                >
                    <dl className="tagShow__grid">
                        <DetailField label="Nome da Etiqueta" wide>
                            {tag.name}
                        </DetailField>
                        <DetailField label="Etiqueta Ativa">
                            <TableLabel tone={tag.is_active ? "success" : "inverse"}>
                                {tag.is_active ? "Ativo" : "Inativo"}
                            </TableLabel>
                        </DetailField>
                        {tag.publications_count !== undefined ? (
                            <DetailField label="Publicações Vinculadas">
                                {tag.publications_count}
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
                    aria-label="Voltar para etiquetas"
                    disabled={deleting}
                />
                {can.edit ? (
                    <Button
                        href={`${base}/${tag.id}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        aria-label="Editar etiqueta"
                        disabled={deleting}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        aria-label="Excluir etiqueta"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Etiqueta"
                message={<>A etiqueta <strong>{tag.name}</strong> será excluída permanentemente.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo etiqueta..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={() => { if (!deleting) { setDeleteOpen(false); setFailure(""); } }}
                onConfirm={remove}
            />
        </TagPage>
    );
}
