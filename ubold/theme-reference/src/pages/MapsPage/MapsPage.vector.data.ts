import type { VectorMapExample } from './MapsPage.types';

export const vectorMaps: VectorMapExample[] = [
  {
    title: 'World Map',
    map: 'world_mill_en',
    source: 'jquery-jvectormap-world-mill-en.js',
    color: '#5fbeaa',
    height: 500,
    fullWidth: true,
    markers: [
      {
        latLng: [41.9, 12.45],
        name: 'Vatican City'
      },
      {
        latLng: [43.73, 7.41],
        name: 'Monaco'
      },
      {
        latLng: [-0.52, 166.93],
        name: 'Nauru'
      },
      {
        latLng: [-8.51, 179.21],
        name: 'Tuvalu'
      },
      {
        latLng: [43.93, 12.46],
        name: 'San Marino'
      },
      {
        latLng: [47.14, 9.52],
        name: 'Liechtenstein'
      },
      {
        latLng: [7.11, 171.06],
        name: 'Marshall Islands'
      },
      {
        latLng: [17.3, -62.73],
        name: 'Saint Kitts and Nevis'
      },
      {
        latLng: [3.2, 73.22],
        name: 'Maldives'
      },
      {
        latLng: [35.88, 14.5],
        name: 'Malta'
      },
      {
        latLng: [12.05, -61.75],
        name: 'Grenada'
      },
      {
        latLng: [13.16, -61.23],
        name: 'Saint Vincent and the Grenadines'
      },
      {
        latLng: [13.16, -59.55],
        name: 'Barbados'
      },
      {
        latLng: [17.11, -61.85],
        name: 'Antigua and Barbuda'
      },
      {
        latLng: [-4.61, 55.45],
        name: 'Seychelles'
      },
      {
        latLng: [7.35, 134.46],
        name: 'Palau'
      },
      {
        latLng: [42.5, 1.51],
        name: 'Andorra'
      },
      {
        latLng: [14.01, -60.98],
        name: 'Saint Lucia'
      },
      {
        latLng: [6.91, 158.18],
        name: 'Federated States of Micronesia'
      },
      {
        latLng: [1.3, 103.8],
        name: 'Singapore'
      },
      {
        latLng: [1.46, 173.03],
        name: 'Kiribati'
      },
      {
        latLng: [-21.13, -175.2],
        name: 'Tonga'
      },
      {
        latLng: [15.3, -61.38],
        name: 'Dominica'
      },
      {
        latLng: [-20.2, 57.5],
        name: 'Mauritius'
      },
      {
        latLng: [26.02, 50.55],
        name: 'Bahrain'
      },
      {
        latLng: [0.33, 6.73],
        name: 'São Tomé and Príncipe'
      }
    ],
    markerStyle: {
      initial: {
        r: 9,
        fill: '#a288d5',
        'fill-opacity': 0.9,
        stroke: '#fff',
        'stroke-width': 7,
        'stroke-opacity': 0.4
      },
      hover: {
        stroke: '#fff',
        'fill-opacity': 1,
        'stroke-width': 1.5
      }
    }
  },
  {
    title: 'USA Map',
    map: 'us_aea_en',
    source: 'jquery-jvectormap-us-aea-en.js',
    color: '#fb6d9d',
    height: 400
  },
  {
    title: 'India Map',
    map: 'in_mill',
    source: 'jquery-jvectormap-in-mill.js',
    color: '#5d9cec',
    height: 400
  },
  {
    title: 'UK Map',
    map: 'uk_mill_en',
    source: 'jquery-jvectormap-uk-mill-en.js',
    color: '#81c868',
    height: 400
  },
  {
    title: 'Chicago Map',
    map: 'us-il-chicago_mill_en',
    source: 'jquery-jvectormap-us-il-chicago-mill-en.js',
    color: '#34d3eb',
    height: 400
  },
  {
    title: 'Australia Map',
    map: 'au_mill',
    source: 'jquery-jvectormap-au-mill.js',
    color: '#ffbd4a',
    height: 400
  },
  {
    title: 'Canada Map',
    map: 'ca_lcc',
    source: 'jquery-jvectormap-ca-lcc.js',
    color: '#f05050',
    height: 400
  },
  {
    title: 'Germany Map',
    map: 'de_mill',
    source: 'jquery-jvectormap-de-mill.js',
    color: '#7266ba',
    height: 400
  },
  {
    title: 'Asia Map',
    map: 'asia_mill',
    source: 'jquery-jvectormap-asia-mill.js',
    color: '#4c5667',
    height: 400
  }
];
