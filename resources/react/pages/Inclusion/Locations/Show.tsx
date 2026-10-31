import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import DetailField from "@/components/UI/DetailField/DetailField";
import RichTextContent from "@/components/UI/RichTextContent/RichTextContent";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import MapSplit, { MapSplitAside, MapSplitMain } from "@/components/Inclusion/MapSplit/MapSplit";
import GeoMap from "@/components/Inclusion/GeoMap/GeoMap";
import { notify } from "@/components/UI/NotificationToast/notify";
import LocationPage from "./LocationPage";
import type { LocationRecord } from "./Location.types";
import "./Locations.css";

const base = "/inclusive-radar/locations";

export default function InclusionLocationsShowPage({
    location,
    display,
    can,
}: {
    location: LocationRecord;
    display: { descriptionHtml: string };
    can: { edit: boolean; destroy: boolean };
}): React.JSX.Element {
    const [deleteOpen, setDeleteOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);
    const [failure, setFailure] = useState("");

    useEffect(() => {
        if (failure) notify({ tone: "error", title: "Não foi possível concluir", text: failure });
    }, [failure]);

    function remove(): void {
        if (deleting) return;
        setFailure("");
        setDeleting(true);
        router.delete(`${base}/${location.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir o ponto de referência. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <LocationPage
            title="Detalhes do Ponto de Referência"
            subtitle={`Visualize as informações cadastradas e a posição no mapa: ${location.name}`}
            location={location}
        >
            <CardBox aria-busy={deleting}>
                <MapSplit wideAside>
                    <MapSplitMain>
                        <FormSection
                            id="location-show-information"
                            title="Identificação"
                            variant="inline"
                            iconClass="fa fa-map-marker"
                        >
                            <dl className="locationShow__grid">
                                <DetailField label="Instituição Base">
                                    {location.institution?.name ?? "Não informada"}
                                </DetailField>
                                <DetailField label="Nome do Local">{location.name}</DetailField>
                                <DetailField label="Tipo de Local">
                                    {location.type || "Não informado"}
                                </DetailField>
                                <DetailField label="Descrição/Observações" wide>
                                    <RichTextContent html={display.descriptionHtml} />
                                </DetailField>
                            </dl>
                        </FormSection>
                        <FormSection
                            id="location-show-map-section"
                            title="Configuração"
                            variant="inline"
                            iconClass="fa fa-globe"
                        >
                            <dl className="locationShow__grid">
                                <DetailField label="Ativo no Sistema">
                                    <TableLabel tone={location.is_active ? "success" : "inverse"}>
                                        {location.is_active ? "Sim" : "Não"}
                                    </TableLabel>
                                </DetailField>
                                <DetailField label="Latitude">
                                    {Number(location.latitude).toFixed(8)}
                                </DetailField>
                                <DetailField label="Longitude">
                                    {Number(location.longitude).toFixed(8)}
                                </DetailField>
                            </dl>
                        </FormSection>
                    </MapSplitMain>
                    <MapSplitAside>
                        <FormSection
                            id="location-show-map"
                            title="Localização no Mapa"
                            variant="inline"
                            iconClass="fa fa-map-marker"
                        >
                            <GeoMap
                                title="Localização do Ponto"
                                value={{ latitude: Number(location.latitude), longitude: Number(location.longitude) }}
                                zoom={location.institution?.default_zoom ?? 16}
                                reference={location.institution ? {
                                    id: location.institution.id,
                                    name: location.institution.name,
                                    latitude: Number(location.institution.latitude),
                                    longitude: Number(location.institution.longitude),
                                    default_zoom: location.institution.default_zoom,
                                } : null}
                                markerLabel={location.name}
                            />
                        </FormSection>
                    </MapSplitAside>
                </MapSplit>
            </CardBox>
            <FormActions>
                <Button href={base} tone="cancel" label="Voltar" disabled={deleting} />
                {can.edit ? (
                    <Button
                        href={`${base}/${location.id}/edit`}
                        tone="warning"
                        leftIconClass="fa fa-pencil"
                        label="Editar"
                        disabled={deleting}
                    />
                ) : null}
                {can.destroy ? (
                    <Button
                        tone="dark-red"
                        leftIconClass="fa fa-trash-o"
                        label="Excluir"
                        disabled={deleting}
                        onClick={() => setDeleteOpen(true)}
                    />
                ) : null}
            </FormActions>
            <ConfirmActionModal
                open={deleteOpen}
                title="Excluir Ponto de Referência"
                message={<>O ponto <strong>{location.name}</strong> será excluído permanentemente.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo ponto..."
                confirmTone="dark-red"
                onClose={() => { if (!deleting) { setDeleteOpen(false); setFailure(""); } }}
                onConfirm={remove}
            />
        </LocationPage>
    );
}
