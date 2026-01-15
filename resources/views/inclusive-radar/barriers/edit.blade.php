<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Relato - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 480px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e2e8f0; }
        .map-disabled { opacity: 0.4; pointer-events: none; filter: grayscale(1); }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <span class="bg-amber-500 text-white p-2 rounded-lg">📝</span>
            Editar Relato de Barreira
        </h1>
        <span class="text-sm text-gray-500 font-mono bg-gray-100 px-2 py-1 rounded">ID: #{{ $barrier->id }}</span>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.barriers.update', $barrier) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- COLUNA DA ESQUERDA --}}
            <div class="lg:col-span-5 space-y-5">

                {{-- PASSO 1: INSTITUIÇÃO --}}
                <div class="bg-amber-50 p-4 rounded-xl border border-amber-200 shadow-sm space-y-4">
                    <div>
                        <label class="block font-bold text-amber-900 mb-2">1. Instituição Responsável</label>
                        <select name="institution_id" id="institution_select" required
                                class="w-full border-amber-300 p-3 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none bg-white font-semibold text-amber-800">
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}"
                                        data-lat="{{ $inst->latitude }}"
                                        data-lng="{{ $inst->longitude }}"
                                    {{ old('institution_id', $barrier->institution_id) == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="location_wrapper">
                        <label class="block font-bold text-amber-900 mb-2 italic text-sm">Prédio / Setor de referência</label>
                        <select name="location_id" id="location_select"
                                class="w-full border-amber-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none bg-white text-amber-800">
                            <option value="">Selecione um local conhecido...</option>
                        </select>
                    </div>
                </div>

                {{-- DETALHES --}}
                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold text-gray-700">Título do Relato</label>
                        <input type="text" name="name" value="{{ old('name', $barrier->name) }}" required
                               class="w-full border p-2.5 rounded-lg border-gray-300 focus:ring-2 focus:ring-amber-400">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700">Descrição</label>
                        <textarea name="description" rows="3" class="w-full border p-2.5 rounded-lg border-gray-300 focus:ring-2 focus:ring-amber-400">{{ old('description', $barrier->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Categoria</label>
                            <select name="barrier_category_id" class="w-full border p-2 rounded-lg bg-white">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('barrier_category_id', $barrier->barrier_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Prioridade</label>
                            <select name="priority" class="w-full border p-2 rounded-lg bg-white">
                                @foreach(['Baixa', 'Média', 'Alta', 'Crítica'] as $p)
                                    <option value="{{ $p }}" {{ old('priority', $barrier->priority) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- BLOCO RESTAURADO: STATUS E DATA --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Status Atual</label>
                            <select name="barrier_status_id" class="w-full border p-2 rounded-lg bg-white">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('barrier_status_id', $barrier->barrier_status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 text-sm">Data do Relato</label>
                            <input type="date" name="identified_at" value="{{ old('identified_at', $barrier->identified_at?->format('Y-m-d')) }}"
                                   class="w-full border p-2 rounded-lg">
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border">
                        <input type="checkbox" id="no_location" name="no_location" value="1"
                               {{ old('no_location', is_null($barrier->latitude)) ? 'checked' : '' }} class="w-5 h-5 text-amber-600">
                        <label for="no_location" class="text-sm font-medium text-gray-700">Barreira não física (Digital/Atitudinal)</label>
                    </div>
                </div>

                {{-- IMAGENS ATUAIS (ESTILO ASSISTIVE TECHNOLOGIES) --}}
                @if($barrier->images->count() > 0)
                    <div class="mt-4">
                        <label class="block font-bold mb-2 text-gray-700">Imagens Atuais ({{ $barrier->images->count() }})</label>
                        <div class="grid grid-cols-3 gap-2 bg-gray-50 p-3 rounded border border-gray-200">
                            @foreach($barrier->images as $image)
                                <div class="relative group border rounded p-1 bg-white shadow-sm">
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Imagem"
                                         class="h-20 w-full object-cover rounded"
                                         onerror="this.src='https://placehold.co/200x200?text=Erro';">

                                    <button type="button"
                                            onclick="if(confirm('Deseja excluir esta imagem?')) document.getElementById('delete-image-{{ $image->id }}').submit();"
                                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-blue-50 p-4 rounded border border-blue-100">
                    <label class="block font-semibold text-blue-800 mb-1 text-sm">Adicionar Novas Fotos</label>
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                </div>
            </div>

            {{-- COLUNA DA DIREITA: MAPA --}}
            <div class="lg:col-span-7 space-y-4">
                <div id="map_wrapper">
                    <label class="block font-bold text-gray-700 mb-2 flex justify-between items-center">
                        <span>Ajuste a posição no mapa</span>
                        <span id="coord_indicator" class="text-[10px] font-mono bg-red-600 text-white px-3 py-1 rounded-full">
                            Local Carregado
                        </span>
                    </label>
                    <div id="map" class="{{ old('no_location', is_null($barrier->latitude)) ? 'map-disabled' : '' }}"></div>

                    <input type="hidden" name="latitude" id="lat" value="{{ old('latitude', $barrier->latitude) }}">
                    <input type="hidden" name="longitude" id="lng" value="{{ old('longitude', $barrier->longitude) }}">
                </div>

                <div class="bg-gray-50 p-4 rounded-xl border">
                    <label class="block font-semibold text-gray-800 mb-3">Público Impactado</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($deficiencies as $def)
                            <div class="flex items-center gap-2 bg-white p-2 rounded border">
                                <input type="checkbox" name="deficiencies[]" value="{{ $def->id }}" id="def_{{ $def->id }}"
                                    {{ in_array($def->id, old('deficiencies', $barrier->deficiencies->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label for="def_{{ $def->id }}" class="text-[11px] font-medium cursor-pointer">{{ $def->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t flex justify-end gap-4">
            <a href="{{ route('inclusive-radar.barriers.index') }}" class="bg-gray-200 px-8 py-3 rounded-lg font-bold">Cancelar</a>
            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-12 py-3 rounded-lg shadow-xl font-bold">
                Atualizar Relato
            </button>
        </div>
    </form>
</div>

{{-- FORMULÁRIOS DE EXCLUSÃO DE IMAGEM --}}
@foreach($barrier->images as $image)
    <form id="delete-image-{{ $image->id }}" action="{{ route('inclusive-radar.barriers.images.destroy', $image->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

<script>
    const initialInstId = "{{ old('institution_id', $barrier->institution_id) }}";
    const initialLocId = "{{ old('location_id', $barrier->location_id) }}";
    const initialLat = {{ old('latitude', $barrier->latitude ?? 'null') }};
    const initialLng = {{ old('longitude', $barrier->longitude ?? 'null') }};
    const institutionsData = @json($institutions);

    const mapCenter = (initialLat && initialLng) ? [initialLat, initialLng] : [-14.2350, -51.9253];
    const map = L.map('map').setView(mapCenter, (initialLat ? 18 : 4));
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let marker, instMarker;
    const instSelect = document.getElementById('institution_select');
    const locSelect = document.getElementById('location_select');

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

    function setupInstitution(instId, selectedLocId = null, isInitialLoad = false) {
        locSelect.innerHTML = '<option value="">Selecione um local conhecido...</option>';
        const inst = institutionsData.find(i => i.id == instId);

        if (inst) {
            if (instMarker) map.removeLayer(instMarker);
            instMarker = L.marker([inst.latitude, inst.longitude], {icon: instIcon}).addTo(map).bindPopup("Sede");

            if (inst.locations) {
                inst.locations.forEach(loc => {
                    const opt = document.createElement('option');
                    opt.value = loc.id;
                    opt.text = `${loc.name}`;
                    opt.dataset.lat = loc.latitude;
                    opt.dataset.lng = loc.longitude;
                    if (selectedLocId && loc.id == selectedLocId) opt.selected = true;
                    locSelect.appendChild(opt);
                });
            }
            if (!isInitialLoad) map.flyTo([inst.latitude, inst.longitude], 16);
        }
    }

    function placeBarrierMarker(lat, lng) {
        if (!lat || !lng) return;
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng], {draggable: true, icon: barrierIcon}).addTo(map);
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        marker.on('dragend', (e) => placeBarrierMarker(e.target.getLatLng().lat, e.target.getLatLng().lng));
    }

    // Inicialização
    if (initialInstId) setupInstitution(initialInstId, initialLocId, true);
    if (initialLat && initialLng && !document.getElementById('no_location').checked) placeBarrierMarker(initialLat, initialLng);

    instSelect.addEventListener('change', function() { setupInstitution(this.value); });
    locSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            const lat = parseFloat(opt.dataset.lat);
            const lng = parseFloat(opt.dataset.lng);
            map.flyTo([lat, lng], 18);
            placeBarrierMarker(lat, lng);
        }
    });

    map.on('click', (e) => {
        if (!document.getElementById('no_location').checked && instSelect.value) placeBarrierMarker(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('no_location').addEventListener('change', function() {
        if(this.checked) {
            document.getElementById('map').classList.add('map-disabled');
            if(marker) map.removeLayer(marker);
            document.getElementById('lat').value = '';
            document.getElementById('lng').value = '';
        } else {
            document.getElementById('map').classList.remove('map-disabled');
        }
    });
</script>
</body>
</html>
