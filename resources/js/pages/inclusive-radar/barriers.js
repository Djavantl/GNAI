// assistive-technologies.js (refatorado)
// Depende de: Leaflet (L), window.institutionsData, window.barrierMapConfig, elementos com IDs usados no Blade
// Ative debug = true se quiser logs
const debug = false;

const log = (...args) => { if (debug) console.log(...args); };

// Mapeamento de status para labels
const barrierStatusLabels = {
    identified: 'Identificada',
    under_analysis: 'Em Análise',
    in_progress: 'Em Tratamento',
    resolved: 'Resolvida',
    not_applicable: 'Não Aplicável'
};

/* ============================
   Utilitários
   ============================ */
const $ = id => document.getElementById(id);
const on = (el, ev, fn) => { if (el) el.addEventListener(ev, fn); };
const exists = el => !!el;
const isValidCoord = v => v !== null && v !== undefined && !isNaN(parseFloat(v));
const safeParse = v => (isValidCoord(v) ? parseFloat(v) : null);
const createOption = (value, text, dataset = {}) => {
    const o = document.createElement('option');
    o.value = value ?? '';
    o.textContent = text ?? '';
    Object.keys(dataset).forEach(k => o.setAttribute(`data-${k}`, dataset[k]));
    return o;
};
const populateSelect = (selectEl, items = [], { valueKey = 'id', labelKey = 'name', datasetMap = {} } = {}) => {
    if (!selectEl) return;
    selectEl.innerHTML = '<option value="">Selecione um local...</option>';
    items.forEach(it => {
        const opt = createOption(it[valueKey], it[labelKey], Object.fromEntries(Object.entries(datasetMap).map(([dk, fk]) => [dk, it[fk]])));
        selectEl.appendChild(opt);
    });
};

const createColorIcon = (color = 'blue', size = [25, 41], anchor = [12, 41], shadowDimensions = null) => {
    const mapUrl = {
        blue: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        red: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        grey: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
        yellow: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png'
    };

    // Se a dimensão da sombra não for passada, ela acompanha a proporção do ícone (ex: altura x altura)
    const currentShadowSize = shadowDimensions || [size[1], size[1]];

    // A âncora da sombra também precisa acompanhar para não ficar "descolada" do pino
    const currentShadowAnchor = [size[1] / 3, size[1]];

    return L.icon({
        iconUrl: mapUrl[color] || mapUrl.blue,
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: size,
        iconAnchor: anchor,
        shadowSize: currentShadowSize,
        shadowAnchor: currentShadowAnchor, // Ajusta o posicionamento da sombra
        popupAnchor: [1, -34]
    });
};

/* ============================
   FormManager: gerencia comportamento do formulário
   ============================ */
class FormManager {
    constructor(config = {}) {
        this.config = config || {};
        // elementos usados no template
        this.institutionSelect = $('institution_select');
        this.locationSelect = $('location_select');
        this.noLocationCheck = $('no_location');
        this.locationWrapper = $('location_wrapper');
        this.isAnonymousCheck = $('is_anonymous');
        this.notApplicableCheck = $('not_applicable');
        this.wrapperNotApplicable = $('wrapper_not_applicable');
        this.identificationFields = $('identification_fields');
        this.personSelects = $('person_selects');
        this.manualPersonData = $('manual_person_data');
        this.affectedPersonNameInput = document.querySelector('input[name="affected_person_name"]');
        this.affectedPersonRoleInput = document.querySelector('input[name="affected_person_role"]');

        this._boundHandleInstitutionChange = this.handleInstitutionChange.bind(this);
        this._boundTogglePersonFields = this.togglePersonFields.bind(this);

        this.init();
    }

    init() {
        log('FormManager.init');
        this.setupEventListeners();
        this.applyInitialState();
    }

