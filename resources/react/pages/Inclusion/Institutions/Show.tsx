import { router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import ConfirmActionModal from "@/components/UI/ConfirmActionModal/ConfirmActionModal";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormSection from "@/components/Forms/FormSection/FormSection";
import DetailField from "@/components/UI/DetailField/DetailField";
import TableLabel from "@/components/UI/TableLabel/TableLabel";
import MapSplit, { MapSplitAside, MapSplitMain } from "@/components/Inclusion/MapSplit/MapSplit";
import GeoMap from "@/components/Inclusion/GeoMap/GeoMap";
import { notify } from "@/components/UI/NotificationToast/notify";
import InstitutionPage from "./InstitutionPage";
import type { InstitutionRecord } from "./Institution.types";
import "./Institutions.css";

const base = "/inclusive-radar/institutions";

export default function InclusionInstitutionsShowPage({
    institution,
    can,
}: {
    institution: InstitutionRecord;
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
        router.delete(`${base}/${institution.id}`, {
            preserveScroll: true,
            onNetworkError: () => {
                setFailure("Falha de conexão. Confira sua conexão e tente novamente.");
                return false;
            },
            onHttpException: () => {
                setFailure("Não foi possível excluir a instituição. Tente novamente.");
                return false;
            },
            onSuccess: () => setDeleteOpen(false),
            onFinish: () => setDeleting(false),
        });
    }

    return (
        <InstitutionPage
            title="Detalhes da Instituição"
            subtitle={`Visualize as informações cadastrais e a localização no mapa da instituição: ${institution.name}`}
            institution={institution}
        >
            <CardBox aria-busy={deleting}>
                <MapSplit wideAside>
                    <MapSplitMain>
                        <FormSection
                            id="institution-show-information"
                            title="Identificação"
                            variant="inline"
                            iconClass="fa fa-university"
                        >
                            <dl className="institutionShow__grid">
                                <DetailField label="Nome da Instituição">{institution.name}</DetailField>
                                <DetailField label="Sigla / Nome Curto">{institution.short_name || "Não informada"}</DetailField>
                                <DetailField label="Cidade">{institution.city}</DetailField>
                                <DetailField label="Estado">{institution.state}</DetailField>
                                <DetailField label="Bairro / Distrito">{institution.district || "Não informado"}</DetailField>
                                <DetailField label="Rua / Logradouro">{institution.address || "Não informado"}</DetailField>
                            </dl>
                        </FormSection>
                        <FormSection
                            id="institution-show-settings"
                            title="Configuração"
                            variant="inline"
                            iconClass="fa fa-globe"
                        >
                            <dl className="institutionShow__grid">
                                <DetailField label="Instituição Ativa">
                                    <TableLabel tone={institution.is_active ? "success" : "inverse"}>
                                        {institution.is_active ? "Sim" : "Não"}
                                    </TableLabel>
                                </DetailField>
                                <DetailField label="Zoom Padrão">{institution.default_zoom}</DetailField>
                                <DetailField label="Latitude Sede">{Number(institution.latitude).toFixed(8)}</DetailField>
                                <DetailField label="Longitude Sede">{Number(institution.longitude).toFixed(8)}</DetailField>
                            </dl>
                        </FormSection>
                    </MapSplitMain>
                    <MapSplitAside>
                        <FormSection
                            id="institution-show-map"
                            title="Localização no Mapa"
                            variant="inline"
                            iconClass="fa fa-map-marker"
                        >
                            <GeoMap
                                title="Localização da Instituição"
                                value={{ latitude: Number(institution.latitude), longitude: Number(institution.longitude) }}
                                zoom={institution.default_zoom}
                                markerLabel={institution.name}
                            />
                        </FormSection>
                    </MapSplitAside>
                </MapSplit>
            </CardBox>
            <FormActions>
                <Button href={base} tone="cancel" label="Voltar" disabled={deleting} />
                {can.edit ? (
                    <Button
                        href={`${base}/${institution.id}/edit`}
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
                title="Excluir Instituição"
                message={<>A instituição <strong>{institution.name}</strong> e seus dados relacionados serão excluídos permanentemente.</>}
                processing={deleting}
                confirmLabel="Confirmar exclusão"
                processingLabel="Excluindo instituição..."
                confirmTone="dark-red"
                onClose={() => { if (!deleting) { setDeleteOpen(false); setFailure(""); } }}
                onConfirm={remove}
            />
        </InstitutionPage>
    );
}
