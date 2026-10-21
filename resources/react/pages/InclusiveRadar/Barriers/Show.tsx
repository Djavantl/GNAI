import { router } from "@inertiajs/react";
import { useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import GeoMap from "@/components/InclusiveRadar/GeoMap/GeoMap";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { notify } from "@/components/UI/NotificationToast/notify";
import BarrierPage from "./BarrierPage";
import type { BarrierInstitution, BarrierRecord } from "./Barrier.types";
import "./Barriers.css";

type Props = {
    barrier: BarrierRecord;
    display: { descriptionHtml: string; locationDetailsHtml: string | null };
    can: { edit: boolean; destroy: boolean; pdf: boolean; inspectionShow: boolean };
};
const base = "/inclusive-radar/barriers";
const statusLabels: Record<string, string> = { identified: "Identificada", under_analysis: "Em Análise", in_progress: "Em Tratamento", resolved: "Resolvida", not_applicable: "Não Aplicável" };
const priorityLabels: Record<string, string> = { low: "Baixa", medium: "Média", high: "Alta", urgent: "Urgente" };

function formatDate(value?: string | null): string {
    return value ? value.slice(0, 10).split("-").reverse().join("/") : "Não informada";
}

function Detail({ label, value, wide = false }: { label: string; value: React.ReactNode; wide?: boolean }): React.JSX.Element {
    return <div className={`barrierShow__item${wide ? " barrierShow__wide" : ""}`}><dt>{label}</dt><dd>{value}</dd></div>;
}

export default function InclusiveRadarBarriersShowPage({ barrier, display, can }: Props): React.JSX.Element {
    const [deleting, setDeleting] = useState(false);
    const [deleteOpen, setDeleteOpen] = useState(false);
    const reference = barrier.institution ? {
        id: barrier.institution.id,
        name: barrier.institution.name,
        latitude: Number(barrier.institution.latitude),
        longitude: Number(barrier.institution.longitude),
        default_zoom: barrier.institution.default_zoom,
        locations: [],
    } as BarrierInstitution : null;
    const barrierLatitude = Number(barrier.latitude);
    const barrierLongitude = Number(barrier.longitude);
    const mapPoint = barrier.latitude !== null && barrier.latitude !== undefined
        && barrier.longitude !== null && barrier.longitude !== undefined
        && Number.isFinite(barrierLatitude) && Number.isFinite(barrierLongitude)
        ? { latitude: barrierLatitude, longitude: barrierLongitude }
        : { latitude: reference?.latitude ?? -14.235, longitude: reference?.longitude ?? -51.9253 };

    function remove(): void {
        if (deleting) return;
        setDeleting(true);
        router.delete(`${base}/${barrier.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                return false;
            },
            onHttpException: () => {
                notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível excluir a barreira. Tente novamente." });
                return false;
            },
            onFinish: () => { setDeleting(false); setDeleteOpen(false); },
        });
    }

    return (
        <BarrierPage title="Detalhes da Barreira" subtitle={`Visualize as informações cadastradas e o histórico de vistorias: ${barrier.name}`} barrier={barrier}>
            <CardBox>
                <div className="barrierShow__split">
                    <section className="barrierShow__content">
                        <FormSection id="barrier-show-details" title="Detalhes da Ocorrência" variant="inline" iconClass="fa fa-exclamation-triangle">
                            <dl className="barrierShow__details">
                                <Detail label="Título do Relato" value={barrier.name} />
                                <Detail label="Data" value={formatDate(barrier.identified_at)} />
                                <Detail label="Prioridade" value={<TableLabel tone={barrier.priority === "urgent" || barrier.priority === "high" ? "danger" : barrier.priority === "low" ? "info" : "warning"}>{priorityLabels[barrier.priority] ?? barrier.priority}</TableLabel>} />
                                <Detail label="Categoria" value={barrier.category?.name ?? "Não categorizada"} />
                                <Detail label="Campus / Unidade" value={barrier.institution?.name ?? "Não informado"} />
                                <Detail label="Local / Ponto de Referência" value={barrier.location?.name ?? "Não informado"} />
                                {barrier.location_specific_details ? <Detail label="Complemento" wide value={<div dangerouslySetInnerHTML={{ __html: display.locationDetailsHtml ?? "" }} />} /> : null}
                                <Detail label="Descrição Detalhada" wide value={<div dangerouslySetInnerHTML={{ __html: display.descriptionHtml }} />} />
                                <Detail label="Pessoa(s) Impactada(s)" wide value={barrier.is_anonymous
                                    ? <strong>Relato Anônimo</strong>
                                    : barrier.not_applicable
                                        ? <span>Relato Geral{barrier.affected_person_name || barrier.affected_person_role ? <><br /><small>Identificação Manual</small><br />{barrier.affected_person_name} {barrier.affected_person_role ? `(${barrier.affected_person_role})` : ""}</> : null}</span>
                                        : <span>{barrier.affected_student?.person?.name ? <>{barrier.affected_student.person.name} (Estudante)</> : null}{barrier.affected_student?.person?.name && barrier.affected_professional?.person?.name ? "; " : null}{barrier.affected_professional?.person?.name ? <>{barrier.affected_professional.person.name} (Profissional)</> : null}{!barrier.affected_student?.person?.name && !barrier.affected_professional?.person?.name ? "Não informado" : null}</span>} />
                                <Detail label="Público Alvo" wide value={barrier.deficiencies?.map((item) => item.name).join(", ") || "---"} />
                                <Detail label="Relator" value={barrier.is_anonymous ? "Contribuidor Anônimo" : barrier.registered_by?.name ?? "Usuário não identificado"} />
                                <Detail label="Status no Sistema" value={<TableLabel tone={barrier.is_active ? "success" : "inverse"}>{barrier.is_active ? "Ativo" : "Inativo"}</TableLabel>} />
                                {!barrier.category?.blocks_map ? <Detail label="Coordenadas" wide value={<span className="font-monospace">{barrier.latitude ?? "—"}, {barrier.longitude ?? "—"}</span>} /> : null}
                            </dl>
                        </FormSection>
                    </section>
                    <section className="barrierShow__content">
                        <FormSection id="barrier-show-map" title="Localização no Mapa" variant="inline" iconClass="fa fa-map-marker">
                            <div className="barrierShow__details">
                                <div className="barrierForm__mapFrame">
                                    <GeoMap title="Localização da Barreira" value={mapPoint} zoom={18} markerLabel="Barreira" reference={reference} />
                                    {barrier.category?.blocks_map ? <div className="barrierForm__mapBlock"><span><i className="fa fa-lock" aria-hidden="true" /><br />Mapa não se aplica à categoria<br />{barrier.category.name}.</span></div> : null}
                                </div>
                            </div>
                        </FormSection>
                        {can.inspectionShow ? <FormSection id="barrier-show-inspections" title="Histórico de Vistorias" variant="inline" iconClass="fa fa-history">
                            <div className="barrierShow__history">
                                {barrier.inspections?.length ? barrier.inspections.map((inspection) => (
                                    <Button key={inspection.id} href={`${base}/${barrier.id}/inspection/${inspection.id}`} className="barrierShow__inspection" labelIconClass="fa fa-clipboard-check" aria-label={`Ver detalhes da vistoria de ${formatDate(inspection.inspection_date)}`}>
                                        <strong>{formatDate(inspection.inspection_date)}</strong><br />
                                        {statusLabels[inspection.status ?? ""] ?? "Identificada"} · {inspection.type ?? "Vistoria"}
                                    </Button>
                                )) : <p>Nenhuma vistoria registrada para esta barreira. As vistorias ajudam a monitorar o status da resolução.</p>}
                            </div>
                        </FormSection> : null}
                    </section>
                </div>
            </CardBox>
            <FormActions secondary={can.pdf ? <Button href={`${base}/${barrier.id}/pdf`} tone="cancel" label="PDF" labelIconClass="fa fa-file-pdf-o" target="_blank" rel="noreferrer" /> : undefined}>
                {can.destroy ? <Button type="button" tone="dark-red" label="Excluir" labelIconClass="fa fa-trash-o" disabled={deleting} onClick={() => setDeleteOpen(true)} /> : null}
                {can.edit ? <Button href={`${base}/${barrier.id}/edit`} tone="save" label="Editar" labelIconClass="fa fa-pencil" /> : null}
                <Button href={base} tone="cancel" label="Voltar" />
            </FormActions>
            <ConfirmActionModal open={deleteOpen} title="Excluir Barreira" message={<>O registro <strong>{barrier.name}</strong> será excluído permanentemente.</>} processing={deleting} confirmLabel="Confirmar exclusão" processingLabel="Excluindo barreira..." confirmTone="dark-red" onClose={() => { if (!deleting) setDeleteOpen(false); }} onConfirm={remove} />
        </BarrierPage>
    );
}
