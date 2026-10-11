import { loadExternalScript } from '@/utils/loadExternalScript';
import type { GoogleMapProps } from './GoogleMap.types';

// Dynamic APIs are confined to the adapter for the bundled GMaps version.
const runtime = window as Window & Record<string, any>;
let loading: Promise<void> | undefined;
export function loadGoogleMaps(apiKey?: string): Promise<void> {
  if (!loading) {
    loading = (async (): Promise<void> => {
      if (!runtime.google?.maps?.Map) {
        const url = new URL('https://maps.google.com/maps/api/js');
        if (apiKey) url.searchParams.set('key', apiKey);
        await loadExternalScript(url.href);
      }
      await loadExternalScript('/assets/react/plugins/gmaps/gmaps.min.js');
    })();
  }
  return loading;
}

export function mountGoogleMap(
  element: HTMLDivElement,
  { variant = 'basic', center = [-12.043333, -77.028333], polygon = [] }: GoogleMapProps
): { resize: () => void; destroy: () => void } {
  const { google, GMaps } = runtime;
  const [lat, lng] = center;
  if (variant === 'panorama') {
    const panorama = GMaps.createPanorama({ el: `#${element.id}`, lat, lng });
    return {
      resize: (): void => google.maps.event.trigger(panorama, 'resize'),
      destroy: (): void => {
        panorama.setVisible(false);
        google.maps.event.clearInstanceListeners(panorama);
        panorama.unbindAll();
      }
    };
  }
  const map = new GMaps({
    div: element,
    lat,
    lng,
    ...(variant === 'types'
      ? { mapTypeControlOptions: { mapTypeIds: ['hybrid', 'roadmap', 'satellite', 'terrain', 'osm', 'cloudmade'] } }
      : {})
  });
  if (variant === 'markers') {
    map.addMarker({
      lat: -12.043333,
      lng: -77.03,
      title: 'Lima',
      details: { database_id: 42, author: 'HPNeo' },
      click: (): void => window.alert('You clicked in this marker')
    });
    map.addMarker({
      lat: -12.042,
      lng: -77.028333,
      title: 'Marker with InfoWindow',
      infoWindow: { content: '<p>HTML Content</p>' }
    });
  }
  if (variant === 'polygons')
    map.drawPolygon({
      paths: polygon,
      strokeColor: '#BBD8E9',
      strokeOpacity: 1,
      strokeWeight: 3,
      fillColor: '#BBD8E9',
      fillOpacity: 0.6
    });
  if (variant === 'overlay')
    map.drawOverlay({
      lat,
      lng,
      content: '<div class="gmaps-overlay">Our Office!<div class="gmaps-overlay_arrow above"></div></div>',
      verticalAlign: 'top',
      horizontalAlign: 'center'
    });
  // The legacy Routes card has no start_travel button or instructions list.
  if (variant === 'types') {
    map.addMapType('osm', {
      getTileUrl: (coord: { x: number; y: number }, zoom: number): string =>
        `https://tile.openstreetmap.org/${zoom}/${coord.x}/${coord.y}.png`,
      tileSize: new google.maps.Size(256, 256),
      name: 'OpenStreetMap',
      maxZoom: 18
    });
    map.addMapType('cloudmade', {
      getTileUrl: (coord: { x: number; y: number }, zoom: number): string =>
        `https://b.tile.cloudmade.com/8ee2a50541944fb9bcedded5165f09d9/1/256/${zoom}/${coord.x}/${coord.y}.png`,
      tileSize: new google.maps.Size(256, 256),
      name: 'CloudMade',
      maxZoom: 18
    });
    map.setMapTypeId('osm');
  }
  if (variant === 'menu')
    map.setContextMenu({
      control: 'map',
      options: [
        {
          title: 'Add marker',
          name: 'add_marker',
          action: (event: { latLng: { lat: () => number; lng: () => number } }): void => {
            map.addMarker({ lat: event.latLng.lat(), lng: event.latLng.lng(), title: 'New marker' });
            map.hideContextMenu();
          }
        },
        {
          title: 'Center here',
          name: 'center_here',
          action: (event: { latLng: { lat: () => number; lng: () => number } }): void =>
            map.setCenter(event.latLng.lat(), event.latLng.lng())
        }
      ]
    });
  return {
    resize: (): void => {
      const currentCenter = map.getCenter();
      map.refresh();
      map.map.setCenter(currentCenter);
    },
    destroy: (): void => {
      map.hideInfoWindows();
      for (const item of [...map.markers, ...map.polygons, ...map.polylines, ...map.overlays])
        google.maps.event.clearInstanceListeners(item);
      map.removeMarkers();
      map.removePolygons();
      map.removePolylines();
      map.removeOverlays();
      google.maps.event.clearInstanceListeners(map.map);
      map.map.unbindAll();
      delete runtime.context_menu?.[element.id];
      if (variant === 'menu') {
        const menu = document.getElementById('gmaps_context_menu');
        if (menu) {
          google.maps.event.clearInstanceListeners(menu);
          menu.remove();
        }
      }
    }
  };
}
