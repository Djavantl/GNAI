import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide, FormStack } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import SelectField from "@/components/Forms/SelectField/SelectField";
import TextArea from "@/components/Forms/TextArea/TextArea";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import GeoMap, { type GeoPoint } from "@/components/InclusiveRadar/GeoMap/GeoMap";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import { notify } from "@/components/UI/NotificationToast/notify";
import LocationPage from "./LocationPage";
import type { InstitutionMapData, InstitutionOption, LocationRecord } from "./Location.types";
import "./Locations.css";

const base = "/inclusive-radar/locations";
const defaultPoint: GeoPoint = { latitude: -14.235, longitude: -51.9253 };

function parseCoordinate(value: string, fallback: number): number {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

export default function LocationForm({
    location,
    institutions,
    institutionsData,
    selectedInstitutionId,
    canSubmit,
}: {
    location?: LocationRecord;
    institutions: InstitutionOption[];
    institutionsData: InstitutionMapData[];
    selectedInstitutionId: number | null;
    canSubmit: boolean;
}): React.JSX.Element {
    const editing = Boolean(location);
    const initialInstitutionId = String(selectedInstitutionId ?? location?.institution_id ?? "");
    const initialInstitution = institutionsData.find((item) => String(item.id) === initialInstitutionId);
    const initialPoint = location
        ? { latitude: location.latitude, longitude: location.longitude }
        : initialInstitution
          ? { latitude: initialInstitution.latitude, longitude: initialInstitution.longitude }
          : defaultPoint;
    const form = useForm({
        institution_id: initialInstitutionId,
        name: location?.name ?? "",
        type: location?.type ?? "",
        description: location?.description ?? "",
        latitude: String(initialPoint.latitude),
        longitude: String(initialPoint.longitude),
        is_active: location?.is_active ?? true,
    });
    const errors = form.errors as Record<string, string | undefined>;
    const selectedInstitution = institutionsData.find((item) => item.id === Number(form.data.institution_id)) ?? null;
    const mapPoint = {
        latitude: parseCoordinate(form.data.latitude, defaultPoint.latitude),
        longitude: parseCoordinate(form.data.longitude, defaultPoint.longitude),
    };

    function updatePoint(point: GeoPoint): void {
        if (!form.data.institution_id) {
            notify({ tone: "warning", title: "Selecione a instituição", text: "Escolha uma instituição base antes de marcar o local no mapa." });
            return;
        }
        form.setData((current) => ({
            ...current,
            latitude: point.latitude.toFixed(8),
            longitude: point.longitude.toFixed(8),
        }));
    }

    function submit(event: React.FormEvent<HTMLFormElement>): void {
        event.preventDefault();
        const options = {
            preserveScroll: true,
            onNetworkError: () => {
                notify({ tone: "error", title: "Falha de conexão", text: "Confira sua conexão e tente novamente." });
                return false;
            },
            onHttpException: () => {
                notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível salvar o ponto de referência. Tente novamente." });
                return false;
            },
        };
        if (location) form.put(`${base}/${location.id}`, options);
        else form.post(`${base}/store`, options);
    }

    return (
        <LocationPage
            title={editing ? "Editar Ponto de Referência" : "Cadastrar Ponto de Referência"}
            subtitle={editing
                ? `Atualize as informações de ${location?.name ?? ""}.`
                : "Vincule prédios ou áreas específicas a uma instituição base."}
            current={editing ? "Editar" : "Cadastrar"}
            location={location}
        >
            <FormLayout onSubmit={submit} aria-busy={form.processing}>
                <CardBox>
                    <FormSection
                        id="location-identification"
                        title="Vínculo e Identificação"
                        variant="inline"
                        iconClass="fa fa-map-marker"
                    >
                        <FormGrid columns={2}>
                            <FormField
                                id="location-institution"
                                label="Instituição Base"
                                requiredIndicator
                                error={errors.institution_id}
                            >
                                <SelectField
                                    id="location-institution"
                                    name="institution_id"
                                    label="Instituição Base"
                                    required
                                    value={form.data.institution_id}
                                    options={institutions}
                                    aria-invalid={!!errors.institution_id}
                                    aria-describedby={errors.institution_id ? "location-institution-error" : undefined}
                                    disabled={form.processing}
                                    onChange={(event) => {
                                        const institutionId = event.target.value;
                                        form.setData((current) => ({
                                            ...current,
                                            institution_id: institutionId,
                                            ...(institutionId ? {
                                                latitude: String(institutionsData.find((item) => item.id === Number(institutionId))?.latitude ?? defaultPoint.latitude),
                                                longitude: String(institutionsData.find((item) => item.id === Number(institutionId))?.longitude ?? defaultPoint.longitude),
                                            } : {}),
                                        }));
                                    }}
                                />
                            </FormField>
                            <FormField
                                id="location-name"
                                label="Nome do Local"
                                requiredIndicator
                                error={errors.name}
                            >
                                <TextInput
                                    id="location-name"
                                    name="name"
                                    required
                                    maxLength={255}
                                    placeholder="Ex.: Bloco Acadêmico II"
                                    value={form.data.name}
                                    invalid={!!errors.name}
                                    aria-describedby={errors.name ? "location-name-error" : undefined}
                                    disabled={form.processing}
                                    onChange={(event) => form.setData("name", event.target.value)}
                                />
                            </FormField>
                            <FormField id="location-type" label="Tipo de Local" error={errors.type}>
                                <TextInput
                                    id="location-type"
                                    name="type"
                                    maxLength={100}
                                    placeholder="Ex.: Pavilhão, Bloco, Laboratório..."
                                    value={form.data.type}
                                    invalid={!!errors.type}
                                    aria-describedby={errors.type ? "location-type-error" : undefined}
                                    disabled={form.processing}
                                    onChange={(event) => form.setData("type", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <FormField id="location-description" label="Descrição/Observações" error={errors.description}>
                                    <TextArea
                                        id="location-description"
                                        name="description"
                                        rows={3}
                                        maxLength={1000}
                                        placeholder="Detalhes adicionais sobre o local..."
                                        value={form.data.description}
                                        aria-invalid={!!errors.description}
                                        aria-describedby={errors.description ? "location-description-error" : undefined}
                                        disabled={form.processing}
                                        onChange={(event) => form.setData("description", event.target.value)}
                                    />
                                </FormField>
                            </FormGridWide>
                            <FormGridWide>
                                <FormStack>
                                    <ToggleControl
                                        id="location-active"
                                        name="is_active"
                                        size="small"
                                        label="Ponto Ativo no Sistema"
                                        labelAddon={<InfoTooltip text="Define se este local aparecerá no mapa público." />}
                                        checked={form.data.is_active}
                                        disabled={form.processing}
                                        onChange={(checked) => form.setData("is_active", checked)}
                                    />
                                </FormStack>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection
                        id="location-map-section"
                        title="Coordenadas e Localização no Mapa"
                        variant="inline"
                        iconClass="fa fa-globe"
                    >
                        <FormGrid columns={2}>
                            <FormField id="location-latitude" label="Latitude" requiredIndicator error={errors.latitude}>
                                <TextInput
                                    id="location-latitude"
                                    name="latitude"
                                    type="number"
                                    step="any"
                                    required
                                    placeholder="-14.2350"
                                    value={form.data.latitude}
                                    invalid={!!errors.latitude}
                                    aria-describedby={errors.latitude ? "location-latitude-error" : undefined}
                                    disabled={form.processing}
                                    onChange={(event) => form.setData("latitude", event.target.value)}
                                />
                            </FormField>
                            <FormField id="location-longitude" label="Longitude" requiredIndicator error={errors.longitude}>
                                <TextInput
                                    id="location-longitude"
                                    name="longitude"
                                    type="number"
                                    step="any"
                                    required
                                    placeholder="-51.9253"
                                    value={form.data.longitude}
                                    invalid={!!errors.longitude}
                                    aria-describedby={errors.longitude ? "location-longitude-error" : undefined}
                                    disabled={form.processing}
                                    onChange={(event) => form.setData("longitude", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <GeoMap
                                    title="Localização no Campus"
                                    value={mapPoint}
                                    zoom={editing ? 18 : selectedInstitution?.default_zoom ?? 16}
                                    editable
                                    interactionEnabled={Boolean(form.data.institution_id) && !form.processing}
                                    markerLabel="Localização selecionada"
                                    reference={selectedInstitution}
                                    excludedLocationId={location?.id}
                                    onChange={updatePoint}
                                    onBlockedInteraction={() => {
                                        if (!form.processing) {
                                            notify({ tone: "warning", title: "Selecione a instituição", text: "Escolha uma instituição base antes de marcar o local no mapa." });
                                        }
                                    }}
                                />
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                </CardBox>
                <FormActions
                    status={form.processing ? (editing ? "Salvando alterações…" : "Cadastrando ponto…") : undefined}
                >
                    <Button
                        href={location ? `${base}/${location.id}` : base}
                        tone="cancel"
                        label="Cancelar"
                        disabled={form.processing}
                    />
                    {canSubmit ? (
                        <Button
                            type="submit"
                            tone="save"
                            label={editing ? "Salvar" : "Cadastrar"}
                            disabled={form.processing}
                        />
                    ) : null}
                </FormActions>
            </FormLayout>
        </LocationPage>
    );
}
