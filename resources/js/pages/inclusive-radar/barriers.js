// Mapeamento de status para labels (usado nos tooltips das barreiras existentes)
const barrierStatusLabels = {
    'identified': 'Identificada',
    'under_analysis': 'Em Análise',
    'in_progress': 'Em Tratamento',
    'resolved': 'Resolvida',
    'not_applicable': 'Não Aplicável'
};

class FormManager {
    constructor(config) {
        this.config = config;
        this.initialized = false;

        // Elementos do formulário
        this.institutionSelect = document.getElementById('institution_select');
        this.locationSelect = document.getElementById('location_select');
        this.noLocationCheck = document.getElementById('no_location');
        this.locationWrapper = document.getElementById('location_wrapper');
        this.isAnonymousCheck = document.getElementById('is_anonymous');
        this.notApplicableCheck = document.getElementById('not_applicable');
        this.wrapperNotApplicable = document.getElementById('wrapper_not_applicable');
        this.identificationFields = document.getElementById('identification_fields');
        this.personSelects = document.getElementById('person_selects');
        this.manualPersonData = document.getElementById('manual_person_data');
        this.affectedStudentSelect = document.querySelector('select[name="affected_student_id"]');
        this.affectedProfessionalSelect = document.querySelector('select[name="affected_professional_id"]');
        this.affectedPersonNameInput = document.querySelector('input[name="affected_person_name"]');
        this.affectedPersonRoleInput = document.querySelector('input[name="affected_person_role"]');

        this.init();
    }

    init() {
        if (this.initialized) return;
        console.log('Inicializando FormManager...');

        this.setupEventListeners();
        this.applyInitialState();

        this.initialized = true;
        console.log('FormManager inicializado');
    }

    setupEventListeners() {
        if (this.institutionSelect) {
            this.institutionSelect.addEventListener('change', () => this.handleInstitutionChange());
        }
        if (this.locationSelect) {
            this.locationSelect.addEventListener('change', () => this.handleLocationSelectChange());
        }
        if (this.noLocationCheck) {
            this.noLocationCheck.addEventListener('change', () => this.handleNoLocationChange());
        }
        if (this.isAnonymousCheck) {
            this.isAnonymousCheck.addEventListener('change', () => this.togglePersonFields());
        }
        if (this.notApplicableCheck) {
            this.notApplicableCheck.addEventListener('change', () => this.togglePersonFields());
        }
    }

    applyInitialState() {
        // Se houver uma instituição pré-selecionada (old), carrega suas localizações
        if (this.institutionSelect && this.institutionSelect.value) {
            setTimeout(() => this.handleInstitutionChange(), 200);
        }

        this.handleNoLocationChange();
        this.togglePersonFields();

        // Se houver oldLocationId, seleciona no dropdown após as opções serem carregadas
        if (window.oldLocationId && this.locationSelect) {
            const checkInterval = setInterval(() => {
                if (this.locationSelect.options.length > 1) { // já tem opções carregadas
                    this.locationSelect.value = window.oldLocationId;
                    if (this.locationSelect.value === window.oldLocationId) {
                        this.handleLocationSelectChange();
                    }
                    clearInterval(checkInterval);
                }
            }, 100);
        }
    }

