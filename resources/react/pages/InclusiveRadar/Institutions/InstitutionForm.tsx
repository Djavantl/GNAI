import { useEffect, useRef } from "react";
import { useForm } from "@inertiajs/react";
import Button from "@/components/UI/Button/Button";
import CardBox from "@/components/UI/CardBox/CardBox";
import FormActions from "@/components/Forms/FormActions/FormActions";
import FormField from "@/components/Forms/FormField/FormField";
import FormGrid, { FormGridWide, FormStack } from "@/components/Forms/FormGrid/FormGrid";
import FormLayout from "@/components/Forms/FormLayout/FormLayout";
import FormSection from "@/components/Forms/FormSection/FormSection";
import TextInput from "@/components/Forms/TextInput/TextInput";
import ToggleControl from "@/components/Forms/ToggleControl/ToggleControl";
import GeoMap, { type GeoPoint } from "@/components/InclusiveRadar/GeoMap/GeoMap";
import InfoTooltip from "@/components/UI/InfoTooltip/InfoTooltip";
import { notify } from "@/components/UI/NotificationToast/notify";
import InstitutionPage from "./InstitutionPage";
import type { InstitutionRecord } from "./Institution.types";
import "./Institutions.css";

const base = "/inclusive-radar/institutions";
const defaultPoint: GeoPoint = { latitude: -14.235, longitude: -51.9253 };

