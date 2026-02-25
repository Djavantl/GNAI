// Mapeamento de status para labels
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

        // Configura eventos
        this.setupEventListeners();

        // Aplica estado inicial
        this.applyInitialState();

        this.initialized = true;
        console.log('FormManager inicializado com sucesso');
    }

    setupEventListeners() {
        // Instituição
        if (this.institutionSelect) {
            this.institutionSelect.addEventListener('change', () => this.handleInstitutionChange());
        }

        // Localização
        if (this.locationSelect) {
            this.locationSelect.addEventListener('change', () => this.handleLocationSelectChange());
        }

        // Sem localização física
        if (this.noLocationCheck) {
            this.noLocationCheck.addEventListener('change', () => this.handleNoLocationChange());
        }

        // Pessoa Impactada
        if (this.isAnonymousCheck) {
            this.isAnonymousCheck.addEventListener('change', () => this.togglePersonFields());
        }

        if (this.notApplicableCheck) {
            this.notApplicableCheck.addEventListener('change', () => this.togglePersonFields());
        }
    }

    applyInitialState() {
        console.log('Aplicando estado inicial do formulário');

        // Se for edição, carrega os dados existentes
        if (this.config.isEditMode && this.config.barrier) {
            console.log('Modo edição - Configurando dados da barreira:', this.config.barrier);

            // Configura checkboxes
            if (this.noLocationCheck) {
                // Verifica se a barreira não tem localização
                const hasNoLocation = this.config.barrier.latitude === null && this.config.barrier.longitude === null;
                this.noLocationCheck.checked = hasNoLocation;
            }

            if (this.isAnonymousCheck) {
                this.isAnonymousCheck.checked = this.config.barrier.is_anonymous || false;
            }

            if (this.notApplicableCheck) {
                this.notApplicableCheck.checked = this.config.barrier.not_applicable || false;
            }

            // Se houver instituição selecionada, carrega as localizações
            if (this.institutionSelect && this.config.barrier.institution_id) {
                // Define o valor do select
                this.institutionSelect.value = this.config.barrier.institution_id;

                // Dispara o evento change para carregar localizações
                setTimeout(() => {
                    this.handleInstitutionChange();
                }, 100);
            }
        }

        // Aplica estado inicial dos campos
        this.handleNoLocationChange();
        this.togglePersonFields();

        // Se houver oldLocationId (após erro de validação), seleciona
        if (window.oldLocationId && this.locationSelect) {
            setTimeout(() => {
                this.locationSelect.value = window.oldLocationId;
                if (window.oldLocationId && this.locationSelect.value === window.oldLocationId) {
                    // Dispara evento para mover o mapa
                    this.handleLocationSelectChange();
                }
            }, 800);
        }
    }

    async loadInstitutionLocations(institutionId) {
        console.log('Carregando localizações da instituição:', institutionId);

        try {
            // Se já tivermos os dados das instituições com localizações
            if (window.institutionsData) {
                const institution = window.institutionsData.find(inst => inst.id == institutionId);
                if (institution && this.locationSelect) {
                    // Limpa opções existentes
                    this.locationSelect.innerHTML = '<option value="">Selecione um local...</option>';

                    // Adiciona as localizações se existirem
                    if (institution.locations && institution.locations.length > 0) {
                        institution.locations.forEach(location => {
                            const option = document.createElement('option');
                            option.value = location.id;
                            option.textContent = location.name;
                            option.dataset.lat = location.latitude;
                            option.dataset.lng = location.longitude;
                            this.locationSelect.appendChild(option);
                        });

                        console.log(`${institution.locations.length} localizações carregadas`);

                        // Se estiver editando e a barreira tiver location_id, seleciona
                        if (this.config.isEditMode && this.config.barrier && this.config.barrier.location_id) {
                            setTimeout(() => {
                                this.locationSelect.value = this.config.barrier.location_id;
                                // Se selecionou com sucesso, dispara evento
                                if (this.locationSelect.value == this.config.barrier.location_id) {
                                    this.handleLocationSelectChange();
                                }
                            }, 200);
                        }
                    } else {
                        console.log('Instituição não tem localizações cadastradas');
                    }
                }
            }
        } catch (error) {
            console.error('Erro ao carregar localizações:', error);
        }
    }

    handleInstitutionChange() {
        const institutionId = this.institutionSelect.value;
        console.log('Instituição alterada para:', institutionId);

        if (institutionId) {
            // Mostra os campos de localização (se não estiver marcado "Sem localização física")
            if (!this.noLocationCheck.checked && this.locationWrapper) {
                this.locationWrapper.classList.remove('d-none');
            }

            // Carrega as localizações da instituição
            this.loadInstitutionLocations(institutionId);

            // Atualiza o mapa com a instituição selecionada
            if (window.barrierMapInstance && window.institutionsData) {
                const selectedInstitution = window.institutionsData.find(inst => inst.id == institutionId);
                if (selectedInstitution) {
                    console.log('Atualizando mapa com instituição:', selectedInstitution);
                    window.barrierMapInstance.plotInstitutionAndData(selectedInstitution);
                }
            }
        } else {
            // Esconde os campos de localização
            if (this.locationWrapper) {
                this.locationWrapper.classList.add('d-none');
            }

            // Limpa o select de localização
            if (this.locationSelect) {
                this.locationSelect.innerHTML = '<option value="">Selecione um local...</option>';
            }
        }
    }

    handleLocationSelectChange() {
        const selectedOption = this.locationSelect.selectedOptions[0];
        console.log('Local selecionado:', selectedOption);

        if (selectedOption && selectedOption.value && window.barrierMapInstance) {
            const lat = selectedOption.getAttribute('data-lat');
            const lng = selectedOption.getAttribute('data-lng');

            if (lat && lng) {
                console.log('Movendo para localização:', lat, lng);
                window.barrierMapInstance.updateLocation(parseFloat(lat), parseFloat(lng), true);
            }
        }
    }

    handleNoLocationChange() {
        const isChecked = this.noLocationCheck.checked;
        console.log('Sem localização física:', isChecked);

        if (isChecked) {
            // Esconde campos de localização específica
            if (this.locationWrapper) {
                this.locationWrapper.classList.add('d-none');
            }

            // Limpa seleção do select
            if (this.locationSelect) {
                this.locationSelect.value = '';
            }

            // Desabilita o mapa
            if (window.barrierMapInstance) {
                window.barrierMapInstance.disableMap();
            }
        } else {
            // Se houver instituição selecionada, mostra os campos
            if (this.institutionSelect && this.institutionSelect.value && this.locationWrapper) {
                this.locationWrapper.classList.remove('d-none');
            }

            // Habilita o mapa
            if (window.barrierMapInstance) {
                window.barrierMapInstance.enableMap();

                // Se houver instituição, recarrega os dados
                if (this.institutionSelect && this.institutionSelect.value) {
                    setTimeout(() => {
                        this.handleInstitutionChange();
                    }, 100);
                }
            }
        }
    }

    clearPersonFields() {
        // Limpa inputs manuais
        if (this.affectedPersonNameInput) {
            this.affectedPersonNameInput.value = '';
        }
        if (this.affectedPersonRoleInput) {
            this.affectedPersonRoleInput.value = '';
        }

        // Limpa selects de estudante/profissional
        if (this.affectedStudentSelect) {
            this.affectedStudentSelect.value = '';
        }
        if (this.affectedProfessionalSelect) {
            this.affectedProfessionalSelect.value = '';
        }
    }

    togglePersonFields() {
        const isAnonymous = this.isAnonymousCheck.checked;
        const notApplicable = this.notApplicableCheck.checked;

        console.log('Toggle Person Fields:', {isAnonymous, notApplicable});

        // REGRA 1: Se "Relato Anônimo" estiver marcado
        if (isAnonymous) {
            // Esconde checkbox "Relato Geral"
            if (this.wrapperNotApplicable) {
                this.wrapperNotApplicable.classList.add('d-none');
            }

            // Esconde todos os campos de identificação
            if (this.identificationFields) {
                this.identificationFields.classList.add('d-none');
            }

            // Desmarca "Relato Geral" se estiver marcado
            if (this.notApplicableCheck) {
                this.notApplicableCheck.checked = false;
            }

            // Limpa todos os campos da pessoa impactada
            this.clearPersonFields();
        }
        // REGRA 2: Se "Relato Anônimo" NÃO estiver marcado
        else {
            // Mostra checkbox "Relato Geral"
            if (this.wrapperNotApplicable) {
                this.wrapperNotApplicable.classList.remove('d-none');
            }

            // Mostra campos de identificação
            if (this.identificationFields) {
                this.identificationFields.classList.remove('d-none');
            }

            // REGRA 2a: Se "Relato Geral" estiver marcado
            if (notApplicable) {
                // Esconde drop-downs de estudante/profissional
                if (this.personSelects) {
                    this.personSelects.classList.add('d-none');
                }

                // Mostra campos para digitar nome e cargo
                if (this.manualPersonData) {
                    this.manualPersonData.classList.remove('d-none');
                }

                // Limpa selects de estudante/profissional
                if (this.affectedStudentSelect) this.affectedStudentSelect.value = '';
                if (this.affectedProfessionalSelect) this.affectedProfessionalSelect.value = '';
            }
            // REGRA 2b: Se "Relato Geral" NÃO estiver marcado
            else {
                // Mostra drop-downs de estudante/profissional
                if (this.personSelects) {
                    this.personSelects.classList.remove('d-none');
                }

                // Esconde campos para digitar nome e cargo
                if (this.manualPersonData) {
                    this.manualPersonData.classList.add('d-none');
                }

                // Limpa inputs manuais
                this.clearPersonFields();
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

        console.log('Inicializando BarrierMap...');

        // Elementos do DOM
        this.mapContainer = document.getElementById(this.config.mapId);
        this.container = document.getElementById(`leaflet-container-${this.config.mapId}`);

        if (!this.mapContainer || !this.container) {
            console.error(`Elementos do mapa não encontrados para ${this.config.mapId}`);
            return;
        }

        // Elementos de entrada
        this.latInput = document.getElementById('latitude');
        this.lngInput = document.getElementById('longitude');
        this.institutionSelect = document.getElementById('institution_select');
        this.noLocationCheckbox = document.getElementById('no_location');

        console.log('Inputs encontrados:', {
            latInput: this.latInput ? 'Sim' : 'Não',
            lngInput: this.lngInput ? 'Sim' : 'Não',
            institutionSelect: this.institutionSelect ? 'Sim' : 'Não',
            noLocationCheckbox: this.noLocationCheckbox ? 'Sim' : 'Não'
        });

        // Coordenadas iniciais
        let initialLat = this.config.lat;
        let initialLng = this.config.lng;

        console.log('Configuração inicial:', {
            configLat: this.config.lat,
            configLng: this.config.lng,
            isEditMode: this.config.isEditMode,
            barrier: this.config.barrier
        });

        // Se estiver editando e tiver uma barreira com coordenadas
        if (this.config.isEditMode && this.config.barrier &&
            this.config.barrier.latitude && this.config.barrier.longitude) {
            initialLat = this.config.barrier.latitude;
            initialLng = this.config.barrier.longitude;
            console.log('Usando coordenadas da barreira:', initialLat, initialLng);
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

        // Adiciona grupos de marcadores ao mapa
        this.existingLocationsLayer.addTo(this.map);
        this.existingBarriersLayer.addTo(this.map);

        // Configura eventos do mapa
        this.setupMapEvents();

        // Se for edição e tiver barreira, plota primeiro a barreira
        if (this.config.isEditMode && this.config.barrier) {
            console.log('Plotando barreira atual para edição:', this.config.barrier);

            // Se a barreira tem instituição, plota ela também
            if (this.config.barrier.institution) {
                this.plotInstitutionAndData(this.config.barrier.institution);
            }

            this.plotCurrentBarrier(this.config.barrier);
        }
        // Se não for edição mas tiver instituição no config
        else if (this.config.institution) {
            this.plotInstitutionAndData(this.config.institution);
        }

        // Se não estiver editando OU se estiver editando mas a barreira não tem coordenadas, cria marcador
        if (!this.config.isEditMode || (this.config.isEditMode && this.config.barrier &&
            (!this.config.barrier.latitude || !this.config.barrier.longitude))) {
            console.log('Criando marcador inicial (AZUL)');
            this.setupMarker(initialLat, initialLng);
        }

        // Sincroniza os valores iniciais
        this.updateInputs(initialLat, initialLng);

        // Configura evento do checkbox "no_location"
        if (this.noLocationCheckbox) {
            this.noLocationCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.disableMap();
                } else {
                    this.enableMap();

                    // Se houver instituição selecionada, recarrega
                    if (this.institutionSelect && this.institutionSelect.value) {
                        setTimeout(() => {
                            const institutionId = this.institutionSelect.value;
                            if (window.institutionsData) {
                                const institution = window.institutionsData.find(inst => inst.id == institutionId);
                                if (institution) {
                                    this.plotInstitutionAndData(institution);
                                }
                            }
                        }, 100);
                    }
                }
            });

            // Verificar estado inicial
            if (this.noLocationCheckbox.checked) {
                this.disableMap();
            }
        }

        // Redimensiona o mapa
        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize();
                console.log('Mapa redimensionado');
            }
        }, 100);

        this.initialized = true;
        console.log('BarrierMap inicializado com sucesso');
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
            console.log('Marcador AZUL movido para nova posição');
        } else {
            this.currentMarker = L.marker([lat, lng], {
                draggable: true,
                icon: blueIcon
            }).addTo(this.map);

            this.currentMarker.bindTooltip("Localização da Barreira (Editável)", {
                permanent: true,
                direction: 'top',
                offset: [0, -35],
                className: 'bg-primary text-white border-0 fw-bold rounded shadow-sm px-2 py-1'
            });

            // Evento de arrastar
            this.currentMarker.on('dragend', () => {
                const pos = this.currentMarker.getLatLng();
                console.log('Marcador AZUL arrastado para:', pos.lat, pos.lng);
                this.updateLocation(pos.lat, pos.lng, false);
            });

            console.log('Marcador AZUL criado e configurado');
        }
    }

    setupMapEvents() {
        console.log('Configurando eventos do mapa');

        // Evento de clique no mapa
        this.map.on('click', (e) => {
            console.log('Mapa clicado em:', e.latlng.lat, e.latlng.lng);

            // Verifica se o checkbox "no_location" está marcado
            if (this.noLocationCheckbox && this.noLocationCheckbox.checked) {
                alert('Por favor, desmarque "Sem localização física" para definir uma localização.');
                return;
            }

            // Verifica se há uma instituição selecionada
            if (!this.institutionSelect || !this.institutionSelect.value) {
                alert('Por favor, selecione uma instituição base primeiro.');
                return;
            }

            this.updateLocation(e.latlng.lat, e.latlng.lng, false);
        });
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
    }

    updateLocation(lat, lng, moveMap = false) {
        const fLat = parseFloat(lat).toFixed(8);
        const fLng = parseFloat(lng).toFixed(8);

        console.log('Atualizando localização para:', fLat, fLng);

        // Atualiza todos os inputs
        this.updateInputs(lat, lng);

        // Move marcador AZUL
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

    getBarrierStatus(barrier) {
        // Primeiro, tenta obter o status da última inspeção
        if (barrier.inspections && barrier.inspections.length > 0) {
            // Ordena inspeções por data (mais recente primeiro)
            const inspections = [...barrier.inspections].sort((a, b) => {
                return new Date(b.inspection_date || b.created_at) - new Date(a.inspection_date || a.created_at);
            });

            const latestInspection = inspections[0];
            if (latestInspection && latestInspection.status) {
                return barrierStatusLabels[latestInspection.status] || 'Sem status';
            }
        }

        // Fallback: verifica se há um campo status direto (para compatibilidade)
        if (barrier.status) {
            return barrierStatusLabels[barrier.status] || 'Sem status';
        }

        return 'Sem status';
    }

    plotInstitutionAndData(institution) {
        console.log('Plotando instituição, localizações e barreiras:', institution);

        // Remove marcador da instituição anterior
        if (this.institutionMarker) {
            this.map.removeLayer(this.institutionMarker);
        }

        // Remove marcadores existentes
        this.existingLocationsLayer.clearLayers();
        this.existingBarriersLayer.clearLayers();

        // Verifica se a instituição tem coordenadas válidas
        if (!institution.latitude || !institution.longitude ||
            isNaN(parseFloat(institution.latitude)) || isNaN(parseFloat(institution.longitude))) {
            console.error('Instituição sem coordenadas válidas:', institution);
            return;
        }

        // Plota a instituição (VERMELHO)
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

        console.log('Marcador da instituição plotado (VERMELHO)');

        // Plota as localizações existentes da instituição (CINZA)
        if (institution.locations && institution.locations.length > 0) {
            console.log('Plotando', institution.locations.length, 'localizações existentes (CINZA)');

            const greyIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [20, 32],
                iconAnchor: [10, 32],
                shadowSize: [32, 32]
            });

            institution.locations.forEach(location => {
                // Verifica se a localização tem coordenadas válidas
                if (!location.latitude || !location.longitude ||
                    isNaN(parseFloat(location.latitude)) || isNaN(parseFloat(location.longitude))) {
                    console.warn('Localização sem coordenadas válidas, pulando:', location.name);
                    return;
                }

                const marker = L.marker([location.latitude, location.longitude], {
                    icon: greyIcon
                }).addTo(this.existingLocationsLayer);

                marker.bindTooltip(`${location.name} (${location.type || 'Local'})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-secondary text-white border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            });
        }

        // Plota as barreiras existentes da instituição (AMARELO)
        if (institution.barriers && institution.barriers.length > 0) {
            console.log('Plotando', institution.barriers.length, 'barreiras existentes (AMARELO)');

            const yellowIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [20, 32],
                iconAnchor: [10, 32],
                shadowSize: [32, 32]
            });

            institution.barriers.forEach(barrier => {
                // Se estiver no modo de edição, não plota a barreira que está sendo editada
                if (this.config.isEditMode && this.config.barrier && this.config.barrier.id === barrier.id) {
                    console.log('Pulando barreira atual (em edição):', barrier.name);
                    return;
                }

                // Verifica se a barreira tem coordenadas válidas
                if (!barrier.latitude || !barrier.longitude ||
                    isNaN(parseFloat(barrier.latitude)) || isNaN(parseFloat(barrier.longitude))) {
                    console.warn('Barreira sem coordenadas válidas, pulando:', barrier.name);
                    return;
                }

                const marker = L.marker([barrier.latitude, barrier.longitude], {
                    icon: yellowIcon
                }).addTo(this.existingBarriersLayer);

                // Obtém o status da barreira (da última inspeção)
                const statusText = this.getBarrierStatus(barrier);

                marker.bindTooltip(`${barrier.name} (${statusText})`, {
                    permanent: false,
                    direction: 'top',
                    className: 'bg-warning text-dark border-0 small rounded shadow-sm px-2 py-1',
                    offset: [0, -32]
                });
            });
        }

        // Move o mapa para a instituição (apenas se não estiver editando ou se não tiver barreira plotada)
        if (!this.config.isEditMode || !this.config.barrier ||
            !this.config.barrier.latitude || !this.config.barrier.longitude) {
            console.log('Movendo mapa para instituição');
            this.map.flyTo([institution.latitude, institution.longitude], institution.default_zoom || 16, {
                animate: true,
                duration: 2
            });
        }

        // Atualiza a localização atual para a da instituição (apenas se não tiver marcador)
        if (!this.currentMarker) {
            this.updateLocation(institution.latitude, institution.longitude, false);
        }
    }

    plotCurrentBarrier(barrier) {
        console.log('Plotando barreira atual para edição:', barrier);

        // Se a barreira tem coordenadas válidas
        if (barrier.latitude && barrier.longitude) {
            // Plota a barreira atual no mapa (AZUL)
            this.updateLocation(barrier.latitude, barrier.longitude, false);

            // Move o mapa para a barreira
            this.map.flyTo([barrier.latitude, barrier.longitude], 18, {
                animate: true,
                duration: 2
            });

            console.log('Barreira atual plotada (AZUL) e mapa movido');
        } else {
            console.log('Barreira não tem coordenadas válidas, não plotando marcador');
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
        // Limpa os inputs
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

        // Se houver instituição selecionada, recarrega os dados
        if (this.institutionSelect && this.institutionSelect.value && window.institutionsData) {
            const selectedInstitution = window.institutionsData.find(inst => inst.id == this.institutionSelect.value);
            if (selectedInstitution) {
                setTimeout(() => {
                    this.plotInstitutionAndData(selectedInstitution);
                }, 100);
            }
        }
    }
}

// Inicialização condicional
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM carregado, verificando configuração do mapa de barreiras...');

    const mapElement = document.getElementById('map-barrier');

    if (!mapElement) {
        console.error('Elemento do mapa (map-barrier) não encontrado');
        return;
    }

    if (!window.barrierMapConfig) {
        console.error('Configuração do mapa (window.barrierMapConfig) não encontrada');
        return;
    }

    if (typeof L === 'undefined') {
        console.error('Biblioteca Leaflet (L) não carregada');
        return;
    }

    console.log('Configuração do mapa encontrada:', window.barrierMapConfig);

    try {
        // Inicializar o mapa
        const map = new BarrierMap(window.barrierMapConfig);
        window.barrierMapInstance = map;

        // Inicializar o gerenciador de formulário
        const formManager = new FormManager(window.barrierMapConfig);
        window.formManagerInstance = formManager;

        console.log('BarrierMap e FormManager inicializados com sucesso');

        // Configurar evento para quando a instituição for alterada manualmente
        const institutionSelect = document.getElementById('institution_select');
        if (institutionSelect) {
            institutionSelect.addEventListener('change', function() {
                console.log('Instituição alterada manualmente para:', this.value);
                const selectedInstitutionId = this.value;

                // Atualiza o FormManager
                if (window.formManagerInstance) {
                    window.formManagerInstance.handleInstitutionChange();
                }
            });
        }

    } catch (error) {
        console.error('Erro ao inicializar o BarrierMap:', error);
    }
});

// Adicionar script para carregar localizações quando a página é carregada em modo edição
document.addEventListener('DOMContentLoaded', function() {
    // Se houver uma instituição inicial (no caso de edição)
    if (window.initialInstitutionId && window.formManagerInstance) {
        // Força o carregamento das localizações da instituição atual
        setTimeout(() => {
            console.log('Carregando localizações da instituição inicial:', window.initialInstitutionId);
            window.formManagerInstance.loadInstitutionLocations(window.initialInstitutionId);
        }, 500);
    }
});

// Expor as classes globalmente para acesso via console
if (typeof window !== 'undefined') {
    window.BarrierMap = BarrierMap;
    window.FormManager = FormManager;
}
