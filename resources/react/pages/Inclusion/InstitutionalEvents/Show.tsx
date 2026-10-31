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
import InstitutionalEventPage from "./InstitutionalEventPage";
import { scheduleStatusLabels, type InstitutionalEventRecord } from "./InstitutionalEvent.types";
import "./InstitutionalEvents.css";

type Props = {
    event: InstitutionalEventRecord;
    backRoute: string;
    can: { edit: boolean; destroy: boolean; pdf: boolean };
};

const base = "/inclusive-radar/institutional-events";

export default function InclusionInstitutionalEventsShowPage({
    event,
    backRoute,
    can,
}: Props): React.JSX.Element {
    const [confirmingDelete, setConfirmingDelete] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [failure, setFailure] = useState("");
    const url = `${base}/${event.id}`;

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

    const statusLabel = event.schedule_status ? scheduleStatusLabels[event.schedule_status] : null;

    return (
        <InstitutionalEventPage
            title="Detalhes da publicação"
            subtitle="Visualize o conteúdo, as mídias e a situação da publicação no site."
            eventId={event.id}
            eventTitle={event.title}
            backRoute={backRoute}
        >
            <CardBox className="institutionalEventShow__card">
                <FormSection
                    id="institutional-event-show-main"
                    title="Conteúdo"
                    variant="inline"
                    iconClass="fa fa-file-text-o"
                >
                    <dl className="institutionalEventShow__grid">
                        <DetailField label="Título" wide>{event.title}</DetailField>
                        <DetailField label="Resumo" wide>{event.summary || "Nenhum resumo informado."}</DetailField>
                        <DetailField label="Tipo">{event.type_label}</DetailField>
                        <DetailField label="Categoria">{event.category || "—"}</DetailField>
                        <DetailField label="Autor">{event.author_name || "—"}</DetailField>
                        <DetailField label="Link">
                            {event.link_url ? (
                                <a href={event.link_url} target="_blank" rel="noopener noreferrer">
                                    {event.link_label || event.link_url}
                                </a>
                            ) : "—"}
                        </DetailField>
                        <DetailField label="Texto da publicação" wide>
                            {event.description_html ? (
                                <RichTextContent html={event.description_html} className="institutionalEventShow__richText" />
                            ) : "Nenhum texto fornecido."}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="institutional-event-show-details"
                    title="Datas e local"
                    variant="inline"
                    iconClass="fa fa-calendar"
                >
                    <dl className="institutionalEventShow__grid">
                        {event.has_schedule ? (
                            <>
                                <DetailField label="Data de Início">{event.start_date_label}</DetailField>
                                <DetailField label="Horário de Início">{event.start_time || "—"}</DetailField>
                                <DetailField label="Data de Término">{event.end_date_label}</DetailField>
                                <DetailField label="Horário de Término">{event.end_time || "—"}</DetailField>
                                <DetailField label="Andamento">{statusLabel || "—"}</DetailField>
                            </>
                        ) : (
                            <DetailField label="Datas" wide>Publicação sem data de início e término.</DetailField>
                        )}
                        <DetailField label="Local">{event.location || "—"}</DetailField>
                        <DetailField label="Organizador">{event.organizer || "—"}</DetailField>
                        <DetailField label="Público" wide>{event.audience || "—"}</DetailField>
                        <DetailField label="Palestrantes" wide>
                            {event.speakers?.length ? (
                                <ul className="institutionalEventShow__people">
                                    {event.speakers.map((speaker) => (
                                        <li key={speaker.id}>
                                            <img src={speaker.photo_url ?? "/images/default-user.jpg"} alt="" />
                                            <span>
                                                {speaker.name}
                                                {speaker.role ? <small>{speaker.role}</small> : null}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            ) : "Nenhum palestrante vinculado."}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="institutional-event-show-media"
                    title="Mídia"
                    variant="inline"
                    iconClass="fa fa-picture-o"
                >
                    <dl className="institutionalEventShow__grid">
                        <DetailField label="Capa" wide>
                            {event.cover_url ? (
                                <figure className="institutionalEventShow__cover">
                                    <img src={event.cover_url} alt={event.cover_alt ?? ""} />
                                    <figcaption>Texto alternativo: {event.cover_alt || "não informado"}</figcaption>
                                </figure>
                            ) : "Nenhuma capa enviada."}
                        </DetailField>
                        <DetailField label="Galeria" wide>
                            {event.gallery?.length ? (
                                <ul className="institutionalEventShow__gallery">
                                    {event.gallery.map((image) => (
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
                    id="institutional-event-show-visibility"
                    title="Publicação no site"
                    variant="inline"
                    iconClass="fa fa-bullhorn"
                >
                    <dl className="institutionalEventShow__grid">
                        <DetailField label="Situação">
                            <TableLabel tone={event.is_active ? "success" : "inverse"}>
                                {event.is_active ? "Publicado" : "Rascunho"}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Data de publicação">{event.published_at_label}</DetailField>
                        <DetailField label="Destaque">{event.is_featured ? "Sim" : "Não"}</DetailField>
                        <DetailField label="Endereço da página">
                            {event.slug ? `/site/publicacoes/${event.slug}` : "—"}
                        </DetailField>
                        <DetailField label="Título para buscadores">{event.seo_title || "Usa o título da publicação"}</DetailField>
                        <DetailField label="Descrição para buscadores">{event.seo_description || "Usa o resumo"}</DetailField>
                    </dl>
                </FormSection>
            </CardBox>
            <FormActions
                secondary={can.pdf || event.public_url ? (
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
                        {event.public_url ? (
                            <Button
                                href={event.public_url}
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
                message={<>A publicação <strong>{event.title}</strong> será excluída permanentemente, com sua capa e galeria.</>}
                processing={processing}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo publicação..."
                confirmTone="dark-red"
                error={failure || undefined}
                onClose={closeModal}
                onConfirm={remove}
            />
        </InstitutionalEventPage>
    );
}