    setupEventListeners() {
        if (this.institutionSelect) on(this.institutionSelect, 'change', this._boundHandleInstitutionChange);
        if (this.locationSelect) on(this.locationSelect, 'change', () => this.handleLocationSelectChange());
        if (this.noLocationCheck) on(this.noLocationCheck, 'change', () => this.handleNoLocationChange());
        if (this.isAnonymousCheck) on(this.isAnonymousCheck, 'change', this._boundTogglePersonFields);
        if (this.notApplicableCheck) on(this.notApplicableCheck, 'change', this._boundTogglePersonFields);
    }

    applyInitialState() {
        log('FormManager.applyInitialState', this.config);
        // Se estiver em modo edição, preenche checks e dispara carregamento
        if (this.config.isEditMode && this.config.barrier) {
            const b = this.config.barrier;
            if (this.noLocationCheck) this.noLocationCheck.checked = (b.latitude === null && b.longitude === null);
            if (this.isAnonymousCheck) this.isAnonymousCheck.checked = !!b.is_anonymous;
            if (this.notApplicableCheck) this.notApplicableCheck.checked = !!b.not_applicable;
            if (this.institutionSelect && b.institution_id) {
                this.institutionSelect.value = b.institution_id;
                // carrega localizações da instituição
                setTimeout(() => this.handleInstitutionChange(), 100);
            }
        }

        // Aplica estado inicial dos campos
        this.handleNoLocationChange();
        this.togglePersonFields();

        // Caso exista oldLocationId (após falha de validação do servidor), seleciona
        if (window.oldLocationId && this.locationSelect) {
            setTimeout(() => {
                this.locationSelect.value = window.oldLocationId;
                if (this.locationSelect.value === window.oldLocationId) {
                    this.handleLocationSelectChange();
                }
            }, 800);
        }
    }

    async loadInstitutionLocations(institutionId) {
        log('loadInstitutionLocations', institutionId);
        if (!institutionId || !window.institutionsData || !this.locationSelect) return;

        const inst = window.institutionsData.find(i => String(i.id) === String(institutionId));
        if (!inst) return;

        populateSelect(this.locationSelect, inst.locations || [], { valueKey: 'id', labelKey: 'name', datasetMap: { lat: 'latitude', lng: 'longitude' } });

        // se em edição e tem location_id, seleciona
        if (this.config.isEditMode && this.config.barrier && this.config.barrier.location_id) {
            setTimeout(() => {
                this.locationSelect.value = this.config.barrier.location_id;
                if (this.locationSelect.value == this.config.barrier.location_id) {
                    this.handleLocationSelectChange();
                }
            }, 150);
        }
    }

    handleInstitutionChange() {
        const val = this.institutionSelect ? this.institutionSelect.value : null;
        log('handleInstitutionChange', val);
        if (!val) {
            if (this.locationWrapper) this.locationWrapper.classList.add('d-none');
            if (this.locationSelect) this.locationSelect.innerHTML = '<option value="">Selecione um local...</option>';
            return;
        }

        if (!this.noLocationCheck || !this.noLocationCheck.checked) {
            if (this.locationWrapper) this.locationWrapper.classList.remove('d-none');
        }
        this.loadInstitutionLocations(val);

        // Atualiza mapa se existir instância global
        if (window.barrierMapInstance && window.institutionsData) {
            const selInst = window.institutionsData.find(inst => String(inst.id) === String(val));
            if (selInst) window.barrierMapInstance.plotInstitutionAndData(selInst);
        }
    }

    handleLocationSelectChange() {
        if (!this.locationSelect) return;
        const opt = this.locationSelect.selectedOptions[0];
        if (!opt) return;
        log('handleLocationSelectChange', opt);
        const lat = opt.getAttribute('data-lat');
        const lng = opt.getAttribute('data-lng');
        if (isValidCoord(lat) && isValidCoord(lng) && window.barrierMapInstance) {
            window.barrierMapInstance.updateLocation(parseFloat(lat), parseFloat(lng), true);
        }
    }