    loadInstitutionLocations(institutionId) {
        console.log('Carregando localizações da instituição:', institutionId);
        if (!window.institutionsData) return;

        const institution = window.institutionsData.find(inst => inst.id == institutionId);
        if (!institution || !this.locationSelect) return;

        // Limpa opções atuais (mantém apenas a primeira)
        this.locationSelect.innerHTML = '<option value="">Selecione um local...</option>';

        if (institution.locations && institution.locations.length > 0) {
            institution.locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = location.name;
                option.dataset.lat = location.latitude;
                option.dataset.lng = location.longitude;
                this.locationSelect.appendChild(option);
            });
            console.log(`${institution.locations.length} localizações carregadas.`);
        } else {
            console.log('Instituição não tem localizações cadastradas.');
        }
    }

    handleInstitutionChange() {
        const institutionId = this.institutionSelect.value;
        console.log('Instituição alterada para:', institutionId);

        if (institutionId) {
            if (!this.noLocationCheck.checked && this.locationWrapper) {
                this.locationWrapper.classList.remove('d-none');
            }
            this.loadInstitutionLocations(institutionId);

            if (window.barrierMapInstance && window.institutionsData) {
                const selectedInstitution = window.institutionsData.find(inst => inst.id == institutionId);
                if (selectedInstitution) {
                    window.barrierMapInstance.plotInstitutionAndData(selectedInstitution);
                    // Move o marcador azul para a instituição (se nenhuma localização específica)
                    if (!this.locationSelect.value) {
                        window.barrierMapInstance.updateLocation(
                            selectedInstitution.latitude,
                            selectedInstitution.longitude,
                            true
                        );
                    }
                }
            }
        } else {
            if (this.locationWrapper) this.locationWrapper.classList.add('d-none');
            if (this.locationSelect) {
                this.locationSelect.innerHTML = '<option value="">Selecione um local...</option>';
            }
            if (window.barrierMapInstance) {
                window.barrierMapInstance.clearLayers();
            }
        }
    }

    handleLocationSelectChange() {
        const selectedOption = this.locationSelect.selectedOptions[0];
        if (selectedOption && selectedOption.value && window.barrierMapInstance) {
            const lat = selectedOption.dataset.lat;
            const lng = selectedOption.dataset.lng;
            if (lat && lng) {
                window.barrierMapInstance.updateLocation(parseFloat(lat), parseFloat(lng), true);
            }
        }
    }

    handleNoLocationChange() {
        const isChecked = this.noLocationCheck.checked;
        if (isChecked) {
            if (this.locationWrapper) this.locationWrapper.classList.add('d-none');
            if (this.locationSelect) this.locationSelect.value = '';
            if (window.barrierMapInstance) {
                window.barrierMapInstance.disableMap();
            }
        } else {
            if (this.institutionSelect && this.institutionSelect.value && this.locationWrapper) {
                this.locationWrapper.classList.remove('d-none');
            }
            if (window.barrierMapInstance) {
                window.barrierMapInstance.enableMap();
                if (this.institutionSelect && this.institutionSelect.value) {
                    setTimeout(() => this.handleInstitutionChange(), 100);
                }
            }
        }
    }

    clearPersonFields() {
        if (this.affectedPersonNameInput) this.affectedPersonNameInput.value = '';
        if (this.affectedPersonRoleInput) this.affectedPersonRoleInput.value = '';
        if (this.affectedStudentSelect) this.affectedStudentSelect.value = '';
        if (this.affectedProfessionalSelect) this.affectedProfessionalSelect.value = '';
    }

    togglePersonFields() {
        const isAnonymous = this.isAnonymousCheck?.checked || false;
        const notApplicable = this.notApplicableCheck?.checked || false;

        if (isAnonymous) {
            if (this.wrapperNotApplicable) this.wrapperNotApplicable.classList.add('d-none');
            if (this.identificationFields) this.identificationFields.classList.add('d-none');
            if (this.notApplicableCheck) this.notApplicableCheck.checked = false;
            this.clearPersonFields();
        } else {
            if (this.wrapperNotApplicable) this.wrapperNotApplicable.classList.remove('d-none');
            if (this.identificationFields) this.identificationFields.classList.remove('d-none');

            if (notApplicable) {
                if (this.personSelects) this.personSelects.classList.add('d-none');
                if (this.manualPersonData) this.manualPersonData.classList.remove('d-none');
                if (this.affectedStudentSelect) this.affectedStudentSelect.value = '';
                if (this.affectedProfessionalSelect) this.affectedProfessionalSelect.value = '';
            } else {
                if (this.personSelects) this.personSelects.classList.remove('d-none');
                if (this.manualPersonData) this.manualPersonData.classList.add('d-none');
                if (this.affectedPersonNameInput) this.affectedPersonNameInput.value = '';
                if (this.affectedPersonRoleInput) this.affectedPersonRoleInput.value = '';
            }
        }
    }
}

class BarrierMap {
    constructor(config) {
        this.config = config;
        this.map = null;
        this.currentMarker = null;
        this.institutionMarker = null;
        this.existingLocationsLayer = L.layerGroup();
        this.existingBarriersLayer = L.layerGroup();
        this.initialized = false;

        console.log('Inicializando BarrierMap com config:', config);
        this.init();
    }

