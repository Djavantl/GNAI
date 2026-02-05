let mapInstance = null;

function initLocationMap() {
    let mapContainer = document.getElementById('map');
    const instSelect = document.getElementById('institution_select');
    const container = document.getElementById('leaflet-container-main');

    if (!mapContainer || !instSelect || !container) return;

    const legendMain = document.getElementById('map-legend-main');
    const legendBlue = document.getElementById('legend-item-blue');
    const legendRed = document.getElementById('legend-item-red');
    const legendGrey = document.getElementById('legend-item-grey');

    if (mapInstance !== null) {
        mapInstance.off();
        mapInstance.remove();
        mapInstance = null;
    }

    if (mapContainer._leaflet_id) {
        const parent = mapContainer.parentNode;
        const newMapDiv = document.createElement('div');
        newMapDiv.id = 'map';
        newMapDiv.style.height = mapContainer.style.height || '550px';
        newMapDiv.style.width = '100%';
        newMapDiv.style.zIndex = '1';
        parent.replaceChild(newMapDiv, mapContainer);
        mapContainer = newMapDiv;
    }

    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const latManual = document.getElementById('lat_manual');
    const lngManual = document.getElementById('lng_manual');

    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    });

    const googleSatellite = L.tileLayer(
        'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
        { attribution: '© Google Maps' }
    );

    const baseMaps = {
        "Mapa de Ruas (OSM)": streetLayer,
        "Satélite (Google)": googleSatellite
    };

    const redIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], shadowSize: [41, 41]
    });

    const blueIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], shadowSize: [41, 41]
    });

    const greyIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [20, 32], iconAnchor: [10, 32], shadowSize: [32, 32]
    });

    let initialLat = (latInput && latInput.value) ? parseFloat(latInput.value) : -14.2350;
    let initialLng = (lngInput && lngInput.value) ? parseFloat(lngInput.value) : -51.9253;

    mapInstance = L.map('map', {
        center: [initialLat, initialLng],
        zoom: (latInput && latInput.value ? 18 : 4),
        layers: [streetLayer]
    });

    L.control.layers(baseMaps).addTo(mapInstance);

    let currentMarker = null;
    let mainInstitutionMarker = null;
    let existingMarkersGroup = L.layerGroup().addTo(mapInstance);

    function updateMarker(lat, lng, moveMap = false) {
        const formattedLat = parseFloat(lat).toFixed(8);
        const formattedLng = parseFloat(lng).toFixed(8);

        if (currentMarker) {
            currentMarker.setLatLng([lat, lng]);
        } else {
            currentMarker = L.marker([lat, lng], {
                draggable: true,
                icon: blueIcon,
                zIndexOffset: 1000
            }).addTo(mapInstance);

            currentMarker.bindTooltip("Localização Selecionada", {
                permanent: true,
                direction: 'top',
                offset: [0, -35],
                className: 'bg-primary text-white border-0 fw-bold rounded shadow-sm px-2 py-1'
            });

            currentMarker.on('dragend', function() {
                const pos = currentMarker.getLatLng();
                updateMarker(pos.lat, pos.lng);
            });
        }

        if (latInput) latInput.value = formattedLat;
        if (lngInput) lngInput.value = formattedLng;
        if (latManual) latManual.value = formattedLat;
        if (lngManual) lngManual.value = formattedLng;

        if (moveMap) {
            mapInstance.panTo([lat, lng]);
        }
    }

    function syncFromManualInputs() {
        const latVal = parseFloat(latManual.value);
        const lngVal = parseFloat(lngManual.value);
        if (!isNaN(latVal) && !isNaN(lngVal)) {
            updateMarker(latVal, lngVal, true);
        }
    }

    if (latManual) latManual.addEventListener('change', syncFromManualInputs);
    if (lngManual) lngManual.addEventListener('change', syncFromManualInputs);

    function updateLegendVisibility(showBlue, showRed, showGrey) {
        if (!legendMain) return;
        legendMain.classList.toggle('d-none', (!showBlue && !showRed && !showGrey));
        if (legendBlue) legendBlue.classList.toggle('d-none', !showBlue);
        if (legendRed) legendRed.classList.toggle('d-none', !showRed);
        if (legendGrey) legendGrey.classList.toggle('d-none', !showGrey);
    }

    instSelect.addEventListener('change', function() {
        const instId = this.value;
        const currentEditId = container.getAttribute('data-location-id');

        existingMarkersGroup.clearLayers();
        if (mainInstitutionMarker) mapInstance.removeLayer(mainInstitutionMarker);

        if (!instId) {
            if (currentMarker) {
                mapInstance.removeLayer(currentMarker);
                currentMarker = null;
            }
            if (latInput) latInput.value = '';
            if (lngInput) lngInput.value = '';
            updateLegendVisibility(false, false, false);
            return;
        }

        if (window.institutionsData) {
            const inst = window.institutionsData.find(i => i.id == instId);

            if (inst) {
                mapInstance.flyTo([inst.latitude, inst.longitude], inst.default_zoom || 17);

                mainInstitutionMarker = L.marker([inst.latitude, inst.longitude], { icon: redIcon }).addTo(mapInstance);
                mainInstitutionMarker.bindTooltip(`<b>Sede:</b> ${inst.name}`, {
                    permanent: false, direction: 'top',
                    className: 'bg-danger text-white border-0 fw-bold shadow-sm px-2 py-1 rounded',
                    offset: [0, -35]
                });

                let hasGreyMarkers = false;
                if (inst.locations && inst.locations.length > 0) {
                    inst.locations.forEach(loc => {
                        if (String(loc.id) !== String(currentEditId)) {
                            hasGreyMarkers = true;
                            const existingMarker = L.marker([loc.latitude, loc.longitude], { icon: greyIcon }).addTo(existingMarkersGroup);
                            existingMarker.bindTooltip(loc.name, {
                                permanent: false, direction: 'top',
                                className: 'bg-secondary text-white border-0 small shadow-sm rounded px-1',
                                offset: [0, -32]
                            });
                        }
                    });
                }

                updateLegendVisibility(true, true, hasGreyMarkers);

                updateMarker(inst.latitude, inst.longitude);
            }
        }
    });

    mapInstance.on('click', (e) => {
        if (!instSelect.value) {
            alert('Selecione uma Instituição Base primeiro!');
            return;
        }
        updateMarker(e.latlng.lat, e.latlng.lng);
    });

    if (latInput && latInput.value && lngInput && lngInput.value) {
        updateMarker(latInput.value, lngInput.value);
        if (instSelect.value) {

            setTimeout(() => instSelect.dispatchEvent(new Event('change')), 100);
        }
    }

    setTimeout(() => { if (mapInstance) mapInstance.invalidateSize(); }, 400);
}

document.addEventListener('DOMContentLoaded', initLocationMap);
