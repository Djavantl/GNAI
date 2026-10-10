import type { VectorMapProps } from '@/Components/UI/VectorMap/VectorMap.types';
import type { GoogleMapProps } from '@/Components/UI/GoogleMap/GoogleMap.types';
export type MapsPageVariant = 'google' | 'vector';
export interface VectorMapExample extends VectorMapProps {
  title: string;
  fullWidth?: boolean;
}
export interface GoogleMapExample extends GoogleMapProps {
  title: string;
}
