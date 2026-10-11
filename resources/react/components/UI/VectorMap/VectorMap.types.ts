export interface VectorMapMarker {
  latLng: [number, number];
  name: string;
}
export interface VectorMapProps {
  map: string;
  source: string;
  color: string;
  height?: number;
  markers?: VectorMapMarker[];
  markerStyle?: Record<string, Record<string, string | number>>;
}
