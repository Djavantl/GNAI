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

        console.log('Inicializando InstitutionMap...');

        // Elementos do DOM
        this.mapContainer = document.getElementById(this.config.mapId);
        this.container = document.getElementById(`leaflet-container-${this.config.mapId}`);

        if (!this.mapContainer || !this.container) {
            console.error(`Elementos do mapa não encontrados para ${this.config.mapId}`);
            return;
        }

        // Elementos de entrada - usar IDs corretos baseados no seu HTML
        this.latInput = document.getElementById('lat');
        this.lngInput = document.getElementById('lng');
        this.zoomRange = document.getElementById('zoom_range');
        this.zoomBadge = document.getElementById('zoom_val');

        console.log('Inputs encontrados:', {
            latInput: this.latInput ? 'Sim' : 'Não',
            lngInput: this.lngInput ? 'Sim' : 'Não',
            zoomRange: this.zoomRange ? 'Sim' : 'Não'
        });

        // Elementos de busca - usar os IDs exatos do seu form
        this.cityInput = this.getRealInput('city_search');
        this.stateInput = this.getRealInput('state_search');
        this.districtInput = this.getRealInput('district_search');
        this.addressInput = this.getRealInput('address_search');

        console.log('Inputs de endereço:', {
            city: this.cityInput ? 'Sim' : 'Não',
            state: this.stateInput ? 'Sim' : 'Não',
            district: this.districtInput ? 'Sim' : 'Não',
            address: this.addressInput ? 'Sim' : 'Não'
        });

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

        console.log('Coordenadas iniciais:', initialLat, initialLng, initialZoom);

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
                console.log('Mapa redimensionado');
            }
        }, 100);

        this.initialized = true;
        console.log('InstitutionMap inicializado com sucesso');
    }

    getRealInput(id) {
        const el = document.getElementById(id);
        if (!el) {
            console.warn(`Elemento com ID "${id}" não encontrado`);
            return null;
        }
        // Se for um input diretamente, retorna ele
        if (el.tagName === 'INPUT') {
            return el;
        }
        // Se for um container, busca o input dentro
        const input = el.querySelector('input');
        if (!input) {
            console.warn(`Nenhum input encontrado dentro do elemento com ID "${id}"`);
        }
        return input;
    }

    createMap(lat, lng, zoom) {
        console.log('Criando mapa em:', lat, lng, 'zoom:', zoom);

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

        console.log('Mapa criado com sucesso');
    }

    setupMarker(lat, lng) {
        console.log('Configurando marcador em:', lat, lng);

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

        console.log('Marcador configurado');
    }

    setupMapEvents() {
        console.log('Configurando eventos do mapa');

        // Evento de clique no mapa
        this.map.on('click', (e) => {
            console.log('Mapa clicado em:', e.latlng.lat, e.latlng.lng);
            this.updateLocation(e.latlng.lat, e.latlng.lng, false, true);
        });

        // Evento de arrastar marcador
        this.marker.on('dragend', () => {
            const pos = this.marker.getLatLng();
            console.log('Marcador arrastado para:', pos.lat, pos.lng);
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
                // Usar 'blur' em vez de 'input' para evitar muitas requisições
                input.addEventListener('blur', () => {
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
            console.log('Fazendo geocodificação reversa para:', lat, lng);

            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&zoom=18&accept-language=pt-BR`
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data && data.address) {
                const addr = data.address;
                console.log('Endereço encontrado:', addr);

                const city = addr.city || addr.town || addr.municipality || addr.village || '';
                const district = addr.hamlet || addr.suburb || addr.neighbourhood || addr.city_district || addr.village || '';
                const state = addr.state || '';
                const street = addr.road || addr.pedestrian || addr.path || '';

                // CORREÇÃO AQUI: usar this.districtInput em vez de districtInput
                if (this.cityInput) {
                    this.cityInput.value = city;
                    console.log('Cidade definida:', city);
                }
                if (this.districtInput) {
                    this.districtInput.value = district;
                    console.log('Bairro definido:', district);
                }
                if (this.stateInput) {
                    this.stateInput.value = state;
                    console.log('Estado definido:', state);
                }
                if (this.addressInput) {
                    this.addressInput.value = street;
                    console.log('Rua definida:', street);
                }
            } else {
                console.warn('Nenhum endereço encontrado na geocodificação reversa');
            }
        } catch (error) {
            console.error('Erro na geocodificação reversa:', error);
        }
    }

    updateLocation(lat, lng, moveMap = false, updateAddress = true, forceZoom = null) {
        console.log('Atualizando localização para:', lat, lng);

        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);

        // 1. Atualiza inputs hidden (os que serão enviados no form)
        if (this.latInput) {
            this.latInput.value = fLat;
            console.log('Input hidden LAT atualizado:', this.latInput.value);
        }
        if (this.lngInput) {
            this.lngInput.value = fLng;
            console.log('Input hidden LNG atualizado:', this.lngInput.value);
        }

        // 2. Atualiza inputs manuais (para visualização)
        if (this.latManual) {
            this.latManual.value = fLat;
            console.log('Input manual LAT atualizado:', this.latManual.value);
        }
        if (this.lngManual) {
            this.lngManual.value = fLng;
            console.log('Input manual LNG atualizado:', this.lngManual.value);
        }

        // 3. Atualiza displays visuais
        this.updateDisplays(fLat, fLng);

        // 4. Move marcador
        if (this.marker) {
            this.marker.setLatLng([lat, lng]);
            console.log('Marcador movido para nova posição');
        }

        // 5. Move mapa se necessário
        if (moveMap && this.map) {
            const zoomLevel = forceZoom ||
                ((this.addressInput && this.addressInput.value && this.addressInput.value.length > 2) ? 18 : 14);

            this.map.flyTo([lat, lng], zoomLevel, {
                animate: true,
                duration: 1.5
            });
            console.log('Mapa movido para nova localização, zoom:', zoomLevel);
        }

        // 6. Faz geocodificação reversa
        if (updateAddress) {
            console.log('Iniciando geocodificação reversa...');
            this.reverseGeocode(lat, lng);
        }
    }

    updateDisplays(lat, lng) {
        // Atualiza displays visuais
        const latDisplay = document.getElementById(`display-${this.config.mapId}-lat`);
        const lngDisplay = document.getElementById(`display-${this.config.mapId}-lng`);

        if (latDisplay) {
            latDisplay.innerText = lat;
            console.log('Display LAT atualizado:', lat);
        }
        if (lngDisplay) {
            lngDisplay.innerText = lng;
            console.log('Display LNG atualizado:', lng);
        }
    }

    searchAddress() {
        const city = this.cityInput ? this.cityInput.value.trim() : '';
        const state = this.stateInput ? this.stateInput.value.trim() : '';
        const district = this.districtInput ? this.districtInput.value.trim() : '';
        const street = this.addressInput ? this.addressInput.value.trim() : '';

        console.log('Buscando endereço:', {city, state, district, street});

        // Verifica se tem dados suficientes para buscar
        const hasEnoughData = (city.length >= 2 || street.length >= 3 || district.length >= 3 || state.length >= 2);
        if (!hasEnoughData) {
            console.log('Dados insuficientes para busca');
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
        console.log('Query de busca:', query);

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
                    console.log('Endereço encontrado:', data[0]);
                    const addr = data[0].address;

                    // Valida cidade se foi especificada
                    if (city) {
                        const foundCity = (addr.city || addr.town || addr.municipality || addr.village || '').toLowerCase();
                        const searchCity = city.toLowerCase();
                        if (foundCity && !foundCity.includes(searchCity) && !searchCity.includes(foundCity)) {
                            console.warn('Cidade encontrada não corresponde à cidade buscada:', foundCity, 'vs', searchCity);
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
                } else {
                    console.warn('Nenhum resultado encontrado para a busca');
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                if (error.name === 'AbortError') {
                    console.error('Busca de endereço timeout após', timeout, 'ms');
                } else {
                    console.error('Erro na busca de endereço:', error);
                }
            });
    }

    // Método para forçar atualização de coordenadas (útil para debug)
    forceUpdateCoordinates(lat, lng) {
        this.updateLocation(lat, lng, true, true);
    }
}

// Inicialização condicional com melhor tratamento de erros
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM carregado, verificando configuração do mapa...');

    const mapElement = document.getElementById('map-institution');

    if (!mapElement) {
        console.error('Elemento do mapa (map-institution) não encontrado');
        return;
    }

    if (!window.institutionMapConfig) {
        console.error('Configuração do mapa (window.institutionMapConfig) não encontrada');
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Biblioteca Leaflet (L) não carregada');
        return;
    }

    console.log('Configuração do mapa encontrada:', window.institutionMapConfig);

    try {
        const map = new InstitutionMap(window.institutionMapConfig);
        window.institutionMapInstance = map;
        console.log('Mapa inicializado com sucesso:', map);
    } catch (error) {
        console.error('Erro ao inicializar o mapa:', error);
    }
});

// Expor a classe globalmente para acesso via console
if (typeof window !== 'undefined') {
    window.InstitutionMap = InstitutionMap;
}