    init() {
        if (this.initialized) return;

        this.mapContainer = document.getElementById(this.config.mapId);
        if (!this.mapContainer) {
            console.error(`Map container #${this.config.mapId} não encontrado.`);
            return;
        }

        // Inputs de latitude/longitude (agora com ids 'latitude' e 'longitude')
        this.latInput = document.getElementById('latitude');
        this.lngInput = document.getElementById('longitude');
        this.institutionSelect = document.getElementById('institution_select');
        this.noLocationCheckbox = document.getElementById('no_location');

        // Coordenadas iniciais
        let initialLat = this.config.lat || -14.235;
        let initialLng = this.config.lng || -51.9253;
        let initialZoom = this.config.zoom || 5;

        if (this.latInput && this.latInput.value && !isNaN(parseFloat(this.latInput.value))) {
            initialLat = parseFloat(this.latInput.value);
            initialLng = parseFloat(this.lngInput.value);
            initialZoom = 18;
        }

        this.createMap(initialLat, initialLng, initialZoom);
        this.existingLocationsLayer.addTo(this.map);
        this.existingBarriersLayer.addTo(this.map);
        this.setupMapEvents();

        if (this.config.institution) {
            this.plotInstitutionAndData(this.config.institution);
        }

        this.setupMarker(initialLat, initialLng);
        this.updateInputs(initialLat, initialLng);

        if (this.noLocationCheckbox && this.noLocationCheckbox.checked) {
            this.disableMap();
        }

        setTimeout(() => this.map?.invalidateSize(), 200);
        this.initialized = true;
        console.log('BarrierMap inicializado');
    }

