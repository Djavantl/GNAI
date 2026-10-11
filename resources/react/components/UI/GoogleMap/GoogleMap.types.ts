export type GoogleMapVariant = 'basic' | 'markers' | 'polygons' | 'overlay' | 'panorama' | 'route' | 'types' | 'menu';
export interface GoogleMapProps {
  variant?: GoogleMapVariant;
  center?: readonly [number, number];
  polygon?: readonly (readonly [number, number])[];
  apiKey?: string;
}