    handleNoLocationChange() {
        const checked = this.noLocationCheck && this.noLocationCheck.checked;
        log('handleNoLocationChange', checked);
        if (checked) {
            if (this.locationWrapper) this.locationWrapper.classList.add('d-none');
            if (this.locationSelect) this.locationSelect.value = '';
            if (window.barrierMapInstance) window.barrierMapInstance.disableMap();
        } else {
            if (this.institutionSelect && this.institutionSelect.value && this.locationWrapper) {
                this.locationWrapper.classList.remove('d-none');
            }
            if (window.barrierMapInstance) {
                window.barrierMapInstance.enableMap();
                // recarrega localizações da instituição se necessário
                if (this.institutionSelect && this.institutionSelect.value) {
                    setTimeout(() => this.handleInstitutionChange(), 100);
                }
            }
        }
    }

    clearManualPersonFields() {
        if (this.affectedPersonNameInput) this.affectedPersonNameInput.value = '';
        if (this.affectedPersonRoleInput) this.affectedPersonRoleInput.value = '';
    }

    togglePersonFields() {
        const isAnonymous = this.isAnonymousCheck && this.isAnonymousCheck.checked;
        const notApplicable = this.notApplicableCheck && this.notApplicableCheck.checked;
        log('togglePersonFields', { isAnonymous, notApplicable });

        if (isAnonymous) {
            if (this.wrapperNotApplicable) this.wrapperNotApplicable.classList.add('d-none');
            if (this.identificationFields) this.identificationFields.classList.add('d-none');
            if (this.notApplicableCheck) this.notApplicableCheck.checked = false;
            this.clearManualPersonFields();
            return;
        }

        if (this.wrapperNotApplicable) this.wrapperNotApplicable.classList.remove('d-none');
        if (this.identificationFields) this.identificationFields.classList.remove('d-none');

        if (notApplicable) {
            if (this.personSelects) this.personSelects.classList.add('d-none');
            if (this.manualPersonData) this.manualPersonData.classList.remove('d-none');
        } else {
            if (this.personSelects) this.personSelects.classList.remove('d-none');
            if (this.manualPersonData) {
                this.manualPersonData.classList.add('d-none');
                this.clearManualPersonFields();
            }
        }
    }
}

/* ============================
   BarrierMap: gerencia o Leaflet e marcadores
   ============================ */
class BarrierMap {
    constructor(config = {}) {
        this.config = config;
        this.map = null;
        this.currentMarker = null;
        this.institutionMarker = null;
        this.existingLocationsLayer = L.layerGroup();
        this.existingBarriersLayer = L.layerGroup();
        this.initialized = false;

        this.latInput = null;
        this.lngInput = null;
        this.institutionSelect = null;
        this.noLocationCheckbox = null;

        // NOVO: elementos de exibição das coordenadas
        this.displayLat = document.getElementById('display-map-barrier-lat');
        this.displayLng = document.getElementById('display-map-barrier-lng');

        this.init();
    }