function parseCoordinate(value: string, fallback: number): number {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

export default function InstitutionForm({ institution, canSubmit }: { institution?: InstitutionRecord; canSubmit: boolean }): React.JSX.Element {
    const editing = Boolean(institution);
    const reverseRequest = useRef<AbortController | null>(null);
    const initialPoint = institution
        ? { latitude: Number(institution.latitude), longitude: Number(institution.longitude) }
        : defaultPoint;
    const form = useForm({
        name: institution?.name ?? "",
        short_name: institution?.short_name ?? "",
        city: institution?.city ?? "",
        state: institution?.state ?? "",
        district: institution?.district ?? "",
        address: institution?.address ?? "",
        is_active: institution?.is_active ?? true,
        default_zoom: String(institution?.default_zoom ?? 16),
        latitude: String(initialPoint.latitude),
        longitude: String(initialPoint.longitude),
    });
    const errors = form.errors as Record<string, string | undefined>;
    const addressKey = [form.data.address, form.data.district, form.data.city, form.data.state].join("|");
    const previousAddressRef = useRef(addressKey);
    const mapPoint = {
        latitude: parseCoordinate(form.data.latitude, defaultPoint.latitude),
        longitude: parseCoordinate(form.data.longitude, defaultPoint.longitude),
    };
    const zoom = Number.parseInt(form.data.default_zoom, 10) || 16;

    useEffect(() => {
        if (previousAddressRef.current === addressKey) return;
        previousAddressRef.current = addressKey;
        const pieces = [form.data.address, form.data.district, form.data.city, form.data.state]
            .map((part) => part.trim())
            .filter(Boolean);
        const city = form.data.city.trim();
        const state = form.data.state.trim();
        const district = form.data.district.trim();
        const address = form.data.address.trim();
        const enoughAddress = city.length >= 2 || address.length >= 3
            || district.length >= 3 || state.length >= 2;
        if (!enoughAddress) return;
        pieces.push("Brasil");
        const controller = new AbortController();
        reverseRequest.current?.abort();
        const timer = window.setTimeout(() => {
            const timeout = window.setTimeout(() => controller.abort(), 10000);
            void fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(pieces.join(", "))}&addressdetails=1&limit=1&accept-language=pt-BR`,
                { signal: controller.signal },
            )
                .then((response) => response.ok ? response.json() : [])
                .then((results: Array<{ lat: string; lon: string; address?: Record<string, string> }>) => {
                    if (!results.length) return;
                    if (city) {
                        const foundCity = results[0].address?.city
                            || results[0].address?.town
                            || results[0].address?.municipality
                            || results[0].address?.village
                            || "";
                        const normalizedFoundCity = foundCity.toLowerCase();
                        const normalizedSearchCity = city.toLowerCase();
                        if (normalizedFoundCity
                            && !normalizedFoundCity.includes(normalizedSearchCity)
                            && !normalizedSearchCity.includes(normalizedFoundCity)) return;
                    }
                    let nextZoom = 12;
                    if (address) nextZoom = 18;
                    else if (district) nextZoom = 16;
                    else if (city) nextZoom = 14;
                    else if (state) nextZoom = 6;
                    form.setData((current) => ({
                        ...current,
                        latitude: Number(results[0].lat).toFixed(8),
                        longitude: Number(results[0].lon).toFixed(8),
                        default_zoom: String(nextZoom),
                    }));
                })
                .catch(() => undefined)
                .finally(() => window.clearTimeout(timeout));
        }, 500);
        reverseRequest.current = controller;
        return () => {
            window.clearTimeout(timer);
            controller.abort();
        };
    }, [addressKey]);

    useEffect(() => () => reverseRequest.current?.abort(), []);

    function updateFromMap(point: GeoPoint): void {
        form.setData((current) => ({
            ...current,
            latitude: point.latitude.toFixed(8),
            longitude: point.longitude.toFixed(8),
        }));
        const controller = new AbortController();
        reverseRequest.current?.abort();
        reverseRequest.current = controller;
        void fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${point.latitude}&lon=${point.longitude}&addressdetails=1&zoom=18&accept-language=pt-BR`,
            { signal: controller.signal },
        )
            .then((response) => response.ok ? response.json() : null)
            .then((result: { address?: Record<string, string> } | null) => {
                const address = result?.address;
                if (!address) return;
                const nextAddress = address.road || address.pedestrian || address.path || form.data.address;
                const nextDistrict = address.hamlet || address.suburb || address.neighbourhood || address.city_district || address.village || form.data.district;
                const nextCity = address.city || address.town || address.municipality || address.village || form.data.city;
                const nextState = address.state || form.data.state;
                previousAddressRef.current = [nextAddress, nextDistrict, nextCity, nextState].join("|");
                form.setData((current) => ({
                    ...current,
                    city: nextCity,
                    district: nextDistrict,
                    state: nextState,
                    address: nextAddress,
                }));
            })
            .catch(() => undefined);
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
                notify({ tone: "error", title: "Não foi possível concluir", text: "Não foi possível salvar a instituição. Tente novamente." });
                return false;
            },
        };
        if (institution) form.put(`${base}/${institution.id}`, options);
        else form.post(`${base}/store`, options);
    }

    return (
        <InstitutionPage
            title={editing ? "Editar Instituição Base" : "Nova Instituição Base"}
            subtitle={editing
                ? "Atualize a localização central ou as informações da sede para o mapa de barreiras."
                : "Defina o ponto central e as informações da sede para o mapa de barreiras."}
            current={editing ? "Editar" : "Cadastrar"}
            institution={institution}
        >
            <FormLayout onSubmit={submit} aria-busy={form.processing}>
                <CardBox>
                    <FormSection id="institution-information" title="Informações Gerais" variant="inline" iconClass="fa fa-university">
                        <FormGrid columns={2}>
                            <FormField id="institution-name" label="Nome da Instituição" requiredIndicator error={errors.name}>
                                <TextInput
                                    id="institution-name" name="name" required maxLength={255}
                                    placeholder="Ex.: IFBA - Campus Guanambi" value={form.data.name}
                                    invalid={!!errors.name} aria-describedby={errors.name ? "institution-name-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("name", event.target.value)}
                                />
                            </FormField>
                            <FormField id="institution-short-name" label="Sigla / Nome Curto" error={errors.short_name}>
                                <TextInput
                                    id="institution-short-name" name="short_name" maxLength={100}
                                    placeholder="Ex.: IFBA-GBI" value={form.data.short_name}
                                    invalid={!!errors.short_name} aria-describedby={errors.short_name ? "institution-short-name-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("short_name", event.target.value)}
                                />
                            </FormField>
                            <FormField id="institution-city" label="Cidade" requiredIndicator error={errors.city}>
                                <TextInput
                                    id="institution-city" name="city" required maxLength={255} value={form.data.city}
                                    invalid={!!errors.city} aria-describedby={errors.city ? "institution-city-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("city", event.target.value)}
                                />
                            </FormField>
                            <FormField id="institution-state" label="Estado" requiredIndicator error={errors.state}>
                                <TextInput
                                    id="institution-state" name="state" required maxLength={255} value={form.data.state}
                                    invalid={!!errors.state} aria-describedby={errors.state ? "institution-state-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("state", event.target.value)}
                                />
                            </FormField>
                            <FormField id="institution-district" label="Bairro / Distrito" error={errors.district}>
                                <TextInput
                                    id="institution-district" name="district" maxLength={255} value={form.data.district}
                                    invalid={!!errors.district} aria-describedby={errors.district ? "institution-district-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("district", event.target.value)}
                                />
                            </FormField>
                            <FormField id="institution-address" label="Rua / Logradouro" error={errors.address}>
                                <TextInput
                                    id="institution-address" name="address" maxLength={255} value={form.data.address}
                                    invalid={!!errors.address} aria-describedby={errors.address ? "institution-address-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("address", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <FormStack>
                                    <ToggleControl
                                        id="institution-active" name="is_active" size="small"
                                        label="Instituição Ativa"
                                        labelAddon={<InfoTooltip text="Define se esta sede estará disponível para o mapeamento público." />}
                                        checked={form.data.is_active} disabled={form.processing}
                                        onChange={(checked) => form.setData("is_active", checked)}
                                    />
                                </FormStack>
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                    <FormSection id="institution-map-settings" title="Configurações e Localização no Mapa" variant="inline" iconClass="fa fa-globe">
                        <FormGrid columns={2}>
                            <FormGridWide>
                                <FormField id="institution-default-zoom" label="Zoom Padrão" error={errors.default_zoom}>
                                    <div className="institutionForm__rangeField">
                                        <input
                                            id="institution-default-zoom" name="default_zoom" type="range" min="1" max="20"
                                            value={zoom} aria-describedby={errors.default_zoom ? "institution-default-zoom-error" : undefined}
                                            disabled={form.processing}
                                            onChange={(event) => form.setData("default_zoom", event.target.value)}
                                        />
                                        <output htmlFor="institution-default-zoom">{zoom}</output>
                                    </div>
                                </FormField>
                            </FormGridWide>
                            <FormField id="institution-latitude" label="Latitude Sede" requiredIndicator error={errors.latitude}>
                                <TextInput
                                    id="institution-latitude" name="latitude" type="number" step="any" required
                                    placeholder="-14.2350" value={form.data.latitude} invalid={!!errors.latitude}
                                    aria-describedby={errors.latitude ? "institution-latitude-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("latitude", event.target.value)}
                                />
                            </FormField>
                            <FormField id="institution-longitude" label="Longitude Sede" requiredIndicator error={errors.longitude}>
                                <TextInput
                                    id="institution-longitude" name="longitude" type="number" step="any" required
                                    placeholder="-51.9253" value={form.data.longitude} invalid={!!errors.longitude}
                                    aria-describedby={errors.longitude ? "institution-longitude-error" : undefined}
                                    disabled={form.processing} onChange={(event) => form.setData("longitude", event.target.value)}
                                />
                            </FormField>
                            <FormGridWide>
                                <GeoMap
                                    title={editing ? "Ajuste a Sede no Mapa" : "Localize a Sede no Mapa"}
                                    value={mapPoint} zoom={zoom} editable
                                    interactionEnabled={!form.processing}
                                    markerLabel="Sede da instituição"
                                    onChange={updateFromMap}
                                    onZoomChange={(nextZoom) => {
                                        if (!form.processing) form.setData("default_zoom", String(nextZoom));
                                    }}
                                />
                            </FormGridWide>
                        </FormGrid>
                    </FormSection>
                </CardBox>
                <FormActions status={form.processing ? (editing ? "Salvando alterações…" : "Cadastrando instituição…") : undefined}>
                    <Button href={institution ? `${base}/${institution.id}` : base} tone="cancel" label="Cancelar" disabled={form.processing} />
                    {canSubmit ? (
                        <Button type="submit" tone="save" label={editing ? "Salvar" : "Cadastrar"} disabled={form.processing} />
                    ) : null}
                </FormActions>
            </FormLayout>
        </InstitutionPage>
    );
}
