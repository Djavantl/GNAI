// resources/js/maps/institution-map.js

class InstitutionMap {
    constructor(config) {
        this.config = config;
        this.map = null;
        this.marker = null;
        this.initialized = false;
        this.timer = null;

        this.init();
    }

    init() {
        if (this.initialized) return;

        // Elementos do DOM
        this.mapContainer = document.getElementById(this.config.mapId);
        this.container = document.getElementById(`leaflet-container-${this.config.mapId}`);

        if (!this.mapContainer || !this.container) {
            return;
        }

        // Elementos de entrada - usar IDs corretos baseados no seu HTML
        this.latInput = document.getElementById('lat');
        this.lngInput = document.getElementById('lng');
        this.zoomRange = document.getElementById('zoom_range');
        this.zoomBadge = document.getElementById('zoom_val');

        // Elementos de busca - usar os IDs exatos do seu form
        this.cityInput = this.getRealInput('city_search');
        this.stateInput = this.getRealInput('state_search');
        this.districtInput = this.getRealInput('district_search');
        this.addressInput = this.getRealInput('address_search');

        // Elementos manuais
        this.latManual = document.getElementById('lat_manual');
        this.lngManual = document.getElementById('lng_manual');

        // Coordenadas iniciais - priorizar valores do form, depois config
        let initialLat = this.config.lat;
        let initialLng = this.config.lng;

        if (this.latInput && this.latInput.value && !isNaN(parseFloat(this.latInput.value))) {
            initialLat = parseFloat(this.latInput.value);
        }

        if (this.lngInput && this.lngInput.value && !isNaN(parseFloat(this.lngInput.value))) {
            initialLng = parseFloat(this.lngInput.value);
        }

        const initialZoom = (this.zoomRange && this.zoomRange.value)
            ? parseInt(this.zoomRange.value)
            : this.config.zoom;

        // Cria mapa
        this.createMap(initialLat, initialLng, initialZoom);

        // Configura marcador
        this.setupMarker(initialLat, initialLng);

        // Configura eventos
        this.setupMapEvents();
        this.setupAddressEvents();
        this.setupZoomEvents();
        this.setupManualInputEvents();

        // Atualiza displays inicialmente
        this.updateDisplays(initialLat, initialLng);

        // Redimensiona mapa após carregamento
        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize();
            }
        }, 100);

        this.initialized = true;
    }

    getRealInput(id) {
        const el = document.getElementById(id);
        if (!el) {
            return null;
        }
        // Se for um input diretamente, retorna ele
        if (el.tagName === 'INPUT') {
            return el;
        }
        // Se for um container, busca o input dentro
        const input = el.querySelector('input');
        return input;
    }

    createMap(lat, lng, zoom) {
        // Camadas
        const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        });

        const googleSatellite = L.tileLayer(
            'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
            {
                attribution: '© Google Maps',
                maxZoom: 21
            }
        );

        // Cria mapa
        this.map = L.map(this.config.mapId, {
            center: [lat, lng],
            zoom: zoom,
            layers: [streetLayer],
            zoomControl: true,
            scrollWheelZoom: true
        });

        // Controle de camadas
        const baseMaps = {
            "Mapa de Ruas (OSM)": streetLayer,
            "Satélite (Google)": googleSatellite
        };

        L.control.layers(baseMaps).addTo(this.map);
    }

    setupMarker(lat, lng) {
        const blueIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            shadowSize: [41, 41],
            popupAnchor: [1, -34]
        });

        this.marker = L.marker([lat, lng], {
            draggable: true,
            icon: blueIcon
        }).addTo(this.map);

        this.marker.bindTooltip("Localização Selecionada", {
            permanent: true,
            direction: 'top',
            offset: [0, -35],
            className: 'bg-primary text-white border-0 fw-bold rounded shadow-sm px-2 py-1'
        });
    }

    setupMapEvents() {
        // Evento de clique no mapa
        this.map.on('click', (e) => {
            this.updateLocation(e.latlng.lat, e.latlng.lng, false, true);
        });

        // Evento de arrastar marcador
        this.marker.on('dragend', () => {
            const pos = this.marker.getLatLng();
            this.updateLocation(pos.lat, pos.lng, false, true);
        });

        // Evento de zoom do mapa atualiza o range
        this.map.on('zoomend', () => {
            if (this.zoomRange) {
                const currentZoom = this.map.getZoom();
                this.zoomRange.value = currentZoom;
                if (this.zoomBadge) this.zoomBadge.innerText = currentZoom;
            }
        });
    }

    setupAddressEvents() {
        // Eventos de busca por endereço
        [this.cityInput, this.stateInput, this.districtInput, this.addressInput].forEach(input => {
            if (input) {
                // Usar input com debounce para evitar muitas requisições
                input.addEventListener('input', () => {
                    clearTimeout(this.timer);
                    this.timer = setTimeout(() => this.searchAddress(), 500);
                });

                // Permitir busca com Enter
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.searchAddress();
                    }
                });
            }
        });
    }

    setupZoomEvents() {
        if (this.zoomRange) {
            this.zoomRange.addEventListener('input', (e) => {
                const zoomValue = parseInt(e.target.value);
                if (this.zoomBadge) this.zoomBadge.innerText = zoomValue;
                if (this.map) {
                    this.map.setZoom(zoomValue);
                }
            });
        }
    }

    setupManualInputEvents() {
        // Eventos para inputs manuais de lat/lng
        if (this.latManual) {
            this.latManual.addEventListener('change', () => {
                const lat = parseFloat(this.latManual.value);
                if (!isNaN(lat) && this.marker) {
                    const currentLng = this.marker.getLatLng().lng;
                    this.updateLocation(lat, currentLng, true, false);
                }
            });

            // Atualizar input hidden quando manual mudar
            this.latManual.addEventListener('input', () => {
                if (this.latInput) {
                    this.latInput.value = this.latManual.value;
                }
            });
        }

        if (this.lngManual) {
            this.lngManual.addEventListener('change', () => {
                const lng = parseFloat(this.lngManual.value);
                if (!isNaN(lng) && this.marker) {
                    const currentLat = this.marker.getLatLng().lat;
                    this.updateLocation(currentLat, lng, true, false);
                }
            });

            // Atualizar input hidden quando manual mudar
            this.lngManual.addEventListener('input', () => {
                if (this.lngInput) {
                    this.lngInput.value = this.lngManual.value;
                }
            });
        }
    }

    async reverseGeocode(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&zoom=18&accept-language=pt-BR`
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data && data.address) {
                const addr = data.address;

                const city = addr.city || addr.town || addr.municipality || addr.village || '';
                const district = addr.hamlet || addr.suburb || addr.neighbourhood || addr.city_district || addr.village || '';
                const state = addr.state || '';
                const street = addr.road || addr.pedestrian || addr.path || '';

                if (this.cityInput) {
                    this.cityInput.value = city;
                }
                if (this.districtInput) {
                    this.districtInput.value = district;
                }
                if (this.stateInput) {
                    this.stateInput.value = state;
                }
                if (this.addressInput) {
                    this.addressInput.value = street;
                }
            }
        } catch (error) {
            // Erro silencioso em produção
        }
    }

    updateLocation(lat, lng, moveMap = false, updateAddress = true, forceZoom = null) {
        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);

        // 1. Atualiza inputs hidden (os que serão enviados no form)
        if (this.latInput) {
            this.latInput.value = fLat;
        }
        if (this.lngInput) {
            this.lngInput.value = fLng;
        }

        // 2. Atualiza inputs manuais (para visualização)
        if (this.latManual) {
            this.latManual.value = fLat;
        }
        if (this.lngManual) {
            this.lngManual.value = fLng;
        }

        // 3. Atualiza displays visuais
        this.updateDisplays(fLat, fLng);

        // 4. Move marcador
        if (this.marker) {
            this.marker.setLatLng([lat, lng]);
        }

        // 5. Move mapa se necessário
        if (moveMap && this.map) {
            const zoomLevel = forceZoom ||
                ((this.addressInput && this.addressInput.value && this.addressInput.value.length > 2) ? 18 : 14);

            this.map.flyTo([lat, lng], zoomLevel, {
                animate: true,
                duration: 1.5
            });
        }

        // 6. Faz geocodificação reversa
        if (updateAddress) {
            this.reverseGeocode(lat, lng);
        }
    }

    updateDisplays(lat, lng) {
        // Atualiza displays visuais
        const latDisplay = document.getElementById(`display-${this.config.mapId}-lat`);
        const lngDisplay = document.getElementById(`display-${this.config.mapId}-lng`);

        if (latDisplay) {
            latDisplay.innerText = lat;
        }
        if (lngDisplay) {
            lngDisplay.innerText = lng;
        }
    }

    searchAddress() {
        const city = this.cityInput ? this.cityInput.value.trim() : '';
        const state = this.stateInput ? this.stateInput.value.trim() : '';
        const district = this.districtInput ? this.districtInput.value.trim() : '';
        const street = this.addressInput ? this.addressInput.value.trim() : '';

        // Verifica se tem dados suficientes para buscar
        const hasEnoughData = (city.length >= 2 || street.length >= 3 || district.length >= 3 || state.length >= 2);
        if (!hasEnoughData) {
            return;
        }

        // Monta query
        const queryParts = [];
        if (street) queryParts.push(street);
        if (district) queryParts.push(district);
        if (city) queryParts.push(city);
        if (state) queryParts.push(state);
        queryParts.push('Brasil');

        const query = queryParts.join(', ');

        // Faz busca com timeout
        const timeout = 10000; // 10 segundos
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeout);

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=1&accept-language=pt-BR`, {
            signal: controller.signal
        })
            .then(r => {
                clearTimeout(timeoutId);
                if (!r.ok) throw new Error(`HTTP error! status: ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (data && data.length > 0) {
                    const addr = data[0].address;

                    // Valida cidade se foi especificada
                    if (city) {
                        const foundCity = (addr.city || addr.town || addr.municipality || addr.village || '').toLowerCase();
                        const searchCity = city.toLowerCase();
                        if (foundCity && !foundCity.includes(searchCity) && !searchCity.includes(foundCity)) {
                            return;
                        }
                    }

                    // Determina zoom baseado no tipo de busca
                    let dynamicZoom = 12;
                    if (street) dynamicZoom = 18;
                    else if (district) dynamicZoom = 16;
                    else if (city) dynamicZoom = 14;
                    else if (state) dynamicZoom = 6;

                    // Atualiza localização SEM geocodificação reversa (já temos o endereço)
                    this.updateLocation(data[0].lat, data[0].lon, true, false, dynamicZoom);
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                // Erro silencioso em produção
            });
    }

    // Método para forçar atualização de coordenadas (útil para debug)
    forceUpdateCoordinates(lat, lng) {
        this.updateLocation(lat, lng, true, true);
    }
}

// Inicialização condicional com melhor tratamento de erros
document.addEventListener('DOMContentLoaded', () => {
    const mapElement = document.getElementById('map-institution');

    if (!mapElement) {
        return;
    }

    if (!window.institutionMapConfig) {
        return;
    }

    if (typeof L === 'undefined') {
        return;
    }

    try {
        const map = new InstitutionMap(window.institutionMapConfig);
        window.institutionMapInstance = map;
    } catch (error) {
    }
});

// Expor a classe globalmente para acesso via console
if (typeof window !== 'undefined') {
    window.InstitutionMap = InstitutionMap;
}
