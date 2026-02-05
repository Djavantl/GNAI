function initInstitutionMap() {
    const mapContainer = document.getElementById('map');
    const container = document.getElementById('leaflet-container-main');
    if (!mapContainer || !container) return;

    const legendMain = document.getElementById('map-legend-main');
    const legendBlue = document.getElementById('legend-item-blue');
    const legendRed = document.getElementById('legend-item-red');
    const legendGrey = document.getElementById('legend-item-grey');

    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const zoomRange = document.getElementById('zoom_range');
    const zoomBadge = document.getElementById('zoom_val');

    const getRealInput = (id) => {
        const el = document.getElementById(id);
        if (!el) return null;
        return el.tagName === 'INPUT' ? el : el.querySelector('input');
    };

    const cityInput = getRealInput('city_search');
    const stateInput = getRealInput('state_search');
    const districtInput = getRealInput('district_search');
    const addressInput = getRealInput('address_search');

    const initialLat = (latInput && latInput.value)
        ? parseFloat(latInput.value)
        : (parseFloat(container.getAttribute('data-lat')) || -14.2350);

    const initialLng = (lngInput && lngInput.value)
        ? parseFloat(lngInput.value)
        : (parseFloat(container.getAttribute('data-lng')) || -51.9253);

    const initialZoom = (zoomRange && zoomRange.value)
        ? parseInt(zoomRange.value)
        : (parseInt(container.getAttribute('data-zoom')) || 16);

    const blueIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        shadowSize: [41, 41]
    });

    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    });

    const googleSatellite = L.tileLayer(
        'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
        { attribution: '© Google Maps' }
    );

    const map = L.map('map', {
        center: [initialLat, initialLng],
        zoom: initialZoom,
        layers: [streetLayer]
    });

    const baseMaps = {
        "Mapa de Ruas (OSM)": streetLayer,
        "Satélite (Google)": googleSatellite
    };

    L.control.layers(baseMaps).addTo(map);
    setTimeout(() => map.invalidateSize(), 400);

    let marker = L.marker([initialLat, initialLng], {
        draggable: true,
        icon: blueIcon
    }).addTo(map);

    marker.bindTooltip("Localização Selecionada", {
        permanent: true,
        direction: 'top',
        offset: [0, -35],
        className: 'bg-primary text-white border-0 fw-bold rounded shadow-sm px-2 py-1'
    });

    if (legendMain) {
        legendMain.classList.remove('d-none');
        if (legendBlue) legendBlue.classList.remove('d-none');
        if (legendRed) legendRed.classList.add('d-none');
        if (legendGrey) legendGrey.classList.add('d-none');
    }

    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&zoom=18&accept-language=pt-BR`
            );
            const data = await response.json();

            if (data && data.address) {
                const addr = data.address;

                const city = addr.city || addr.town || addr.municipality || addr.village || '';
                const district = addr.hamlet || addr.suburb || addr.neighbourhood || addr.city_district || addr.village || '';
                const state = addr.state || '';
                const street = addr.road || addr.pedestrian || addr.path || '';

                if (cityInput) cityInput.value = city;
                if (districtInput) districtInput.value = district;
                if (stateInput) stateInput.value = state;
                if (addressInput) addressInput.value = street;
            }
        } catch (error) {
            console.error(error);
        }
    }

    function updateLocation(lat, lng, moveMap = false, updateAddress = true, forceZoom = null) {
        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);

        if (latInput) latInput.value = fLat;
        if (lngInput) lngInput.value = fLng;

        const dLat = document.getElementById(`display-${container.getAttribute('data-lat-id')}`);
        const dLng = document.getElementById(`display-${container.getAttribute('data-lng-id')}`);
        if (dLat) dLat.innerText = fLat;
        if (dLng) dLng.innerText = fLng;

        marker.setLatLng([lat, lng]);

        if (moveMap) {
            const zoomLevel = forceZoom || ((addressInput && addressInput.value.length > 2) ? 18 : 14);
            map.flyTo([lat, lng], zoomLevel, { animate: true, duration: 4 });
        }

        if (updateAddress) {
            reverseGeocode(lat, lng);
        }
    }

    map.on('click', (e) => updateLocation(e.latlng.lat, e.latlng.lng, false, true));
    marker.on('dragend', () => updateLocation(marker.getLatLng().lat, marker.getLatLng().lng, false, true));

    let timer;
    const searchAddress = () => {
        const city = cityInput ? cityInput.value.trim() : '';
        const state = stateInput ? stateInput.value.trim() : '';
        const district = districtInput ? districtInput.value.trim() : '';
        const street = addressInput ? addressInput.value.trim() : '';

        const hasEnoughData = (city.length >= 3 || street.length >= 3 || district.length >= 3 || state.length >= 2);
        if (!hasEnoughData) return;

        const queryParts = [];
        if (street) queryParts.push(street);
        if (district) queryParts.push(district);
        if (city) queryParts.push(city);
        if (state) queryParts.push(state);
        queryParts.push('Brasil');

        const query = queryParts.join(', ');

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=1&accept-language=pt-BR`)
            .then(r => r.json())
            .then(data => {
                if (data && data.length > 0) {
                    const addr = data[0].address;

                    if (city) {
                        const foundCity = (addr.city || addr.town || addr.municipality || addr.village || '').toLowerCase();
                        if (foundCity && !foundCity.includes(city.toLowerCase())) return;
                    }

                    let dynamicZoom = 12;
                    if (street) dynamicZoom = 18;
                    else if (district) dynamicZoom = 16;
                    else if (city) dynamicZoom = 14;
                    else if (state) dynamicZoom = 6;

                    updateLocation(data[0].lat, data[0].lon, true, false, dynamicZoom);
                }
            })
            .catch(console.error);
    };

    [cityInput, stateInput, districtInput, addressInput].forEach(input => {
        if (input) {
            ['input', 'change'].forEach(evt => {
                input.addEventListener(evt, () => {
                    clearTimeout(timer);
                    timer = setTimeout(searchAddress, 1000);
                });
            });
        }
    });

    if (zoomRange) {
        zoomRange.addEventListener('input', (e) => {
            if (zoomBadge) zoomBadge.innerText = e.target.value;
            map.setZoom(e.target.value);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof L !== 'undefined') initInstitutionMap();
});
