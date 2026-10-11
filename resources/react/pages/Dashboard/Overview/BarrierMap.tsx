import { createPortal } from 'react-dom';
import BarrierPopup from './BarrierPopup';
import { useEffect, useMemo, useRef, useState } from 'react';
import ChoiceControl from '@/components/Forms/ChoiceControl/ChoiceControl';
import CardBox from '@/components/UI/CardBox/CardBox';
import { loadExternalScript } from '@/utils/loadExternalScript';
import type { Barrier } from './Overview.types';
import { colors } from './MetricsChart';
import '/public/assets/react/plugins/leaflet/leaflet.css';

const statuses = [
  ['identified', 'Identificada'], ['under_analysis', 'Em análise'],
  ['in_progress', 'Em tratamento'], ['resolved', 'Resolvida'], ['not_applicable', 'Não aplicável'],
];
interface MapInstance {
  remove(): void; invalidateSize(): void;
  fitBounds(points: number[][], options: object): void;
}
interface MapLayer { addTo(map: MapInstance): MapLayer; bindPopup(content: HTMLElement, options?: object): MapLayer }
interface MarkerGroup { addTo(map: MapInstance): MarkerGroup; addLayer(layer: MapLayer): void; clearLayers(): void }
interface Leaflet {
  layerGroup(): MarkerGroup;
  map(element: HTMLElement, options: object): MapInstance;
  tileLayer(url: string, options: object): MapLayer;
  circleMarker(point: number[], options: object): MapLayer;
  control: { layers(layers: Record<string, MapLayer>): { addTo(map: MapInstance): void } };
}
export default function BarrierMap({ barriers }: { barriers: Barrier[] }): React.JSX.Element {
  const ref = useRef<HTMLDivElement>(null);
  const [selected, setSelected] = useState(statuses.map(([value]) => value));
  const [error, setError] = useState(false);
  const [runtime, setRuntime] = useState<{ L: Leaflet; map: MapInstance; markers: MarkerGroup } | null>(null);
  const fitted = useRef(false);
  const [popups, setPopups] = useState<{ element: HTMLElement; barrier: Barrier }[]>([]);
  const visible = useMemo(() => barriers.filter(barrier => selected.includes(barrier.status)), [barriers, selected]);
  const mapped = useMemo(() => visible.filter(barrier => !barrier.blocks_map && barrier.lat !== null && barrier.lng !== null
    && Number.isFinite(barrier.lat) && Number.isFinite(barrier.lng)
    && Math.abs(barrier.lat) <= 90 && Math.abs(barrier.lng) <= 180), [visible]);
  const message = !visible.length ? 'Nenhuma barreira encontrada para os filtros selecionados.'
    : visible.every(barrier => barrier.blocks_map) ? 'As barreiras selecionadas não se aplicam ao mapa.'
    : !mapped.length ? 'As barreiras selecionadas não possuem coordenadas válidas.' : '';
  useEffect(() => {
    let disposed = false;
    let map: MapInstance | undefined;
    let observer: ResizeObserver | undefined;
    setError(false);
    void loadExternalScript('/assets/react/plugins/leaflet/leaflet.js').then(() => {
      if (disposed || !ref.current) return;
      const L = (window as unknown as { L: Leaflet }).L;
      map = L.map(ref.current, { center: [-14.235, -42.769], zoom: 15, scrollWheelZoom: false });
      const streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 19 }).addTo(map);
      const satellite = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { attribution: '© Google Maps', maxZoom: 21 });
      L.control.layers({ 'Mapa de ruas': streets, 'Satélite': satellite }).addTo(map);
      const markers = L.layerGroup().addTo(map);
      fitted.current = false;
      setRuntime({ L, map, markers });
      observer = new ResizeObserver(() => map?.invalidateSize());
      observer.observe(ref.current);
    }).catch(() => { if (!disposed) setError(true); });
    return () => { disposed = true; observer?.disconnect(); map?.remove(); };
  }, []);

  useEffect(() => {
    if (!runtime) return;
    const { L, map, markers } = runtime;
    markers.clearLayers();
    const nextPopups: { element: HTMLElement; barrier: Barrier }[] = [];
      const points: number[][] = [];
      for (const barrier of mapped) {
        const point = [barrier.lat!, barrier.lng!];
        const popup = document.createElement('div');
        nextPopups.push({ element: popup, barrier });
        markers.addLayer(L.circleMarker(point, { radius: 9, color: colors[barrier.color] ?? colors.primary, fillOpacity: 0.85 }).bindPopup(popup, { className: 'barrierPopup', minWidth: 240, maxWidth: 240 }));
        points.push(point);
      }
      if (!fitted.current && points.length) {
        map.fitBounds(points, { padding: [35, 35], maxZoom: 16 });
        fitted.current = true;
      }
    setPopups(nextPopups);
  }, [runtime, mapped]);
  return <CardBox className="gnaiDashboard__chart">
    {popups.map(({ element, barrier }) => createPortal(<BarrierPopup barrier={barrier} />, element, String(barrier.id)))}
    <h2 className="header-title">Mapa de barreiras</h2>
    <div ref={ref} className="gnaiDashboard__map" role="region" aria-label="Localização das barreiras" />
    {(error || message) && <p role="status">{error ? 'Não foi possível carregar o mapa. Recarregue a página para tentar novamente.' : message}</p>}
    <fieldset className="gnaiDashboard__filters"><legend>Visualizar no mapa</legend>
      <ChoiceControl type="checkbox" id="dashboard-barriers-all" label="Todas" tone="custom" inline
        checked={selected.length === statuses.length}
        onChange={checked => setSelected(checked ? statuses.map(([value]) => value) : [])} />
      {statuses.map(([value, label]) => <ChoiceControl key={value} type="checkbox"
        id={`dashboard-barriers-${value}`} label={label} tone="custom" inline
        checked={selected.includes(value)}
        onChange={checked => setSelected(current => checked ? [...current, value] : current.filter(item => item !== value))} />)}
    </fieldset>
  </CardBox>;
}
