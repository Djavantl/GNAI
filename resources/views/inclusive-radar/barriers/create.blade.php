<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatar Barreira - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 480px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e2e8f0; }
        .map-disabled { opacity: 0.4; pointer-events: none; filter: grayscale(1); }
        /* Estilo para o seletor de camadas ficar visível sobre o mapa */
        .leaflet-control-layers { border-radius: 8px !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important; }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800 flex items-center gap-2">
        <span class="bg-blue-600 text-white p-2 rounded-lg">📍</span>
        Relatar Barreira de Acessibilidade
    </h1>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <p class="font-bold mb-1">Atenção:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.barriers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- COLUNA DA ESQUERDA --}}
            <div class="lg:col-span-5 space-y-5">

                {{-- PASSO 1: INSTITUIÇÃO --}}
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-200 shadow-sm space-y-4">
                    <div>
                        <label class="block font-bold text-blue-900 mb-2">1. Selecione a Instituição</label>
                        <select name="institution_id" id="institution_select" required
                                class="w-full border-blue-300 p-3 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none bg-white font-semibold text-blue-800">
                            <option value="">Selecione o Campus / Unidade...</option>
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}"
                                        data-lat="{{ $inst->latitude }}"
                                        data-lng="{{ $inst->longitude }}"
                                        data-zoom="{{ $inst->default_zoom }}"
                                    {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="location_wrapper" class="{{ old('institution_id') ? '' : 'hidden' }}">
                        <label class="block font-bold text-blue-900 mb-2 italic text-sm">Prédio / Setor de referência</label>
                        <select name="location_id" id="location_select"
                                class="w-full border-blue-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none bg-white text-blue-800">
                            <option value="">Selecione um local conhecido...</option>
                        </select>
                    </div>
                </div>

                {{-- PASSO 2: DETALHES --}}
                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold text-gray-700">Título do Relato</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border p-2.5 rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-400"
                               placeholder="Ex: Degrau na entrada da biblioteca">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700">Descrição</label>
                        <textarea name="description" rows="3" class="w-full border p-2.5 rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-400"
                                  placeholder="Detalhe o problema...">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Categoria</label>
                            <select name="barrier_category_id" class="w-full border p-2 rounded-lg bg-white">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('barrier_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Prioridade</label>
                            <select name="priority" class="w-full border p-2 rounded-lg bg-white">
                                @foreach(['Baixa', 'Média', 'Alta', 'Crítica'] as $p)
                                    <option value="{{ $p }}" {{ old('priority', 'Média') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Status</label>
                            <select name="barrier_status_id" class="w-full border p-2 rounded-lg bg-white">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('barrier_status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Data do Relato</label>
                            <input type="date" name="identified_at" value="{{ old('identified_at', date('Y-m-d')) }}"
                                   class="w-full border p-2 rounded-lg">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border">
                        <input type="checkbox" id="no_location" name="no_location" value="1"
                               {{ old('no_location') ? 'checked' : '' }} class="w-5 h-5 text-blue-600">
                        <label for="no_location" class="text-sm font-medium text-gray-700">Barreira não física (Digital/Atitudinal)</label>
                    </div>
                </div>
            </div>

            {{-- COLUNA DA DIREITA: MAPA REALISTA --}}
            <div class="lg:col-span-7 space-y-4">
                <div id="map_wrapper">
                    <label class="block font-bold text-gray-700 mb-2 flex justify-between items-center">
                        <span>2. Marque o local exato no mapa</span>
                        <span id="coord_indicator" class="text-[10px] font-mono bg-blue-600 text-white px-3 py-1 rounded-full shadow-sm">
                            Selecione a Unidade
                        </span>
                    </label>
                    <div id="map" class="{{ old('no_location') ? 'map-disabled' : '' }}"></div>

                    <input type="hidden" name="latitude" id="lat" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ old('longitude') }}">
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label class="block font-semibold text-gray-800 mb-3">Público Impactado</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($deficiencies as $def)
                            <div class="flex items-center gap-2 bg-white p-2 rounded border">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                    {{ (is_array(old('deficiencies')) && in_array($def->id, old('deficiencies'))) ? 'checked' : '' }}>
                                <label for="def_{{ $def->id }}" class="text-[11px] font-medium cursor-pointer">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 flex items-center justify-between">
                    <label class="font-semibold text-indigo-900">Fotos da barreira:</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="text-sm">
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex gap-8">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }} class="w-5 h-5">
                    <span class="text-sm font-medium text-gray-600 italic">Relato Anônimo</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-5 h-5">
                    <span class="text-sm font-bold text-green-700 underline">Ativo no Radar</span>
                </label>
            </div>

            <div class="flex gap-4 w-full md:w-auto">
                <a href="{{ route('inclusive-radar.barriers.index') }}" class="bg-gray-200 px-8 py-3 rounded-lg font-bold">Voltar</a>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-12 py-3 rounded-lg shadow-xl font-bold text-lg">
                    Salvar Denúncia
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // 1. Definição das Camadas (Tiles)
    // Google Maps Satélite/Híbrido (O mais realista e estável)
    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution: '© Google Maps'
    });

    // Google Maps Ruas (Desenho padrão)
    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains:['mt0','mt1','mt2','mt3'],
        attribution: '© Google Maps'
    });

    // 2. Inicialização do Mapa
    const map = L.map('map', {
        center: [-14.2350, -51.9253],
        zoom: 4,
        layers: [googleHybrid] // Inicia com o Satélite Híbrido do Google
    });

    // Controle de Camadas
    const baseMaps = {
        "Visão Satélite": googleHybrid,
        "Visão Mapa": googleStreets
    };
    L.control.layers(baseMaps).addTo(map);

    let marker, instMarker;
    const instSelect = document.getElementById('institution_select');
    const locSelect = document.getElementById('location_select');
    const institutionsData = @json($institutions);

    // Ícones personalizados
    const instIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41]
    });

    const barrierIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41]
    });

    function placeBarrierMarker(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng], {draggable: true, icon: barrierIcon}).addTo(map);
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        const indicator = document.getElementById('coord_indicator');
        indicator.innerText = `Localizado: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        indicator.classList.replace('bg-blue-600', 'bg-red-600');

        marker.on('dragend', (e) => placeBarrierMarker(e.target.getLatLng().lat, e.target.getLatLng().lng));
    }

    instSelect.addEventListener('change', function() {
        const inst = institutionsData.find(i => i.id == this.value);
        if (instMarker) map.removeLayer(instMarker);
        locSelect.innerHTML = '<option value="">Selecione um local conhecido...</option>';

        if (inst) {
            const lat = parseFloat(inst.latitude);
            const lng = parseFloat(inst.longitude);
            instMarker = L.marker([lat, lng], {icon: instIcon}).addTo(map).bindPopup("Sede");
            map.flyTo([lat, lng], 17);
            document.getElementById('location_wrapper').classList.remove('hidden');

            if (inst.locations) {
                inst.locations.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc.id;
                    opt.text = loc.name;
                    opt.dataset.lat = loc.latitude;
                    opt.dataset.lng = loc.longitude;
                    locSelect.appendChild(opt);
                });
            }
        }
    });

    locSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            const lat = parseFloat(opt.dataset.lat);
            const lng = parseFloat(opt.dataset.lng);
            map.flyTo([lat, lng], 19);
            placeBarrierMarker(lat, lng);
        }
    });

    map.on('click', (e) => {
        if (!document.getElementById('no_location').checked && instSelect.value) {
            placeBarrierMarker(e.latlng.lat, e.latlng.lng);
        }
    });

    @if(old('latitude')) placeBarrierMarker({{ old('latitude') }}, {{ old('longitude') }}); @endif
</script>
</body>
</html>
