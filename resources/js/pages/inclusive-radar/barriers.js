/* barrier-modern.js
   Versão modernizada mantendo a lógica original
*/

const debug = false;
const log = (...args) => { if (debug) console.log(...args); };

// Labels de status
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
const exists = el => Boolean(el);
const isValidCoord = v => v !== null && v !== undefined && Number.isFinite(Number(v));
const safeParse = v => (isValidCoord(v) ? Number.parseFloat(v) : null);

const createOption = (value = '', text = '', dataset = {}) => {
    const o = document.createElement('option');
    o.value = value ?? '';
    o.textContent = text ?? '';
    for (const [k, v] of Object.entries(dataset || {})) {
        if (v !== undefined && v !== null) o.setAttribute(`data-${k}`, String(v));
    }
    return o;
};

const populateSelect = (selectEl, items = [], { valueKey = 'id', labelKey = 'name', datasetMap = {} } = {}) => {
    if (!selectEl) return;
    selectEl.innerHTML = ''; // limpa
    const frag = document.createDocumentFragment();
    frag.appendChild(createOption('', 'Selecione um local...'));

    for (const it of items || []) {
        const dataset = {};
        for (const [dk, fk] of Object.entries(datasetMap || {})) {
            if (it?.[fk] !== undefined) dataset[dk] = it[fk];
        }
        const opt = createOption(it?.[valueKey] ?? '', it?.[labelKey] ?? '', dataset);
        frag.appendChild(opt);
    }
    selectEl.appendChild(frag);
};

const createColorIcon = (color = 'blue', size = [25, 41], anchor = [12, 41], shadowDimensions = null) => {
    const mapUrl = {
        blue: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        red: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        grey: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
        yellow: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png'
    };

    const currentShadowSize = shadowDimensions ?? [size[1], size[1]];
    const currentShadowAnchor = [Math.floor(size[1] / 3), size[1]];

    return L.icon({
        iconUrl: mapUrl[color] ?? mapUrl.blue,
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: size,
        iconAnchor: anchor,
        shadowSize: currentShadowSize,
        shadowAnchor: currentShadowAnchor,
        popupAnchor: [1, -34]
    });
};

/* ============================
   FormManager
   ============================ */
class FormManager {
    constructor(config = {}) {
        this.config = config ?? {};

        // elementos usados no template
        this.institutionSelect = $('institution_select');
        this.locationSelect = $('location_select');
        this.locationWrapper = $('location_wrapper');
        this.isAnonymousCheck = $('is_anonymous');
        this.notApplicableCheck = $('not_applicable');
        this.wrapperNotApplicable = $('wrapper_not_applicable');
        this.identificationFields = $('identification_fields');
        this.personSelects = $('person_selects');
        this.manualPersonData = $('manual_person_data');
        this.affectedPersonNameInput = document.querySelector('input[name="affected_person_name"]');
        this.affectedPersonRoleInput = document.querySelector('input[name="affected_person_role"]');

        // binds
        this._boundHandleInstitutionChange = this.handleInstitutionChange.bind(this);
        this._boundTogglePersonFields = this.togglePersonFields.bind(this);
        this._boundHandleCategoryChange = this.handleCategoryChange.bind(this);

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
        if (this.isAnonymousCheck) on(this.isAnonymousCheck, 'change', this._boundTogglePersonFields);
        if (this.notApplicableCheck) on(this.notApplicableCheck, 'change', this._boundTogglePersonFields);

        const categorySelect = document.getElementById('barrier_category_id');
        if (categorySelect) on(categorySelect, 'change', this._boundHandleCategoryChange);
    }

    applyInitialState() {
        log('FormManager.applyInitialState', this.config);

        if (this.config.isEditMode && this.config.barrier) {
            const b = this.config.barrier;
            if (this.isAnonymousCheck) this.isAnonymousCheck.checked = !!b.is_anonymous;
            if (this.notApplicableCheck) this.notApplicableCheck.checked = !!b.not_applicable;
            if (this.institutionSelect && b.institution_id) {
                this.institutionSelect.value = b.institution_id;
                // carrega localizações da instituição — mantive pequena espera para garantir dados carregados na página
                setTimeout(() => this.handleInstitutionChange(), 80);
            }
        }

        this.togglePersonFields();

        if (window.oldLocationId && this.locationSelect) {
            setTimeout(() => {
                this.locationSelect.value = window.oldLocationId;
                if (this.locationSelect.value === window.oldLocationId) this.handleLocationSelectChange();
            }, 120);
        }

        // checar categoria inicial
        setTimeout(() => this.handleCategoryChange(), 80);
    }

