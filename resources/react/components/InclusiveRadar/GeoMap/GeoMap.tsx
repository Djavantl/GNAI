import { useEffect, useRef, useState } from "react";
import { loadExternalScript } from "@/utils/loadExternalScript";
import "/public/assets/react/plugins/leaflet/leaflet.css";
import "./GeoMap.css";

export type GeoPoint = {
    latitude: number;
    longitude: number;
};

export type GeoMapMarker = GeoPoint & {
    id: number;
    label: string;
};

export type GeoMapReference = GeoPoint & {
    id: number;
    name: string;
    default_zoom?: number;
    locations?: GeoMapMarker[];
};

type LeafletLayer = {
    addTo(map: LeafletMap | LeafletLayerGroup): LeafletLayer;
    bindTooltip(text: string, options?: Record<string, unknown>): LeafletLayer;
    setLatLng(point: [number, number]): void;
    on(event: string, handler: (event: LeafletEvent) => void): void;
    dragging?: { enable(): void; disable(): void };
};

type LeafletLayerGroup = {
    addTo(map: LeafletMap): LeafletLayerGroup;
    addLayer(layer: LeafletLayer): void;
    clearLayers(): void;
};

type LeafletMap = {
    remove(): void;
    invalidateSize(): void;
    flyTo(point: [number, number], zoom?: number, options?: Record<string, unknown>): void;
    setView(point: [number, number], zoom?: number): void;
    getZoom(): number;
    addLayer(layer: LeafletLayerGroup): void;
    removeLayer(layer: LeafletLayerGroup): void;
    on(event: string, handler: (event: LeafletEvent) => void): void;
};

type LeafletEvent = {
    latlng?: { lat: number; lng: number };
    target?: { getLatLng(): { lat: number; lng: number } };
};

type LeafletRuntime = {
    map(element: HTMLElement, options: Record<string, unknown>): LeafletMap;
    tileLayer(url: string, options: Record<string, unknown>): LeafletLayer;
    marker(point: [number, number], options?: Record<string, unknown>): LeafletLayer;
    circleMarker(point: [number, number], options?: Record<string, unknown>): LeafletLayer;
    layerGroup(): LeafletLayerGroup;
    control: {
        layers(layers: Record<string, LeafletLayer>): {
            addTo(map: LeafletMap): void;
        };
    };
};

type GeoMapProps = {
    title: string;
    value: GeoPoint;
    zoom: number;
    editable?: boolean;
    interactionEnabled?: boolean;
    height?: number;
    markerLabel: string;
    reference?: GeoMapReference | null;
    excludedLocationId?: number;
    onChange?: (point: GeoPoint) => void;
    onBlockedInteraction?: () => void;
    onZoomChange?: (zoom: number) => void;
    showReferenceMarkers?: boolean;
};

const defaultCenter: GeoPoint = { latitude: -14.235, longitude: -51.9253 };

function validPoint(point: GeoPoint): boolean {
    return Number.isFinite(point.latitude)
        && Number.isFinite(point.longitude)
        && Math.abs(point.latitude) <= 90
        && Math.abs(point.longitude) <= 180;
}

