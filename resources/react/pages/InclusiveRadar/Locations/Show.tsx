import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import GeoMap from "@/components/InclusiveRadar/GeoMap/GeoMap";
import { notify } from "@/components/UI/NotificationToast/notify";
import LocationPage from "./LocationPage";
import type { LocationRecord } from "./Location.types";
import "./Locations.css";

const base = "/inclusive-radar/locations";

export default function InclusiveRadarLocationsShowPage({
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
                <FormSection
                    id="location-show-information"
                    title="Vínculo e Identificação"
                    variant="inline"
                    iconClass="fa fa-map-marker"
                >
                    <dl className="locationShow__grid">
                        <DetailField label="Instituição Base" wide>
                            {location.institution?.name ?? "— Não informada —"}
                        </DetailField>
                        <DetailField label="Nome do Local" wide>{location.name}</DetailField>
                        <DetailField label="Tipo de Local">
                            {location.type || "— Não informado —"}
                        </DetailField>
                        <DetailField label="Ativo no Sistema">
                            <TableLabel tone={location.is_active ? "success" : "inverse"}>
                                {location.is_active ? "Sim" : "Não"}
                            </TableLabel>
                        </DetailField>
                        <DetailField label="Descrição/Observações" wide>
                            <span className="locationShow__description">
                                <span dangerouslySetInnerHTML={{ __html: display.descriptionHtml }} />
                            </span>
                        </DetailField>
                        <DetailField label="Latitude">
                            {Number(location.latitude).toFixed(8)}
                        </DetailField>
                        <DetailField label="Longitude">
                            {Number(location.longitude).toFixed(8)}
                        </DetailField>
                    </dl>
                </FormSection>
                <FormSection
                    id="location-show-map-section"
                    title="Localização no Mapa"
                    variant="inline"
                    iconClass="fa fa-globe"
                >
                    <GeoMap
                        title="Localização do Ponto"
                        value={{ latitude: Number(location.latitude), longitude: Number(location.longitude) }}
                        zoom={location.institution?.default_zoom ?? 16}
                        markerLabel={location.name}
                        reference={null}
                    />
                </FormSection>
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