    createMap(lat, lng, zoom) {
        const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        });
        const googleSatellite = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '© Google Maps',
            maxZoom: 21
        });

        this.map = L.map(this.config.mapId, {
            center: [lat, lng],
            zoom: zoom,
            layers: [streetLayer],
            zoomControl: true,
            scrollWheelZoom: true
        });

        L.control.layers({
            "Mapa de Ruas (OSM)": streetLayer,
            "Satélite (Google)": googleSatellite
        }).addTo(this.map);
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

        if (this.currentMarker) {
            this.currentMarker.setLatLng([lat, lng]);
        } else {
            this.currentMarker = L.marker([lat, lng], {
                draggable: true,
                icon: blueIcon
            }).addTo(this.map);

            this.currentMarker.bindTooltip("Localização da Barreira (Arraste para ajustar)", {
                permanent: true,
                direction: 'top',
                offset: [0, -35],
                className: 'bg-primary text-white border-0 fw-bold rounded shadow-sm px-2 py-1'
            });

            this.currentMarker.on('dragend', () => {
                const pos = this.currentMarker.getLatLng();
                this.updateLocation(pos.lat, pos.lng, false);
            });
        }
    }

    setupMapEvents() {
        this.map.on('click', (e) => {
            if (this.noLocationCheckbox && this.noLocationCheckbox.checked) {
                alert('Desmarque "Sem localização física" para definir uma localização.');
                return;
            }
            if (!this.institutionSelect || !this.institutionSelect.value) {
                alert('Selecione uma instituição primeiro.');
                return;
            }
            this.updateLocation(e.latlng.lat, e.latlng.lng, false);
        });
    }

    updateInputs(lat, lng) {
        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);
        if (this.latInput) this.latInput.value = fLat;
        if (this.lngInput) this.lngInput.value = fLng;
    }

    updateLocation(lat, lng, moveMap = false) {
        this.setupMarker(lat, lng);
        this.updateInputs(lat, lng);

        const displayLat = document.getElementById('display-map-barrier-lat');
        const displayLng = document.getElementById('display-map-barrier-lng');
        if (displayLat) displayLat.innerText = parseFloat(lat).toFixed(8);
        if (displayLng) displayLng.innerText = parseFloat(lng).toFixed(8);

        if (moveMap && this.map) {
            this.map.flyTo([lat, lng], 18, { animate: true, duration: 1.5 });
        }
    }

    getBarrierStatus(barrier) {
        if (barrier.inspections && barrier.inspections.length > 0) {
            const inspections = [...barrier.inspections].sort((a, b) => new Date(b.inspection_date) - new Date(a.inspection_date));
            const latest = inspections[0];
            if (latest && latest.status) return barrierStatusLabels[latest.status] || 'Sem status';
        }
        return barrier.status ? barrierStatusLabels[barrier.status] || 'Sem status' : 'Sem status';
    }

    plotInstitutionAndData(institution) {
        if (this.institutionMarker) this.map.removeLayer(this.institutionMarker);
        this.existingLocationsLayer.clearLayers();
        this.existingBarriersLayer.clearLayers();

        if (!institution.latitude || !institution.longitude) {
            console.warn('Instituição sem coordenadas.');
            return;
        }

        // Marcador vermelho da instituição
        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            shadowSize: [41, 41],
            popupAnchor: [1, -34]
        });
        this.institutionMarker = L.marker([institution.latitude, institution.longitude], { icon: redIcon })
            .addTo(this.map)
            .bindTooltip(`Sede: ${institution.name}`, { direction: 'top', className: 'bg-danger text-white' });

        // Localizações (cinza)
        if (institution.locations && institution.locations.length > 0) {
            const greyIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [20, 32],
                iconAnchor: [10, 32],
                shadowSize: [32, 32]
            });
            institution.locations.forEach(loc => {
                if (loc.latitude && loc.longitude) {
                    L.marker([loc.latitude, loc.longitude], { icon: greyIcon })
                        .addTo(this.existingLocationsLayer)
                        .bindTooltip(loc.name, { className: 'bg-secondary text-white' });
                }
            });
        }

        // Barreiras existentes (amarelo)
        if (institution.barriers && institution.barriers.length > 0) {
            const yellowIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [20, 32],
                iconAnchor: [10, 32],
                shadowSize: [32, 32]
            });
            institution.barriers.forEach(barrier => {
                if (barrier.latitude && barrier.longitude) {
                    // Pula a barreira atual se estiver em edição (aqui nunca ocorre porque é criação)
                    const status = this.getBarrierStatus(barrier);
                    L.marker([barrier.latitude, barrier.longitude], { icon: yellowIcon })
                        .addTo(this.existingBarriersLayer)
                        .bindTooltip(`${barrier.name} (${status})`, { className: 'bg-warning text-dark' });
                }
            });
        }

        // Move o mapa para a instituição se não houver marcador posicionado
        if (!this.currentMarker || !this.currentMarker.getLatLng) {
            this.map.flyTo([institution.latitude, institution.longitude], institution.default_zoom || 16);
        }
    }

    clearLayers() {
        this.existingLocationsLayer.clearLayers();
        this.existingBarriersLayer.clearLayers();
        if (this.institutionMarker) {
            this.map.removeLayer(this.institutionMarker);
            this.institutionMarker = null;
        }
    }

    disableMap() {
        if (this.map) {
            this.map.dragging.disable();
            this.map.touchZoom.disable();
            this.map.doubleClickZoom.disable();
            this.map.scrollWheelZoom.disable();
            this.map.boxZoom.disable();
            this.map.keyboard.disable();
            this.map._container.style.cursor = 'not-allowed';
            this.map._container.style.opacity = '0.5';
        }
        if (this.currentMarker) {
            this.map.removeLayer(this.currentMarker);
            this.currentMarker = null;
        }
        if (this.latInput) this.latInput.value = '';
        if (this.lngInput) this.lngInput.value = '';
    }

    enableMap() {
        if (this.map) {
            this.map.dragging.enable();
            this.map.touchZoom.enable();
            this.map.doubleClickZoom.enable();
            this.map.scrollWheelZoom.enable();
            this.map.boxZoom.enable();
            this.map.keyboard.enable();
            this.map._container.style.cursor = '';
            this.map._container.style.opacity = '1';
        }
        if (this.institutionSelect && this.institutionSelect.value && window.institutionsData) {
            const inst = window.institutionsData.find(i => i.id == this.institutionSelect.value);
            if (inst) setTimeout(() => this.plotInstitutionAndData(inst), 100);
        }
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('map-barrier')) {
        console.error('Elemento map-barrier não encontrado.');
        return;
    }
    if (!window.barrierMapConfig) {
        console.error('window.barrierMapConfig não definido.');
        return;
    }
    if (typeof L === 'undefined') {
        console.error('Leaflet não carregado.');
        return;
    }

    try {
        window.barrierMapInstance = new BarrierMap(window.barrierMapConfig);
        window.formManagerInstance = new FormManager(window.barrierMapConfig);
    } catch (error) {
        console.error('Erro na inicialização:', error);
    }
});

// Expor classes globalmente
window.BarrierMap = BarrierMap;
window.FormManager = FormManager;