    init() {
        log('BarrierMap.init', this.config);
        // DOM container IDs
        this.mapContainer = $ ? $('map-barrier') : null;
        this.container = $ ? $(`leaflet-container-${this.config.mapId}`) : null;
        if (!this.mapContainer || !this.container) {
            console.error('Elemento do mapa ou container não encontrado.');
            return;
        }

        // inputs
        this.latInput = $('lat');
        this.lngInput = $('lng');
        this.institutionSelect = $('institution_select');
        this.noLocationCheckbox = $('no_location');

        // coordenadas iniciais
        let lat = this.config.lat ?? 0;
        let lng = this.config.lng ?? 0;

        if (this.config.isEditMode && this.config.barrier && isValidCoord(this.config.barrier.latitude) && isValidCoord(this.config.barrier.longitude)) {
            lat = this.config.barrier.latitude;
            lng = this.config.barrier.longitude;
        } else if (this.latInput && isValidCoord(this.latInput.value) && isValidCoord(this.lngInput.value)) {
            lat = parseFloat(this.latInput.value);
            lng = parseFloat(this.lngInput.value);
        }

        const zoom = this.config.zoom ?? 16;
        this.createMap(lat, lng, zoom);

        // adicionar camadas
        this.existingLocationsLayer.addTo(this.map);
        this.existingBarriersLayer.addTo(this.map);

        this.setupMapEvents();

        // plotagem inicial
        if (this.config.isEditMode && this.config.barrier) {
            if (this.config.barrier.institution) this.plotInstitutionAndData(this.config.barrier.institution);
            this.plotCurrentBarrier(this.config.barrier);
        } else if (this.config.institution) {
            this.plotInstitutionAndData(this.config.institution);
        }

        // se necessário, criar marcador editável
        if (!this.config.isEditMode || (this.config.isEditMode && this.config.barrier && (!this.config.barrier.latitude || !this.config.barrier.longitude))) {
            this.setupMarker(lat, lng);
        }

        this.updateInputs(lat, lng);

        // checkbox sem localização
        if (this.noLocationCheckbox) {
            on(this.noLocationCheckbox, 'change', (e) => {
                if (e.target.checked) this.disableMap();
                else {
                    this.enableMap();
                    if (this.institutionSelect && this.institutionSelect.value) {
                        setTimeout(() => {
                            const id = this.institutionSelect.value;
                            if (window.institutionsData) {
                                const inst = window.institutionsData.find(i => String(i.id) === String(id));
                                if (inst) this.plotInstitutionAndData(inst);
                            }
                        }, 100);
                    }
                }
            });
            if (this.noLocationCheckbox.checked) this.disableMap();
        }

        // garantia de redraw
        setTimeout(() => { if (this.map) this.map.invalidateSize(); }, 100);

        this.initialized = true;
        log('BarrierMap initialized');
    }

    createMap(lat, lng, zoom) {
        log('createMap', lat, lng, zoom);
        const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap', maxZoom: 19 });
        const googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { attribution: '© Google Maps', maxZoom: 21 });

        this.map = L.map(this.config.mapId, {
            center: [lat, lng],
            zoom,
            layers: [streetLayer],
            zoomControl: true,
            scrollWheelZoom: true
        });