    async loadInstitutionLocations(institutionId) {
        log('loadInstitutionLocations', institutionId);
        if (!institutionId || !Array.isArray(window.institutionsData) || !this.locationSelect) return;

        const inst = window.institutionsData.find(i => String(i.id) === String(institutionId));
        if (!inst) return;

        populateSelect(this.locationSelect, inst.locations || [], {
            valueKey: 'id',
            labelKey: 'name',
            datasetMap: { lat: 'latitude', lng: 'longitude' }
        });

        // Se em edição e existe location_id, seleciona (pequeno delay para garantir DOM)
        if (this.config.isEditMode && this.config.barrier?.location_id) {
            setTimeout(() => {
                this.locationSelect.value = this.config.barrier.location_id;
                if (this.locationSelect.value == this.config.barrier.location_id) this.handleLocationSelectChange();
            }, 80);
        }
    }

    handleInstitutionChange() {
        const val = this.institutionSelect?.value ?? null;
        log('handleInstitutionChange', val);

        if (!val) {
            this.locationWrapper?.classList?.add('d-none');
            if (this.locationSelect) this.locationSelect.innerHTML = '<option value="">Selecione um local...</option>';
            return;
        }

        this.locationWrapper?.classList?.remove('d-none');
        this.loadInstitutionLocations(val);

        if (window.barrierMapInstance && Array.isArray(window.institutionsData)) {
            const selInst = window.institutionsData.find(inst => String(inst.id) === String(val));
            if (selInst) window.barrierMapInstance.plotInstitutionAndData(selInst);
        }
    }

    handleLocationSelectChange() {
        if (!this.locationSelect) return;
        const opt = this.locationSelect.selectedOptions?.[0];
        if (!opt) return;
        log('handleLocationSelectChange', opt);
        const lat = opt.getAttribute('data-lat');
        const lng = opt.getAttribute('data-lng');
        if (isValidCoord(lat) && isValidCoord(lng) && window.barrierMapInstance) {
            window.barrierMapInstance.updateLocation(Number(lat), Number(lng), true);
        }
    }

    handleCategoryChange() {
        const select = document.getElementById('barrier_category_id');
        if (!select || !select.value) return;
        const categoryId = select.value;
        const blocksMap = Boolean(window.categoriesData?.[categoryId]);
        const categoryName = select.options[select.selectedIndex]?.textContent ?? '';
        window.barrierMapInstance?.setBlocked(blocksMap, categoryName);
    }

    clearManualPersonFields() {
        if (this.affectedPersonNameInput) this.affectedPersonNameInput.value = '';
        if (this.affectedPersonRoleInput) this.affectedPersonRoleInput.value = '';
    }

    togglePersonFields() {
        const isAnonymous = this.isAnonymousCheck?.checked ?? false;
        const notApplicable = this.notApplicableCheck?.checked ?? false;
        log('togglePersonFields', { isAnonymous, notApplicable });

        if (isAnonymous) {
            this.wrapperNotApplicable?.classList?.add('d-none');
            this.identificationFields?.classList?.add('d-none');
            if (this.notApplicableCheck) this.notApplicableCheck.checked = false;
            this.clearManualPersonFields();
            return;
        }

        this.wrapperNotApplicable?.classList?.remove('d-none');
        this.identificationFields?.classList?.remove('d-none');

        if (notApplicable) {
            this.personSelects?.classList?.add('d-none');
            this.manualPersonData?.classList?.remove('d-none');
        } else {
            this.personSelects?.classList?.remove('d-none');
            if (this.manualPersonData) {
                this.manualPersonData.classList?.add('d-none');
                this.clearManualPersonFields();
            }
        }
    }
}

/* ============================
   BarrierMap
   ============================ */
class BarrierMap {
    constructor(config = {}) {
        this.config = config ?? {};
        this.map = null;
        this.currentMarker = null;
        this.institutionMarker = null;
        this.existingLocationsLayer = L.layerGroup();
        this.existingBarriersLayer = L.layerGroup();
        this.initialized = false;

        // inputs / elements
        this.latInput = null;
        this.lngInput = null;
        this.institutionSelect = null;
        this.displayLat = document.getElementById('display-map-barrier-lat');
        this.displayLng = document.getElementById('display-map-barrier-lng');
        this.blockedOverlay = document.getElementById('map-blocked-overlay');
        this.blockedTextSpan = document.getElementById('map-blocked-text');
        this.isBlocked = false;

        this.init();
    }

