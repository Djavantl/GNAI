import { router } from "@inertiajs/react";
import { useState } from "react";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import PublicationPage from "./PublicationPage";
import { scheduleStatusLabels, type PublicationRecord } from "./Publication.types";
import "./Publications.css";

type Props = {
    publication: PublicationRecord;
    backRoute: string;
    can: { edit: boolean; destroy: boolean; pdf: boolean };
};

const base = "/inclusive-radar/publications";

export default function InclusionPublicationsShowPage({
    publication,
    backRoute,
    can,
}: Props): React.JSX.Element {
    const [confirmingDelete, setConfirmingDelete] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [failure, setFailure] = useState("");
    const url = `${base}/${publication.id}`;

    function closeModal(): void {
        if (processing) return;
        setConfirmingDelete(false);
        setFailure("");
    }

    function remove(): void {
        if (processing) return;
        setFailure("");
        setProcessing(true);
        router.delete(url, {
            preserveScroll: true,
            onError: () => setFailure("Não foi possível excluir a publicação. Tente novamente."),
            onSuccess: () => setConfirmingDelete(false),
            onFinish: () => setProcessing(false),
        });
    }

    const statusLabel = publication.schedule_status ? scheduleStatusLabels[publication.schedule_status] : null;

    return (
        <PublicationPage
            title="Detalhes da publicação"
            subtitle="Visualize o conteúdo, as mídias e a situação da publicação no site."
            publicationId={publication.id}
            publicationTitle={publication.title}
            backRoute={backRoute}
        >
            <CardBox className="publicationShow__card">
                <FormSection
                    id="publication-show-main"
                    title="Conteúdo"
                    variant="inline"
                    iconClass="fa fa-file-text-o"
                >
                    <dl className="publicationShow__grid">
                        <DetailField label="Título" wide>{publication.title}</DetailField>
                        <DetailField label="Resumo" wide>{publication.summary || "Nenhum resumo informado."}</DetailField>
                        <DetailField label="Tipo">{publication.type_label}</DetailField>
                        <DetailField label="Etiquetas">{publication.tags.map((tag) => tag.name).join(", ") || "Não informado"}</DetailField>
                        <DetailField label="Autor">{publication.author_name || "Não informado"}</DetailField>
                        <DetailField label="Link">
                            {publication.link_url ? (
                                <a href={publication.link_url} target="_blank" rel="noopener noreferrer">
                                    {publication.link_label || publication.link_url}
                                </a>
                            ) : "Não informado"}
                        </DetailField>
                        <DetailField label="Texto da publicação" wide>
                            {publication.description_html ? (
                                <RichTextContent html={publication.description_html} className="publicationShow__richText" />
                            ) : "Nenhum texto fornecido."}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="publication-show-details"
                    title="Datas e local"
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <dl className="publicationShow__grid">
                        {publication.has_schedule ? (
                            <>
                                <DetailField label="Data de Início">{publication.start_date_label}</DetailField>
                                <DetailField label="Horário de Início">{publication.start_time || "Não informado"}</DetailField>
                                <DetailField label="Data de Término">{publication.end_date_label}</DetailField>
                                <DetailField label="Horário de Término">{publication.end_time || "Não informado"}</DetailField>
                                <DetailField label="Andamento">{statusLabel || "Não informado"}</DetailField>
                            </>
                        ) : (
                            <DetailField label="Datas" wide>Publicação sem data de início e término.</DetailField>
                        )}
                        <DetailField label="Local">{publication.location || "Não informado"}</DetailField>
                        <DetailField label="Organizador">{publication.organizer || "Não informado"}</DetailField>
                        <DetailField label="Público" wide>{publication.audience || "Não informado"}</DetailField>
                        <DetailField label="Participantes" wide>
                            {publication.speakers?.length ? (
                                <ul className="publicationShow__people">
                                    {publication.speakers.map((speaker) => (
                                        <li key={speaker.id}>
                                            <img src={speaker.photo_url ?? "/images/default-user.jpg"} alt="" />
                                            <span>
                                                {speaker.name}
                                                {speaker.participation || speaker.role ? <small>{[speaker.participation, speaker.role].filter(Boolean).join(" · ")}</small> : null}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            ) : "Nenhum participante vinculado."}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="publication-show-media"
                    title="Mídia"
                    variant="inline"
                    iconClass="fa fa-picture-o"
                >
                    <dl className="publicationShow__grid">
                        <DetailField label="Capa" wide>
                            {publication.cover_url ? (
                                <figure className="publicationShow__cover">
                                    <img src={publication.cover_url} alt={publication.cover_alt ?? ""} />
                                    <figcaption>Texto alternativo: {publication.cover_alt || "não informado"}</figcaption>
                                </figure>
                            ) : "Nenhuma capa enviada."}
                        </DetailField>
                        <DetailField label="Galeria" wide>
                            {publication.gallery?.length ? (
                                <ul className="publicationShow__gallery">
                                    {publication.gallery.map((image) => (
                                        <li key={image.id}>
                                            <img src={image.url} alt={image.alt} />
                                        </li>
                                    ))}
                                </ul>
                            ) : "Nenhuma foto na galeria."}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="publication-show-visibility"
                    title="Publicação no site"
                    variant="inline"
                    iconClass="fa fa-bullhorn"
                >
                    <dl className="publicationShow__grid">
                        <DetailField label="Situação">
                            <TableLabel tone={publication.is_active ? "success" : "inverse"}>
                                {publication.is_active ? "Publicado" : "Rascunho"}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Data de publicação">{publication.published_at_label}</DetailField>
                        <DetailField label="Destaque">{publication.is_featured ? "Sim" : "Não"}</DetailField>
                        <DetailField label="Endereço da página">
                            {publication.slug ? `/site/publicacoes/${publication.slug}` : "Não informado"}
                        </DetailField>
                        <DetailField label="Título para buscadores">{publication.seo_title || "Usa o título da publicação"}</DetailField>
                        <DetailField label="Descrição para buscadores">{publication.seo_description || "Usa o resumo"}</DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.pdf || publication.public_url ? (
                    <>
                        {can.pdf ? (
                            <Button
                                href={`${url}/pdf`}
                                target="_blank"
                                rel="noopener noreferrer"
                                tone="dark-red"
                                leftIconClass="fa fa-file-pdf-o"
                                label="PDF"
                                title="Exportar publicação em PDF"
                                aria-label="Exportar publicação em PDF"
                            />
                        ) : null}
                        {publication.public_url ? (
                            <Button
                                href={publication.public_url}
                                target="_blank"
                                rel="noopener noreferrer"
                                tone="list-action"
                                leftIconClass="fa fa-external-link"
                                label="Ver no site"
                                title="Abrir a publicação no site em nova aba"
                                aria-label="Abrir a publicação no site em nova aba"
                            />
                        ) : null}
                    </>
                ) : undefined}
            >
                <Button
                    href={backRoute || base}
                    tone="cancel"
                    label="Voltar"
                    title="Voltar para as publicações"
                    aria-label="Voltar para as publicações"
                    disabled={processing}
                />
                {can.edit ? (
                    <Button
                        href={`${url}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar publicação"
                        aria-label="Editar publicação"
                        disabled={processing}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir publicação"
                        aria-label="Excluir publicação"
                        disabled={processing}
                        onClick={() => setConfirmingDelete(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={confirmingDelete}
                title="Excluir Publicação"
                message={<>A publicação <strong>{publication.title}</strong> será excluída permanentemente, com sua capa e galeria.</>}
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo publicação..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={closeModal}
                onConfirm={remove}
            />
        </PublicationPage>
    );
}
