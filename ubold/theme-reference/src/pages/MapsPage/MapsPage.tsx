import CardBox from '@/Components/UI/CardBox/CardBox';
import PageBreadcrumb from '@/Components/Navigation/PageBreadcrumb/PageBreadcrumb';
import GoogleMap from '@/Components/UI/GoogleMap/GoogleMap';
import VectorMap from '@/Components/UI/VectorMap/VectorMap';
import { googleMaps } from './MapsPage.google.data';
import { vectorMaps } from './MapsPage.vector.data';
import type { MapsPageVariant } from './MapsPage.types';
import './MapsPage.css';

const apiKey = (import.meta as ImportMeta & { env: Record<string, string | undefined> }).env.VITE_GOOGLE_MAPS_API_KEY;

export default function MapsPage({ variant }: { variant: MapsPageVariant }): JSX.Element {
  const title = variant === 'google' ? 'Google Maps' : 'Vector Map';
  return (
    <section className="mapsPage">
      <h4 className="mapsPage__title">{title}</h4>
      <PageBreadcrumb
        items={[
          { label: 'Ubold', href: '#' },
          { label: 'Maps', href: '#' },
          { label: variant === 'google' ? 'Gmaps' : title }
        ]}
      />
      <div className="mapsPage__grid">
        {variant === 'vector'
          ? vectorMaps.map(({ title: heading, fullWidth, ...props }) => (
              <CardBox key={props.map} className={`mapsPage__card${fullWidth ? ' mapsPage__card--full' : ''}`}>
                <h4 className="mapsPage__cardTitle">
                  <b>{heading}</b>
                </h4>
                <VectorMap {...props} />
              </CardBox>
            ))
          : googleMaps.map(({ title: heading, ...props }) => (
              <CardBox key={props.variant} className="mapsPage__card">
                <h4 className="mapsPage__cardTitle">
                  <b>{heading}</b>
                </h4>
                <GoogleMap {...props} apiKey={apiKey} />
              </CardBox>
            ))}
      </div>
    </section>
  );
}