    init() {
        log('BarrierMap.init', this.config);
        this.mapContainer = $('map-barrier');
        this.container = $(`leaflet-container-${this.config.mapId}`);
        if (!this.mapContainer || !this.container) {
            console.error('Elemento do mapa ou container não encontrado.');
            return;
        }

        this.latInput = $('lat');
        this.lngInput = $('lng');
        this.institutionSelect = $('institution_select');

        let lat = this.config.lat ?? 0;
        let lng = this.config.lng ?? 0;

        if (this.config.isEditMode && this.config.barrier && isValidCoord(this.config.barrier.latitude) && isValidCoord(this.config.barrier.longitude)) {
            lat = this.config.barrier.latitude;
            lng = this.config.barrier.longitude;
        } else if (isValidCoord(this.latInput?.value) && isValidCoord(this.lngInput?.value)) {
            lat = Number(this.latInput.value);
            lng = Number(this.lngInput.value);
        }

        const zoom = this.config.zoom ?? 16;
        this.createMap(lat, lng, zoom);

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

        // marcador editável se aplicável
        if (!this.config.isEditMode || (this.config.isEditMode && this.config.barrier && (!isValidCoord(this.config.barrier.latitude) || !isValidCoord(this.config.barrier.longitude)))) {
            this.setupMarker(lat, lng);
        }

        this.updateInputs(lat, lng);

        // redraw garantido
        setTimeout(() => { this.map?.invalidateSize(); }, 80);

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
            if (this.isBlocked) this.currentMarker.dragging?.disable();
            return;
        }

        this.currentMarker = L.marker([lat, lng], { draggable: !this.isBlocked, icon: blueIcon }).addTo(this.map);
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
            if (this.isBlocked) return;

            if (!this.institutionSelect?.value) {
                alert('Por favor, selecione uma instituição base primeiro.');
                return;
            }
            this.updateLocation(e.latlng.lat, e.latlng.lng, false);
        });
    }

    updateInputs(lat, lng) {
        const fLat = isValidCoord(lat) ? Number(lat).toFixed(8) : '';
        const fLng = isValidCoord(lng) ? Number(lng).toFixed(8) : '';
        if (this.latInput) this.latInput.value = fLat;
        if (this.lngInput) this.lngInput.value = fLng;
        if (this.displayLat) this.displayLat.textContent = fLat || '-';
        if (this.displayLng) this.displayLng.textContent = fLng || '-';
    }

    updateLocation(lat, lng, moveMap = false) {
        if (this.isBlocked) return;
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

        if (Array.isArray(barrier.inspections) && barrier.inspections.length > 0) {
            const latestInspection = [...barrier.inspections].sort((a, b) => {
                const dateA = new Date(a.inspection_date || a.created_at).getTime();
                const dateB = new Date(b.inspection_date || b.created_at).getTime();
                return dateB - dateA;
            })[0];

            if (latestInspection?.status) {
                return barrierStatusLabels[latestInspection.status] ?? 'Sem status';
            }
        }

        const statusKey = barrier.status?.value ?? barrier.status;
        return barrierStatusLabels[statusKey] ?? 'Identificada';
    }

    plotInstitutionAndData(institution = {}) {
        log('plotInstitutionAndData', institution);
        if (!institution || !isValidCoord(institution.latitude) || !isValidCoord(institution.longitude)) {
            console.error('Instituição sem coordenadas válidas.');
            return;
        }

        this.institutionMarker && this.map.removeLayer(this.institutionMarker);
        this.existingLocationsLayer.clearLayers();
        this.existingBarriersLayer.clearLayers();

        this.institutionMarker = L.marker([institution.latitude, institution.longitude], { icon: createColorIcon('red') }).addTo(this.map);
        this.institutionMarker.bindTooltip(`Sede: ${institution.name}`, {
            permanent: false,
            direction: 'top',
            className: 'bg-danger text-white border-0 fw-bold rounded shadow-sm px-2 py-1',
            offset: [0, -35]
        });

        // localizações existentes (cinza)
        if (Array.isArray(institution.locations)) {
            const greyIcon = createColorIcon('grey', [20, 32], [10, 32]);
            for (const loc of institution.locations) {
                if (!isValidCoord(loc.latitude) || !isValidCoord(loc.longitude)) continue;
                const m = L.marker([loc.latitude, loc.longitude], { icon: greyIcon }).addTo(this.existingLocationsLayer);
                m.bindTooltip(`${loc.name} (${loc.type ?? 'Local'})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-secondary text-white border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            }
        }

        // barreiras existentes (amarelo)
        if (Array.isArray(institution.barriers)) {
            const yellowIcon = createColorIcon('yellow', [20, 32], [10, 32]);
            for (const b of institution.barriers) {
                if (this.config.isEditMode && this.config.barrier && this.config.barrier.id === b.id) continue;
                if (!isValidCoord(b.latitude) || !isValidCoord(b.longitude)) continue;
                const m = L.marker([b.latitude, b.longitude], { icon: yellowIcon }).addTo(this.existingBarriersLayer);
                const statusText = this.getBarrierStatus(b);
                m.bindTooltip(`${b.name} (${statusText})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-warning text-dark border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            }
        }

        const shouldMoveBlueMarker = !this.config.isEditMode ||
            (this.config.isEditMode && this.config.barrier && (!isValidCoord(this.config.barrier.latitude) || !isValidCoord(this.config.barrier.longitude)));

        if (!this.isBlocked && shouldMoveBlueMarker) {
            this.updateLocation(institution.latitude, institution.longitude, true);
        } else if (!shouldMoveBlueMarker && !this.isBlocked) {
            if (!this.config.barrier || !isValidCoord(this.config.barrier.latitude) || !isValidCoord(this.config.barrier.longitude)) {
                this.map.flyTo([institution.latitude, institution.longitude], institution.default_zoom ?? 16, { animate: true, duration: 2 });
            }
        } else if (this.isBlocked && this.currentMarker) {
            this.currentMarker.dragging?.disable();
        }
    }

    plotCurrentBarrier(barrier = {}) {
        log('plotCurrentBarrier', barrier);
        if (isValidCoord(barrier.latitude) && isValidCoord(barrier.longitude)) {
            this.updateLocation(barrier.latitude, barrier.longitude, false);
            this.map.flyTo([barrier.latitude, barrier.longitude], 18, { animate: true, duration: 2 });
        }
    }

    setBlocked(blocked, categoryName = '') {
        if (!this.map) return;
        this.isBlocked = Boolean(blocked);

        const disableInteractions = () => {
            this.map.dragging?.disable();
            this.map.touchZoom?.disable();
            this.map.doubleClickZoom?.disable();
            this.map.scrollWheelZoom?.disable();
            this.map.boxZoom?.disable();
            this.map.keyboard?.disable();
        };

        const enableInteractions = () => {
            this.map.dragging?.enable();
            this.map.touchZoom?.enable();
            this.map.doubleClickZoom?.enable();
            this.map.scrollWheelZoom?.enable();
            this.map.boxZoom?.enable();
            this.map.keyboard?.enable();
        };

        if (this.isBlocked) {
            disableInteractions();
            this.currentMarker?.dragging?.disable();
            if (this.blockedOverlay && this.blockedTextSpan) {
                this.blockedTextSpan.textContent = categoryName
                    ? `Mapa não se enquadra para categoria: ${categoryName}`
                    : 'Mapa não se enquadra para categoria';
                this.blockedOverlay.classList.remove('d-none');
            }
        } else {
            enableInteractions();
            this.currentMarker?.dragging?.enable();
            if (this.blockedOverlay) this.blockedOverlay.classList.add('d-none');
        }
    }
}

