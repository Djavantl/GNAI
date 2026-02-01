<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatar Barreira - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        #map { height: 450px; width: 100%; border-radius: 8px; z-index: 1; border: 1px solid #e2e8f0; }
        .map-disabled { opacity: 0.4; pointer-events: none; filter: grayscale(1); }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8">

<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow-lg border-t-4 border-blue-700">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <span class="bg-blue-700 text-white p-2 rounded-lg"><i class="fas fa-map-marker-alt"></i></span>
            Relatar Barreira de Acessibilidade
        </h1>
        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Novo Registro</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm text-sm">
            <p class="font-bold mb-1 italic">Atenção: Verifique os erros no formulário.</p>
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.barriers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-blue-50 p-5 rounded-lg border border-blue-200 shadow-inner space-y-4">
                    <h3 class="text-blue-800 font-bold mb-2 flex items-center gap-2 uppercase text-xs">
                        <i class="fas fa-university"></i> 1. Instituição e Setor
                    </h3>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Campus / Unidade *</label>
                        <select name="institution_id" id="institution_select" required
                                class="w-full border p-2 rounded bg-white focus:ring-2 focus:ring-blue-500 text-sm font-semibold {{ $errors->has('institution_id') ? 'border-red-500' : '' }}">
                            <option value="">-- Selecione a Unidade --</option>
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}" data-lat="{{ $inst->latitude }}" data-lng="{{ $inst->longitude }}"
                                    {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="location_wrapper" class="{{ old('institution_id') ? '' : 'hidden' }} space-y-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase italic text-blue-600">Prédio / Local de referência</label>
                            <select name="location_id" id="location_select" class="w-full border p-2 rounded bg-white text-sm">
                                <option value="">Selecione um local...</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase italic">Complemento da Localização</label>
                            <input type="text" name="location_specific_details" value="{{ old('location_specific_details') }}" class="w-full border p-2 rounded text-sm" placeholder="Ex: Próximo à porta de entrada">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Título do Relato *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm {{ $errors->has('name') ? 'border-red-500' : '' }}"
                               placeholder="Ex: Calçada irregular">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Data da Identificação *</label>
                            <input type="date" name="identified_at" value="{{ old('identified_at', now()->format('Y-m-d')) }}" required
                                   class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Prioridade</label>
                            <select name="priority" class="w-full border p-2 rounded bg-white text-sm">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baixa</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Média</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Crítica</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 space-y-4">
                        <h3 class="text-gray-700 font-bold mb-2 flex items-center gap-2 uppercase text-xs">
                            <i class="fas fa-users"></i> 2. Pessoa Impactada
                        </h3>

                        <div class="flex flex-col gap-2 p-2 bg-blue-50 rounded border border-blue-100">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }} class="w-4 h-4 text-gray-700 rounded">
                                <label for="is_anonymous" class="text-xs font-bold text-gray-700 uppercase cursor-pointer italic">
                                    Desejo fazer um Relato Anônimo
                                </label>
                            </div>

                            <div id="wrapper_not_applicable" class="flex items-center gap-2 border-t border-blue-200 pt-2 mt-1">
                                <input type="checkbox" name="not_applicable" id="not_applicable" value="1"
                                       {{ old('not_applicable') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 rounded">
                                <label for="not_applicable" class="text-xs font-bold text-blue-800 uppercase cursor-pointer">
                                    Não se aplica a uma pessoa específica (Relato Geral)
                                </label>
                            </div>
                        </div>

                        <div id="identification_fields" class="space-y-4">
                            <div id="person_selects" class="grid grid-cols-1 gap-4 {{ old('not_applicable') ? 'hidden' : '' }}">
                                <div>
                                    <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Estudante Impactado</label>
                                    <select name="affected_student_id" class="w-full border p-2 rounded bg-white text-sm">
                                        <option value="">-- Selecione o Estudante (se houver) --</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('affected_student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->person?->name ?? 'Estudante sem nome' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Profissional Impactado</label>
                                    <select name="affected_professional_id" class="w-full border p-2 rounded bg-white text-sm">
                                        <option value="">-- Selecione o Profissional (se houver) --</option>
                                        @foreach($professionals as $prof)
                                            <option value="{{ $prof->id }}" {{ old('affected_professional_id') == $prof->id ? 'selected' : '' }}>
                                                {{ $prof->person?->name ?? 'Profissional sem nome' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div id="manual_person_data" class="space-y-3 {{ old('not_applicable') ? '' : 'hidden' }}">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Nome da Pessoa</label>
                                        <input type="text" name="affected_person_name"
                                               value="{{ old('affected_person_name') }}"
                                               class="w-full border p-2 rounded text-sm {{ $errors->has('affected_person_name') ? 'border-red-500' : '' }}" placeholder="Ex: João da Silva">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Papel / Vínculo</label>
                                        <input type="text" name="affected_person_role"
                                               value="{{ old('affected_person_role') }}"
                                               class="w-full border p-2 rounded text-sm {{ $errors->has('affected_person_role') ? 'border-red-500' : '' }}" placeholder="Ex: Visitante">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Descrição Detalhada da Barreira</label>
                        <textarea name="description" rows="3" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500 text-sm"
                                  placeholder="Explique o problema encontrado...">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Categoria</label>
                        <select name="barrier_category_id" class="w-full border p-2 rounded bg-white text-sm">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('barrier_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-800 mb-2 border-l-4 border-blue-600 pl-2 text-xs uppercase">Deficiências Relacionadas *</label>
                    <div class="grid grid-cols-2 gap-2 bg-gray-50 p-3 rounded border border-gray-200 max-h-40 overflow-y-auto custom-scrollbar {{ $errors->has('deficiencies') ? 'border-red-500' : '' }}">
                        @foreach($deficiencies as $def)
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                       {{ in_array($def->id, old('deficiencies', [])) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                                <label for="def_{{ $def->id }}" class="text-xs cursor-pointer text-gray-600">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div class="relative">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block font-bold text-gray-700 text-xs uppercase">Localização no Mapa</label>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="no_location" id="no_location" value="1" {{ old('no_location') ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                            <label for="no_location" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer italic">Sem localização física</label>
                        </div>
                    </div>

                    <div id="map"></div>

                    <div id="coord_badge" class="absolute bottom-4 right-4 z-[1000] bg-white/90 px-3 py-1 rounded shadow text-[10px] font-mono border border-gray-300 hidden">
                        LAT: <span id="lat_txt">0</span> | LNG: <span id="lng_txt">0</span>
                    </div>

                    <input type="hidden" name="latitude" id="lat" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ old('longitude') }}">

                    <div class="mt-4">
                        <label class="block font-bold text-gray-700 mb-1 text-xs uppercase">Status Inicial</label>
                        <select name="barrier_status_id" class="w-full border p-2 rounded bg-white text-sm">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('barrier_status_id', $loop->first ? $status->id : null) == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-lg border-2 border-dashed border-gray-300">
                    <label class="block font-bold text-gray-700 mb-2 text-xs uppercase flex items-center gap-2">
                        <i class="fas fa-camera text-blue-600"></i> Fotos da Barreira e Vistoria
                    </label>
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-700 file:text-white cursor-pointer">

                    <div class="mt-4">
                        <label class="block font-bold text-gray-700 mb-1 text-[10px] uppercase">Notas da Vistoria Inicial (Opcional)</label>
                        <textarea name="inspection_description" rows="2" class="w-full border p-2 rounded text-sm" placeholder="Observações sobre as fotos ou condições do local...">{{ old('inspection_description') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 p-4 bg-gray-100 rounded border border-gray-300">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="w-5 h-5 text-green-600 rounded">
                            <label for="is_active" class="cursor-pointer text-sm font-bold text-green-700">Ativar no Radar</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-4 mt-8 border-t pt-6">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-12 py-3 rounded shadow-lg transition font-bold text-lg">
                <i class="fas fa-save mr-2"></i> Registrar Barreira
            </button>
            <a href="{{ route('inclusive-radar.barriers.index') }}" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-6 py-3 rounded transition font-bold">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'], attribution: '© Google Maps' });
    const map = L.map('map', { center: [-14.2350, -51.9253], zoom: 4, layers: [googleHybrid] });

    let layerInstituicao = L.layerGroup().addTo(map);
    let layerReferencias = L.layerGroup().addTo(map);
    let barrierMarker = null;
    const institutionsData = @json($institutions);

    const iconInst = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', iconSize: [25,41], iconAnchor: [12,41], popupAnchor: [1,-34] });
    const iconRef = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', iconSize: [20,32], iconAnchor: [10,32] });
    const iconBarrier = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [30,48], iconAnchor: [15,48] });

    function setBarrierLocation(lat,lng,fly=false){
        if(barrierMarker){ barrierMarker.setLatLng([lat,lng]); }
        else{
            barrierMarker=L.marker([lat,lng],{icon:iconBarrier,draggable:true}).addTo(map);
            barrierMarker.on('dragend',function(e){ const pos=e.target.getLatLng(); updateInputs(pos.lat,pos.lng); });
        }
        updateInputs(lat,lng);
        if(fly) map.flyTo([lat,lng],19);
    }

    function updateInputs(lat,lng){
        document.getElementById('lat').value=lat;
        document.getElementById('lng').value=lng;
        document.getElementById('lat_txt').innerText=parseFloat(lat).toFixed(6);
        document.getElementById('lng_txt').innerText=parseFloat(lng).toFixed(6);
        document.getElementById('coord_badge').classList.remove('hidden');
    }

    document.getElementById('institution_select').addEventListener('change',function(){
        const instId=this.value;
        const selected=this.options[this.selectedIndex];
        layerInstituicao.clearLayers();
        layerReferencias.clearLayers();
        if(instId){
            const instLat=selected.dataset.lat;
            const instLng=selected.dataset.lng;
            L.marker([instLat,instLng],{icon:iconInst}).addTo(layerInstituicao).bindPopup("<b>Sede:</b> "+selected.text);
            map.flyTo([instLat,instLng],17);
            document.getElementById('location_wrapper').classList.remove('hidden');

            const inst=institutionsData.find(i=>i.id==instId);
            const locSelect=document.getElementById('location_select');
            locSelect.innerHTML='<option value="">Selecione um local...</option>';

            if(inst && inst.locations){
                inst.locations.forEach(loc=>{
                    const opt=document.createElement('option');
                    opt.value=loc.id;
                    opt.text=loc.name;
                    opt.dataset.lat=loc.latitude;
                    opt.dataset.lng=loc.longitude;
                    if("{{ old('location_id') }}"==loc.id) opt.selected=true;
                    locSelect.appendChild(opt);
                    L.marker([loc.latitude,loc.longitude],{icon:iconRef}).addTo(layerReferencias).bindPopup("<b>Referência:</b> "+loc.name);
                });
            }
        }
    });

    document.getElementById('location_select').addEventListener('change',function(){
        const opt=this.options[this.selectedIndex];
        if(opt.value){ setBarrierLocation(opt.dataset.lat,opt.dataset.lng,true); }
    });

    map.on('click',function(e){
        if(!document.getElementById('no_location').checked && document.getElementById('institution_select').value){
            setBarrierLocation(e.latlng.lat,e.latlng.lng);
        }
    });

    document.getElementById('no_location').addEventListener('change',function(){
        if(this.checked){ if(barrierMarker) map.removeLayer(barrierMarker); barrierMarker=null; document.getElementById('lat').value=''; document.getElementById('lng').value=''; document.getElementById('coord_badge').classList.add('hidden'); }
    });

    // Lógica de Visibilidade da Pessoa Impactada
    function togglePersonFields(){
        const isAnonymous = document.getElementById('is_anonymous').checked;
        const notApplicable = document.getElementById('not_applicable').checked;

        const identificationFields = document.getElementById('identification_fields');
        const wrapperNotApplicable = document.getElementById('wrapper_not_applicable');
        const personSelects = document.getElementById('person_selects');
        const manualData = document.getElementById('manual_person_data');

        if (isAnonymous) {
            // Se for anônimo, esconde tudo de identificação
            identificationFields.classList.add('hidden');
            wrapperNotApplicable.classList.add('hidden');
        } else {
            // Se não for anônimo, mostra o wrapper e decide entre selects ou manual
            identificationFields.classList.remove('hidden');
            wrapperNotApplicable.classList.remove('hidden');

            if(notApplicable){
                personSelects.classList.add('hidden');
                manualData.classList.remove('hidden');
            } else {
                personSelects.classList.remove('hidden');
                manualData.classList.add('hidden');
            }
        }
    }

    document.getElementById('is_anonymous').addEventListener('change', togglePersonFields);
    document.getElementById('not_applicable').addEventListener('change', togglePersonFields);

    document.addEventListener('DOMContentLoaded', function() {
        togglePersonFields();
        const instSelect = document.getElementById('institution_select');
        if(instSelect.value) {
            instSelect.dispatchEvent(new Event('change'));
        }
    });

    @if(old('latitude') && old('longitude'))
    setBarrierLocation("{{ old('latitude') }}","{{ old('longitude') }}",true);
    @endif
</script>
</body>
</html>
