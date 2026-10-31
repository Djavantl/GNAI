import { router } from "@inertiajs/react";
import { useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import MapSplit, { MapSplitAside, MapSplitMain } from "@/components/Inclusion/MapSplit/MapSplit";
import GeoMap from "@/components/Inclusion/GeoMap/GeoMap";
import InspectionHistoryTable from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable";
import type { InspectionHistoryItem } from "@/components/Inspections/InspectionHistoryTable/InspectionHistoryTable.types";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import DetailField from "@/components/UI/DetailField/DetailField";
import ExpandableContent from "@/components/UI/ExpandableContent/ExpandableContent";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import { notify } from "@/components/UI/NotificationToast/notify";
import BarrierPage from "./BarrierPage";
import type { BarrierInstitution, BarrierRecord } from "./Barrier.types";
import "./Barriers.css";

type Props = {
    barrier: BarrierRecord;
    display: {
        descriptionHtml: string;
        locationDetailsHtml: string | null;
        inspectionDetails: InspectionHistoryItem[];
    };
    can: { edit: boolean; destroy: boolean; pdf: boolean; inspectionShow: boolean };
};

const base = "/inclusive-radar/barriers";
const priorityLabels: Record<string, string> = { low: "Baixa", medium: "Média", high: "Alta", urgent: "Urgente" };

function formatDate(value?: string | null): string {
    return value ? value.slice(0, 10).split("-").reverse().join("/") : "Não informada";
}

function affectedPeople(barrier: BarrierRecord): React.ReactNode {
    if (barrier.is_anonymous) return <strong>Relato Anônimo</strong>;

    if (barrier.not_applicable) {
        const manual = barrier.affected_person_name || barrier.affected_person_role;

        return (
            <span>
                Relato Geral
                {manual ? (
                    <>
                        <br />
                        <small>Identificação Manual</small>
                        <br />
                        {barrier.affected_person_name} {barrier.affected_person_role ? `(${barrier.affected_person_role})` : ""}
                    </>
                ) : null}
            </span>
        );
    }

    const people = [
        barrier.affected_student?.person?.name ? `${barrier.affected_student.person.name} (Estudante)` : null,
        barrier.affected_professional?.person?.name ? `${barrier.affected_professional.person.name} (Profissional)` : null,
    ].filter(Boolean);

    return people.length ? people.join("; ") : "Não informado";
}

export default function InclusionBarriersShowPage({ barrier, display, can }: Props): React.JSX.Element {
    const [deleting, setDeleting] = useState(false);
    const [deleteOpen, setDeleteOpen] = useState(false);
    const barrierUrl = `${base}/${barrier.id}`;
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
    const hasCoordinates = barrier.latitude !== null && barrier.latitude !== undefined
        && barrier.longitude !== null && barrier.longitude !== undefined
        && Number.isFinite(barrierLatitude) && Number.isFinite(barrierLongitude);
    const mapPoint = hasCoordinates
        ? { latitude: barrierLatitude, longitude: barrierLongitude }
        : { latitude: reference?.latitude ?? -14.235, longitude: reference?.longitude ?? -51.9253 };
    const priorityTone = barrier.priority === "urgent" || barrier.priority === "high"
        ? "danger"
        : barrier.priority === "low" ? "info" : "warning";

    function remove(): void {
        if (deleting) return;
        setDeleting(true);
        router.delete(barrierUrl, {
            preserveScroll: true,
            onNetworkError: () => {
                notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                return false;
            },
            onHttpException: () => {
                notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível excluir a barreira. Tente novamente." });
                return false;
            },
            onFinish: () => {
                setDeleting(false);
                setDeleteOpen(false);
            },
        });
    }

    return (
        <BarrierPage
            title="Detalhes da Barreira"
            subtitle={`Visualize as informações cadastradas e o histórico de vistorias: ${barrier.name}`}
            barrier={barrier}
        >
            <CardBox className="barrierShow__panel">
                <MapSplit>
                    <MapSplitMain>
                        <FormSection id="barrier-show-identification" title="Identificação" variant="inline" iconClass="fa fa-exclamation-triangle">
                            <dl className="barrierShow__grid">
                                <DetailField label="Título do Relato">{barrier.name}</DetailField>
                                <DetailField label="Data">{formatDate(barrier.identified_at)}</DetailField>
                                <DetailField label="Prioridade">
                                    <TableLabel tone={priorityTone}>{priorityLabels[barrier.priority] ?? barrier.priority}</TableLabel>
                                </DetailField>
                                <DetailField label="Categoria">{barrier.category?.name ?? "Não categorizada"}</DetailField>
                                <DetailField label="Campus / Unidade">{barrier.institution?.name ?? "Não informado"}</DetailField>
                                <DetailField label="Local / Ponto de Referência">{barrier.location?.name ?? "Não informado"}</DetailField>
                                <DetailField label="Relator">
                                    {barrier.is_anonymous ? "Contribuidor Anônimo" : barrier.registered_by?.name ?? "Usuário não identificado"}
                                </DetailField>
                                <DetailField label="Status no Sistema">
                                    <TableLabel tone={barrier.is_active ? "success" : "inverse"}>{barrier.is_active ? "Ativo" : "Inativo"}</TableLabel>
                                </DetailField>
                                {display.locationDetailsHtml ? (
                                    <DetailField label="Complemento" wide>
                                        <RichTextContent html={display.locationDetailsHtml} />
                                    </DetailField>
                                ) : null}
                                <DetailField label="Descrição Detalhada" wide>
                                    <ExpandableContent>
                                        <RichTextContent html={display.descriptionHtml} />
                                    </ExpandableContent>
                                </DetailField>
                                <DetailField label="Público Alvo" wide>
                                    {barrier.deficiencies?.length ? (
                                        <ul className="barrierShow__tags">
                                            {barrier.deficiencies.map((item) => <li key={item.id}>{item.name}</li>)}
                                        </ul>
                                    ) : "---"}
                                </DetailField>
                                <DetailField label="Pessoa(s) Impactada(s)" wide>{affectedPeople(barrier)}</DetailField>
                            </dl>
                        </FormSection>
                    </MapSplitMain>
                    <MapSplitAside>
                        <FormSection id="barrier-show-map" title="Localização no Mapa" variant="inline" iconClass="fa fa-map-marker">
                            {!barrier.category?.blocks_map ? (
                                <dl className="barrierShow__grid barrierShow__coordinates">
                                    <DetailField label="Latitude">{hasCoordinates ? barrierLatitude.toFixed(8) : "Não informado"}</DetailField>
                                    <DetailField label="Longitude">{hasCoordinates ? barrierLongitude.toFixed(8) : "Não informado"}</DetailField>
                                </dl>
                            ) : null}
                            <div className="barrierForm__mapFrame">
                                <GeoMap
                                    title="Localização da Barreira"
                                    value={mapPoint}
                                    zoom={reference?.default_zoom ?? 16}
                                    markerLabel="Barreira"
                                    reference={reference}
                                />
                                {barrier.category?.blocks_map ? (
                                    <div className="barrierForm__mapBlock">
                                        <span>
                                            <i className="fa fa-lock" aria-hidden="true" />
                                            <br />
                                            Mapa não se aplica à categoria
                                            <br />
                                            {barrier.category.name}.
                                        </span>
                                    </div>
                                ) : null}
                            </div>
                        </FormSection>
                    </MapSplitAside>
                </MapSplit>
                {can.inspectionShow ? (
                    <InspectionHistoryTable
                        id="barrier-show-inspections"
                        baseUrl={barrierUrl}
                        inspections={display.inspectionDetails}
                        stateLabel="Status da Barreira"
                    />
                ) : null}
            </CardBox>
            <FormActions
                secondary={can.pdf ? (
                    <Button
                        href={`${barrierUrl}/pdf`}
                        target="_blank"
                        rel="noopener noreferrer"
                        tone="dark-red"
                        leftIconClass="fa fa-file-pdf-o"
                        label="PDF"
                        title="Exportar barreira em PDF"
                        aria-label="Exportar barreira em PDF"
                    />
                ) : undefined}
            >
                <Button href={base} tone="cancel" label="Voltar" title="Voltar para barreiras" aria-label="Voltar para barreiras" disabled={deleting} />
                {can.edit ? (
                    <Button
                        href={`${barrierUrl}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        title="Editar barreira"
                        aria-label="Editar barreira"
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        type="button"
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        title="Excluir barreira"
                        aria-label="Excluir barreira"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Barreira"
                message={<>O registro <strong>{barrier.name}</strong> será excluído permanentemente.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo barreira..."
                confirmTone="dark-red"
                onClose={() => {
                    if (!deleting) setDeleteOpen(false);
                }}
                onConfirm={remove}
            />
        </BarrierPage>
    );
}