function escapeTooltipText(value: string): string {
    const replacements: Record<string, string> = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
    };
    return value.replace(/[&<>"']/g, (character) => replacements[character] ?? character);
}

export default function GeoMap({
    title,
    value,
    zoom,
    editable = false,
    interactionEnabled = true,
    height = 550,
    markerLabel,
    reference,
    excludedLocationId,
    onChange,
    onBlockedInteraction,
    onZoomChange,
    showReferenceMarkers = true,
}: GeoMapProps): React.JSX.Element {
    const containerRef = useRef<HTMLDivElement>(null);
    const mapRef = useRef<LeafletMap | null>(null);
    const selectedMarkerRef = useRef<LeafletLayer | null>(null);
    const referenceMarkersRef = useRef<LeafletLayerGroup | null>(null);
    const previousReferenceIdRef = useRef(reference?.id);
    const onChangeRef = useRef(onChange);
    const onBlockedInteractionRef = useRef(onBlockedInteraction);
    const interactionEnabledRef = useRef(interactionEnabled);
    const onZoomChangeRef = useRef(onZoomChange);
    const [loadError, setLoadError] = useState(false);
    const [ready, setReady] = useState(false);
    onChangeRef.current = onChange;
    onBlockedInteractionRef.current = onBlockedInteraction;
    interactionEnabledRef.current = interactionEnabled;
    onZoomChangeRef.current = onZoomChange;

    useEffect(() => {
        let disposed = false;
        let resizeObserver: ResizeObserver | undefined;
        let resizeTimer: number | undefined;
        void loadExternalScript("/assets/react/plugins/leaflet/leaflet.js")
            .then(() => {
                if (disposed || !containerRef.current) return;
                const leaflet = (window as Window & { L?: LeafletRuntime }).L;
                if (!leaflet) throw new Error("Leaflet não foi carregado.");

                const point = validPoint(value) ? value : defaultCenter;
                const map = leaflet.map(containerRef.current, {
                    center: [point.latitude, point.longitude],
                    zoom,
                    scrollWheelZoom: true,
                });
                const streets = leaflet.tileLayer(
                    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
                    { attribution: "© OpenStreetMap", maxZoom: 19 },
                ).addTo(map);
                const satellite = leaflet.tileLayer(
                    "https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}",
                    { attribution: "© Google Maps", maxZoom: 21 },
                );
                leaflet.control.layers({ "Mapa de ruas (OSM)": streets, "Satélite (Google)": satellite }).addTo(map);
                const referenceMarkers = leaflet.layerGroup().addTo(map);
                const selectedMarker = leaflet.marker(
                    [point.latitude, point.longitude],
                    { draggable: editable && interactionEnabled },
                ).addTo(map).bindTooltip(escapeTooltipText(markerLabel), {
                    permanent: true,
                    direction: "top",
                    offset: [-15, -30],
                    className: "geoMap__selectedTooltip",
                });

                if (editable) {
                    map.on("click", (event) => {
                        if (!interactionEnabledRef.current) {
                            onBlockedInteractionRef.current?.();
                            return;
                        }
                        const coordinates = event.latlng;
                        if (coordinates) onChangeRef.current?.({ latitude: coordinates.lat, longitude: coordinates.lng });
                    });
                    selectedMarker.on("dragend", (event) => {
                        if (!interactionEnabledRef.current) return;
                        const coordinates = event.target?.getLatLng();
                        if (coordinates) onChangeRef.current?.({ latitude: coordinates.lat, longitude: coordinates.lng });
                    });
                }
                map.on("zoomend", (event) => {
                    const target = event.target as unknown as { getZoom(): number } | undefined;
                    if (target) onZoomChangeRef.current?.(target.getZoom());
                });

                mapRef.current = map;
                selectedMarkerRef.current = selectedMarker;
                referenceMarkersRef.current = referenceMarkers;
                setReady(true);
                resizeObserver = new ResizeObserver(() => map.invalidateSize());
                resizeObserver.observe(containerRef.current);
                resizeTimer = window.setTimeout(() => map.invalidateSize(), 100);
            })
            .catch(() => {
                if (!disposed) setLoadError(true);
            });

        return () => {
            disposed = true;
            resizeObserver?.disconnect();
            if (resizeTimer !== undefined) window.clearTimeout(resizeTimer);
            mapRef.current?.remove();
            mapRef.current = null;
            selectedMarkerRef.current = null;
            referenceMarkersRef.current = null;
        };
        // The map is initialized once; controlled values are synchronized below.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    useEffect(() => {
        if (!editable || !selectedMarkerRef.current?.dragging) return;
        if (interactionEnabled) selectedMarkerRef.current.dragging.enable();
        else selectedMarkerRef.current.dragging.disable();
    }, [editable, interactionEnabled, ready]);

    useEffect(() => {
        if (!validPoint(value)) return;
        selectedMarkerRef.current?.setLatLng([value.latitude, value.longitude]);
    }, [value.latitude, value.longitude, ready]);

    useEffect(() => {
        if (zoom > 0 && mapRef.current && mapRef.current.getZoom() !== zoom) {
            mapRef.current.setView([value.latitude, value.longitude], zoom);
        }
    }, [value.latitude, value.longitude, zoom, ready]);

    useEffect(() => {
        const leaflet = (window as Window & { L?: LeafletRuntime }).L;
        const map = mapRef.current;
        const group = referenceMarkersRef.current;
        if (!leaflet || !map || !group) return;
        group.clearLayers();

        if (reference && validPoint(reference)) {
            leaflet.circleMarker(
                [reference.latitude, reference.longitude],
                { radius: 9, color: "#c0392b", fillColor: "#e74c3c", fillOpacity: 0.9 },
            ).addTo(group).bindTooltip(escapeTooltipText(`Sede: ${reference.name}`), { className: "geoMap__institutionTooltip" });
            for (const location of reference.locations ?? []) {
                if (location.id === excludedLocationId || !validPoint(location)) continue;
                leaflet.circleMarker(
                    [location.latitude, location.longitude],
                    { radius: 6, color: "#68737d", fillColor: "#98a6ad", fillOpacity: 0.9 },
                ).addTo(group).bindTooltip(escapeTooltipText(location.label), { className: "geoMap__locationTooltip" });
            }
        }
    }, [reference, excludedLocationId, loadError, ready]);

    useEffect(() => {
        const map = mapRef.current;
        const markers = referenceMarkersRef.current;
        if (!map || !markers) return;
        if (showReferenceMarkers) map.addLayer(markers);
        else map.removeLayer(markers);
    }, [showReferenceMarkers, ready]);

    useEffect(() => {
        if (!reference || !validPoint(reference) || !mapRef.current) return;
        if (previousReferenceIdRef.current === reference.id) return;
        previousReferenceIdRef.current = reference.id;
        mapRef.current.flyTo(
            [reference.latitude, reference.longitude],
            reference.default_zoom ?? zoom,
            { animate: true, duration: 0.8 },
        );
    }, [reference?.id, reference?.name, reference?.latitude, reference?.longitude, reference?.default_zoom, ready]);

    return (
        <div className="geoMap">
            <div className="geoMap__heading">
                <h3>{title}</h3>
                {editable ? (
                    <span>
                        {interactionEnabled
                            ? "Clique no mapa ou arraste o marcador para definir o ponto."
                            : "Selecione uma instituição base para posicionar o local."}
                    </span>
                ) : null}
            </div>
            <div
                ref={containerRef}
                className="geoMap__canvas"
                style={{ height }}
                role="region"
                aria-label={title}
                aria-busy={!loadError && !mapRef.current}
            />
            {loadError ? (
                <p className="geoMap__message" role="status">
                    Não foi possível carregar o mapa. Confira sua conexão e tente novamente.
                </p>
            ) : null}
            <dl className="geoMap__coordinates">
                <div><dt>Latitude</dt><dd>{validPoint(value) ? value.latitude.toFixed(8) : "—"}</dd></div>
                <div><dt>Longitude</dt><dd>{validPoint(value) ? value.longitude.toFixed(8) : "—"}</dd></div>
            </dl>
        </div>
    );
}
