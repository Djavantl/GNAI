// resources/js/pages/inclusive-radar/locations.js

class LocationMap {
    constructor(config) {
        this.config = config;
        this.map = null;
        this.currentMarker = null; // Marcador da localização sendo criada/editada
        this.institutionMarker = null; // Marcador da instituição
        this.existingMarkers = L.layerGroup(); // Grupo para marcadores existentes
        this.initialized = false;

        console.log('Inicializando LocationMap com config:', config);

        this.init();
    }

    init() {
        if (this.initialized) return;

        console.log('Inicializando LocationMap...');

        // Elementos do DOM
        this.mapContainer = document.getElementById(this.config.mapId);
        this.container = document.getElementById(`leaflet-container-${this.config.mapId}`);

        if (!this.mapContainer || !this.container) {
            console.error(`Elementos do mapa não encontrados para ${this.config.mapId}`);
            return;
        }

        // Elementos de entrada
        this.latInput = document.getElementById('lat');
        this.lngInput = document.getElementById('lng');
        this.institutionSelect = document.getElementById('institution_select');

        console.log('Inputs encontrados:', {
            latInput: this.latInput ? 'Sim' : 'Não',
            lngInput: this.lngInput ? 'Sim' : 'Não',
            institutionSelect: this.institutionSelect ? 'Sim' : 'Não'
        });

        // Elementos manuais
        this.latManual = document.getElementById('lat_manual');
        this.lngManual = document.getElementById('lng_manual');

        // Coordenadas iniciais - PRIORIZAR A LOCALIZAÇÃO se estiver no modo de edição
        let initialLat = this.config.lat;
        let initialLng = this.config.lng;

        console.log('Configuração inicial:', {
            configLat: this.config.lat,
            configLng: this.config.lng,
            isEditMode: this.config.isEditMode,
            location: this.config.location
        });

        // Se estiver editando e tiver uma localização, use as coordenadas da localização
        if (this.config.isEditMode && this.config.location) {
            initialLat = this.config.location.latitude;
            initialLng = this.config.location.longitude;
            console.log('Usando coordenadas da localização:', initialLat, initialLng);
        } else if (this.latInput && this.latInput.value && !isNaN(parseFloat(this.latInput.value))) {
            // Senão, use os valores dos inputs hidden
            initialLat = parseFloat(this.latInput.value);
            initialLng = parseFloat(this.lngInput.value);
            console.log('Usando coordenadas dos inputs:', initialLat, initialLng);
        }

        const initialZoom = this.config.zoom;

        console.log('Coordenadas finais iniciais:', initialLat, initialLng, initialZoom);

        // Cria mapa
        this.createMap(initialLat, initialLng, initialZoom);

        // Adiciona o grupo de marcadores existentes ao mapa
        this.existingMarkers.addTo(this.map);

        // Configura eventos do mapa
        this.setupMapEvents();
        this.setupManualInputEvents();

        // Se houver uma instituição no config, plota a instituição e suas localizações
        if (this.config.institution) {
            this.plotInstitutionAndLocations(this.config.institution);
        }

        // Se estiver no modo de edição, plota a localização atual
        if (this.config.isEditMode && this.config.location) {
            console.log('Plotando localização atual para edição:', this.config.location);
            this.plotCurrentLocation(this.config.location);
        } else {
            // Se não estiver editando, cria um marcador na posição inicial
            console.log('Criando marcador inicial');
            this.setupMarker(initialLat, initialLng);
        }

        // Sincroniza os valores iniciais
        this.updateInputs(initialLat, initialLng);

        // Redimensiona o mapa
        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize();
                console.log('Mapa redimensionado');
            }
        }, 100);

        this.initialized = true;
        console.log('LocationMap inicializado com sucesso');
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

        if (this.currentMarker) {
            this.currentMarker.setLatLng([lat, lng]);
            console.log('Marcador movido para nova posição');
        } else {
            this.currentMarker = L.marker([lat, lng], {
                draggable: true,
                icon: blueIcon
            }).addTo(this.map);

            this.currentMarker.bindTooltip("Localização Selecionada", {
                permanent: true,
                direction: 'top',
                offset: [0, -35],
                className: 'bg-primary text-white border-0 fw-bold rounded shadow-sm px-2 py-1'
            });

            // Evento de arrastar
            this.currentMarker.on('dragend', () => {
                const pos = this.currentMarker.getLatLng();
                console.log('Marcador arrastado para:', pos.lat, pos.lng);
                this.updateLocation(pos.lat, pos.lng, false);
            });

            console.log('Marcador criado e configurado');
        }
    }

    setupMapEvents() {
        console.log('Configurando eventos do mapa');

        // Evento de clique no mapa
        this.map.on('click', (e) => {
            console.log('Mapa clicado em:', e.latlng.lat, e.latlng.lng);
            // Verifica se há uma instituição selecionada
            if (!this.institutionSelect || !this.institutionSelect.value) {
                alert('Por favor, selecione uma instituição base primeiro.');
                return;
            }
            this.updateLocation(e.latlng.lat, e.latlng.lng, false);
        });
    }

    setupManualInputEvents() {
        console.log('Configurando eventos de inputs manuais');

        // Eventos para inputs manuais de lat/lng
        if (this.latManual) {
            this.latManual.addEventListener('change', () => {
                const lat = parseFloat(this.latManual.value);
                if (!isNaN(lat)) {
                    const currentLng = this.currentMarker ? this.currentMarker.getLatLng().lng : this.lngManual.value;
                    console.log('Input manual LAT alterado para:', lat);
                    this.updateLocation(lat, currentLng, true);
                }
            });

            // Sincroniza input manual com hidden
            this.latManual.addEventListener('input', () => {
                if (this.latInput) {
                    this.latInput.value = this.latManual.value;
                }
            });
        }

        if (this.lngManual) {
            this.lngManual.addEventListener('change', () => {
                const lng = parseFloat(this.lngManual.value);
                if (!isNaN(lng)) {
                    const currentLat = this.currentMarker ? this.currentMarker.getLatLng().lat : this.latManual.value;
                    console.log('Input manual LNG alterado para:', lng);
                    this.updateLocation(currentLat, lng, true);
                }
            });

            // Sincroniza input manual com hidden
            this.lngManual.addEventListener('input', () => {
                if (this.lngInput) {
                    this.lngInput.value = this.lngManual.value;
                }
            });
        }
    }

    updateInputs(lat, lng) {
        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);

        console.log('Atualizando inputs para:', fLat, fLng);

        // Atualiza inputs hidden
        if (this.latInput) {
            this.latInput.value = fLat;
            console.log('Input hidden LAT atualizado:', this.latInput.value);
        }
        if (this.lngInput) {
            this.lngInput.value = fLng;
            console.log('Input hidden LNG atualizado:', this.lngInput.value);
        }

        // Atualiza inputs manuais
        if (this.latManual) {
            this.latManual.value = fLat;
            console.log('Input manual LAT atualizado:', this.latManual.value);
        }
        if (this.lngManual) {
            this.lngManual.value = fLng;
            console.log('Input manual LNG atualizado:', this.lngManual.value);
        }

        // Atualiza displays
        const latDisplay = document.getElementById(`display-${this.config.mapId}-lat`);
        const lngDisplay = document.getElementById(`display-${this.config.mapId}-lng`);
        if (latDisplay) {
            latDisplay.innerText = fLat;
            console.log('Display LAT atualizado:', fLat);
        }
        if (lngDisplay) {
            lngDisplay.innerText = fLng;
            console.log('Display LNG atualizado:', fLng);
        }
    }

    updateLocation(lat, lng, moveMap = false) {
        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);

        console.log('Atualizando localização para:', fLat, fLng);

        // Atualiza todos os inputs
        this.updateInputs(lat, lng);

        // Move marcador
        this.setupMarker(lat, lng);

        // Move mapa se necessário
        if (moveMap && this.map) {
            this.map.flyTo([lat, lng], 18, {
                animate: true,
                duration: 1.5
            });
            console.log('Mapa movido para nova localização');
        }
    }

    plotInstitutionAndLocations(institution) {
        console.log('Plotando instituição e localizações:', institution);

        // Remove marcador da instituição anterior
        if (this.institutionMarker) {
            this.map.removeLayer(this.institutionMarker);
        }

        // Remove marcadores existentes
        this.existingMarkers.clearLayers();

        // Plota a instituição
        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            shadowSize: [41, 41],
            popupAnchor: [1, -34]
        });

        this.institutionMarker = L.marker([institution.latitude, institution.longitude], {
            icon: redIcon
        }).addTo(this.map);

        this.institutionMarker.bindTooltip(`Sede: ${institution.name}`, {
            permanent: false,
            direction: 'top',
            className: 'bg-danger text-white border-0 fw-bold rounded shadow-sm px-2 py-1',
            offset: [0, -35]
        });

        console.log('Marcador da instituição plotado');

        // Plota as localizações existentes da instituição
        if (institution.locations && institution.locations.length > 0) {
            console.log('Plotando', institution.locations.length, 'localizações existentes');

            const greyIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [20, 32],
                iconAnchor: [10, 32],
                shadowSize: [32, 32]
            });

            institution.locations.forEach(location => {
                // Se estiver no modo de edição, não plota a localização que está sendo editada
                if (this.config.isEditMode && this.config.location && this.config.location.id === location.id) {
                    console.log('Pulando localização atual (em edição):', location.name);
                    return;
                }

                const marker = L.marker([location.latitude, location.longitude], {
                    icon: greyIcon
                }).addTo(this.existingMarkers);

                marker.bindTooltip(`${location.name} (${location.type || 'Sem tipo'})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-secondary text-white border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            });
        }

        // Move o mapa para a instituição (apenas se não estiver no modo de edição)
        if (!this.config.isEditMode) {
            console.log('Movendo mapa para instituição');
            this.map.flyTo([institution.latitude, institution.longitude], institution.default_zoom || 16, {
                animate: true,
                duration: 2
            });

            // Atualiza a localização atual para a da instituição
            this.updateLocation(institution.latitude, institution.longitude, false);
        } else {
            console.log('Modo de edição: não movendo mapa para instituição');
        }
    }

    plotCurrentLocation(location) {
        console.log('Plotando localização atual para edição:', location);

        // Plota a localização atual no mapa
        this.updateLocation(location.latitude, location.longitude, false);

        // Move o mapa para a localização
        this.map.flyTo([location.latitude, location.longitude], 18, {
            animate: true,
            duration: 2
        });

        console.log('Localização atual plotada e mapa movido');
    }
}

// Inicialização condicional
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM carregado, verificando configuração do mapa de localização...');

    const mapElement = document.getElementById('map-location');

    if (!mapElement) {
        console.error('Elemento do mapa (map-location) não encontrado');
        return;
    }

    if (!window.locationMapConfig) {
        console.error('Configuração do mapa (window.locationMapConfig) não encontrada');
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Biblioteca Leaflet (L) não carregada');
        return;
    }

    console.log('Configuração do mapa encontrada:', window.locationMapConfig);

    try {
        const map = new LocationMap(window.locationMapConfig);
        window.locationMapInstance = map;
        console.log('Mapa de localização inicializado com sucesso:', map);

        // Adiciona evento de change no select de instituição
        const institutionSelect = document.getElementById('institution_select');
        if (institutionSelect) {
            institutionSelect.addEventListener('change', function() {
                console.log('Instituição alterada para:', this.value);
                const selectedInstitutionId = this.value;
                if (window.institutionsData) {
                    const selectedInstitution = window.institutionsData.find(inst => inst.id == selectedInstitutionId);
                    if (selectedInstitution) {
                        console.log('Instituição encontrada:', selectedInstitution);
                        // Atualiza o mapa com a nova instituição
                        map.plotInstitutionAndLocations(selectedInstitution);
                    }
                }
            });
        }

    } catch (error) {
        console.error('Erro ao inicializar o mapa de localização:', error);
    }
});

// Expor a classe globalmente para acesso via console
if (typeof window !== 'undefined') {
    window.LocationMap = LocationMap;
}