        const baseMaps = { "Mapa de Ruas (OSM)": streetLayer, "Satélite (Google)": googleSat };
        L.control.layers(baseMaps).addTo(this.map);
    }

    setupMarker(lat, lng) {
        log('setupMarker', lat, lng);
        const blueIcon = createColorIcon('blue');

        if (this.currentMarker) {
            this.currentMarker.setLatLng([lat, lng]);
            return;
        }

        this.currentMarker = L.marker([lat, lng], { draggable: true, icon: blueIcon }).addTo(this.map);
        this.currentMarker.bindTooltip("Localização da Barreira (Editável)", {
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

    setupMapEvents() {
        this.map.on('click', (e) => {
            log('map click', e.latlng);
            if (this.noLocationCheckbox && this.noLocationCheckbox.checked) {
                alert('Por favor, desmarque "Sem localização física" para definir uma localização.');
                return;
            }
            if (!this.institutionSelect || !this.institutionSelect.value) {
                alert('Por favor, selecione uma instituição base primeiro.');
                return;
            }
            this.updateLocation(e.latlng.lat, e.latlng.lng, false);
        });
    }

    updateInputs(lat, lng) {
        const fLat = isValidCoord(lat) ? parseFloat(lat).toFixed(8) : '';
        const fLng = isValidCoord(lng) ? parseFloat(lng).toFixed(8) : '';
        if (this.latInput) this.latInput.value = fLat;
        if (this.lngInput) this.lngInput.value = fLng;

        // NOVO: atualiza os spans de exibição
        if (this.displayLat) this.displayLat.textContent = fLat || '-';
        if (this.displayLng) this.displayLng.textContent = fLng || '-';
    }

    updateLocation(lat, lng, moveMap = false) {
        log('updateLocation', lat, lng, moveMap);
        if (!isValidCoord(lat) || !isValidCoord(lng)) return;
        this.updateInputs(lat, lng);
        this.setupMarker(lat, lng);

        if (moveMap && this.map) {
            this.map.flyTo([lat, lng], 18, { animate: true, duration: 1.5 });
        }
    }

    toggleGreyMarkers() {
        if (!this.map) return;

        if (this.map.hasLayer(this.existingLocationsLayer)) {
            this.map.removeLayer(this.existingLocationsLayer);
            log("Localizações cinzas ocultadas");
        } else {
            this.map.addLayer(this.existingLocationsLayer);
            log("Localizações cinzas exibidas");
        }
    }

    getBarrierStatus(barrier) {
        if (!barrier) return 'Sem status';
        if (barrier.inspections && barrier.inspections.length > 0) {
            const inspections = [...barrier.inspections].sort((a, b) => new Date(b.inspection_date || b.created_at) - new Date(a.inspection_date || a.created_at));
            const latest = inspections[0];
            if (latest && latest.status) return barrierStatusLabels[latest.status] || 'Sem status';
        }
        if (barrier.status) return barrierStatusLabels[barrier.status] || 'Sem status';
        return 'Sem status';
    }

    plotInstitutionAndData(institution = {}) {
        log('plotInstitutionAndData', institution);
        if (!institution || !isValidCoord(institution.latitude) || !isValidCoord(institution.longitude)) {
            console.error('Instituição sem coordenadas válidas.');
            return;
        }

        // Remove marcador antigo da instituição (vermelho) e limpa camadas
        if (this.institutionMarker) this.map.removeLayer(this.institutionMarker);
        this.existingLocationsLayer.clearLayers();
        this.existingBarriersLayer.clearLayers();

        // Adiciona marcador da instituição (vermelho)
        this.institutionMarker = L.marker([institution.latitude, institution.longitude], { icon: createColorIcon('red') }).addTo(this.map);
        this.institutionMarker.bindTooltip(`Sede: ${institution.name}`, {
            permanent: false,
            direction: 'top',
            className: 'bg-danger text-white border-0 fw-bold rounded shadow-sm px-2 py-1',
            offset: [0, -35]
        });

        // Localizações existentes (cinza)
        if (institution.locations && institution.locations.length) {
            const greyIcon = createColorIcon('grey', [20, 32], [10, 32]);
            institution.locations.forEach(loc => {
                if (!isValidCoord(loc.latitude) || !isValidCoord(loc.longitude)) return;
                const m = L.marker([loc.latitude, loc.longitude], { icon: greyIcon }).addTo(this.existingLocationsLayer);
                m.bindTooltip(`${loc.name} (${loc.type || 'Local'})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-secondary text-white border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            });
        }

        // Barreiras existentes (amarelo), exceto a atual em edição
        if (institution.barriers && institution.barriers.length) {
            const yellowIcon = createColorIcon('yellow', [20, 32], [10, 32]);
            institution.barriers.forEach(b => {
                if (this.config.isEditMode && this.config.barrier && this.config.barrier.id === b.id) return;
                if (!isValidCoord(b.latitude) || !isValidCoord(b.longitude)) return;
                const m = L.marker([b.latitude, b.longitude], { icon: yellowIcon }).addTo(this.existingBarriersLayer);
                const statusText = this.getBarrierStatus(b);
                m.bindTooltip(`${b.name} (${statusText})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-warning text-dark border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            });
        }

        // --- Lógica para o marcador azul (barreira em criação/edição) ---
        const shouldMoveBlueMarker = !this.config.isEditMode ||
            (this.config.isEditMode && this.config.barrier &&
                (!isValidCoord(this.config.barrier.latitude) || !isValidCoord(this.config.barrier.longitude)));

        if (shouldMoveBlueMarker) {
            // Move o marcador azul para a sede da instituição e centraliza o mapa
            this.updateLocation(institution.latitude, institution.longitude, true);
        } else {
            // Em edição com barreira já localizada: apenas move o mapa para a instituição (opcional)
            // O marcador azul permanece onde está (na barreira)
            if (!this.config.barrier || !isValidCoord(this.config.barrier.latitude) || !isValidCoord(this.config.barrier.longitude)) {
                this.map.flyTo([institution.latitude, institution.longitude], institution.default_zoom || 16, { animate: true, duration: 2 });
            }
        }
    }

    plotCurrentBarrier(barrier = {}) {
        log('plotCurrentBarrier', barrier);
        if (barrier.latitude && barrier.longitude) {
            this.updateLocation(barrier.latitude, barrier.longitude, false);
            this.map.flyTo([barrier.latitude, barrier.longitude], 18, { animate: true, duration: 2 });
        }
    }

    disableMap() {
        if (!this.map) return;
        ['dragging','touchZoom','doubleClickZoom','scrollWheelZoom','boxZoom','keyboard'].forEach(fn => { if (this.map[fn]) this.map[fn].disable(); });
        this.map._container.style.cursor = 'not-allowed';
        this.map._container.style.opacity = '0.5';
        if (this.currentMarker) { this.map.removeLayer(this.currentMarker); this.currentMarker = null; }
        if (this.latInput) this.latInput.value = '';
        if (this.lngInput) this.lngInput.value = '';
        // Atualiza os spans para vazio também
        if (this.displayLat) this.displayLat.textContent = '-';
        if (this.displayLng) this.displayLng.textContent = '-';
    }

    enableMap() {
        if (!this.map) return;
        ['dragging','touchZoom','doubleClickZoom','scrollWheelZoom','boxZoom','keyboard'].forEach(fn => { if (this.map[fn]) this.map[fn].enable(); });
        this.map._container.style.cursor = '';
        this.map._container.style.opacity = '1';
        if (this.institutionSelect && this.institutionSelect.value && window.institutionsData) {
            const inst = window.institutionsData.find(i => String(i.id) === String(this.institutionSelect.value));
            if (inst) setTimeout(() => this.plotInstitutionAndData(inst), 100);
        }
    }
}

/* ============================
   Inicialização única e segura
   ============================ */
document.addEventListener('DOMContentLoaded', () => {
    log('DOM loaded, inicializando módulos');

    const mapEl = $('map-barrier');
    if (!mapEl) { console.error('Elemento do mapa (map-barrier) não encontrado'); return; }
    if (typeof L === 'undefined') { console.error('Leaflet (L) não encontrado'); return; }
    if (!window.barrierMapConfig) { console.error('window.barrierMapConfig não encontrado'); return; }

    try {
        // 1. Cria instâncias
        const map = new BarrierMap(window.barrierMapConfig);
        window.barrierMapInstance = map;

        const fm = new FormManager(window.barrierMapConfig);
        window.formManagerInstance = fm;

        // --- NOVO: Lógica do Checkbox para limpar localizações cinzas ---
        const checkToggle = $('btn-toggle-locations'); // Certifique-se que o ID no HTML é este
        if (checkToggle) {
            on(checkToggle, 'change', (e) => {
                if (window.barrierMapInstance) {
                    // Chama o método que já criamos na classe BarrierMap
                    window.barrierMapInstance.toggleGreyMarkers();
                }
            });
        }
        // ----------------------------------------------------------------

        // Quando instituição mudar manualmente
        const instSel = $('institution_select');
        if (instSel) on(instSel, 'change', function() {
            log('manual institution change', this.value);
            if (window.formManagerInstance) window.formManagerInstance.handleInstitutionChange();

            // DICA: Se mudar a instituição, garante que o checkbox volte a ficar marcado
            if (checkToggle) checkToggle.checked = true;
        });

        // Carregar localizações iniciais se houver initialInstitutionId
        if (window.initialInstitutionId && window.formManagerInstance) {
            setTimeout(() => {
                log('Carregando localizações iniciais', window.initialInstitutionId);
                window.formManagerInstance.loadInstitutionLocations(window.initialInstitutionId);
            }, 500);
        }

        window.BarrierMap = BarrierMap;
        window.FormManager = FormManager;

        log('Inicialização concluída');
    } catch (err) {
        console.error('Erro na inicialização do script:', err);
    }
});