function initInspectionRedirects() {
    const timeline = document.querySelector('.history-timeline');
    if (!timeline) return;

    timeline.addEventListener('click', (e) => {
        const card = e.target.closest('.cursor-pointer');
        const url = card?.getAttribute('data-url');
        if (url) {
            window.location.href = url;
        }
    });

    timeline.addEventListener('keydown', (e) => {
        const card = e.target.closest('.cursor-pointer');
        const url = card?.getAttribute('data-url');

        if (card && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            if (url) window.location.href = url;
        }
    });
}

/* ============================
   Inicialização
   ============================ */
document.addEventListener('DOMContentLoaded', () => {
    log('DOM loaded, inicializando módulos');

    initInspectionRedirects();

    const mapEl = $('map-barrier');
    if (!mapEl) { console.error('Elemento do mapa (map-barrier) não encontrado'); return; }
    if (typeof L === 'undefined') { console.error('Leaflet (L) não encontrado'); return; }
    if (!window.barrierMapConfig) { console.error('window.barrierMapConfig não encontrado'); return; }

    try {
        const map = new BarrierMap(window.barrierMapConfig);
        window.barrierMapInstance = map;

        const fm = new FormManager(window.barrierMapConfig);
        window.formManagerInstance = fm;

        const checkToggle = $('btn-toggle-locations');
        if (checkToggle) {
            on(checkToggle, 'change', () => window.barrierMapInstance?.toggleGreyMarkers());
        }

        const instSel = $('institution_select');
        if (instSel) on(instSel, 'change', function () {
            log('manual institution change', this.value);
            window.formManagerInstance?.handleInstitutionChange();
            if (checkToggle) checkToggle.checked = true;
        });

        if (window.initialInstitutionId && window.formManagerInstance) {
            setTimeout(() => {
                log('Carregando localizações iniciais', window.initialInstitutionId);
                window.formManagerInstance.loadInstitutionLocations(window.initialInstitutionId);
            }, 120);
        }

        window.BarrierMap = BarrierMap;
        window.FormManager = FormManager;

        log('Inicialização concluída');
    } catch (err) {
        console.error('Erro na inicialização do script:', err);
    }
});
