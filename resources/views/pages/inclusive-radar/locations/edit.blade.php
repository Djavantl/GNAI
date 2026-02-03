<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Localização - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 480px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e2e8f0; }
    </style>
</head>
<body class="bg-gray-100 p-4 md:p-8">
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4 text-gray-800 flex items-center gap-2">
        <span class="bg-amber-500 text-white p-2 rounded-lg">📝</span>
        Editar Ponto de Referência: <span class="text-amber-600">{{ $location->name }}</span>
    </h1>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
            <ul class="list-disc ml-5 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.locations.update', $location) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- COLUNA DA ESQUERDA --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-amber-50 p-4 rounded-xl border border-amber-200 shadow-sm">
                    <label class="block font-bold text-amber-900 mb-2 italic">1. Instituição Base</label>
                    <select name="institution_id" id="institution_select" required
                            class="w-full border-amber-300 p-3 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none bg-white font-semibold text-amber-800">
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}"
                                    data-lat="{{ $inst->latitude }}"
                                    data-lng="{{ $inst->longitude }}"
                                    data-zoom="{{ $inst->default_zoom ?? 16 }}"
                                {{ old('institution_id', $location->institution_id) == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold text-gray-700">Nome do Local/Prédio</label>
                        <input type="text" name="name" value="{{ old('name', $location->name) }}" required
                               class="w-full border p-2.5 rounded-lg border-gray-300 focus:ring-2 focus:ring-amber-400"
                               placeholder="Ex: Bloco Acadêmico II">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700">Tipo de Local</label>
                        <input type="text"
                               name="type"
                               value="{{ old('type', $location->type) }}"
                               class="w-full border p-2.5 rounded-lg border-gray-300 focus:ring-2 focus:ring-amber-400 outline-none"
                               placeholder="Ex: Pavilhão, Bloco, Laboratório...">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700">Descrição/Observações</label>
                        <textarea name="description" rows="3" class="w-full border p-2.5 rounded-lg border-gray-300"
                                  placeholder="Detalhes adicionais sobre o local...">{{ old('description', $location->description) }}</textarea>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg border flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }} class="w-5 h-5 text-amber-600">
                        <label for="is_active" class="text-sm font-bold text-gray-700 italic">Ponto Ativo no Sistema</label>
                    </div>
                </div>
            </div>

            {{-- COLUNA DA DIREITA: MAPA --}}
            <div class="lg:col-span-7 space-y-4">
                <label class="block font-bold text-gray-700 mb-2 flex justify-between items-center">
                    <span>2. Ajuste a posição no mapa se necessário</span>
                    <span id="coord_indicator" class="text-[10px] font-mono bg-green-600 text-white px-3 py-1 rounded-full shadow-sm">
                        Posição Atual Carregada
                    </span>
                </label>
                <div id="map"></div>

                {{-- Coordenadas Ocultas --}}
                <input type="hidden" name="latitude" id="lat" value="{{ old('latitude', $location->latitude) }}">
                <input type="hidden" name="longitude" id="lng" value="{{ old('longitude', $location->longitude) }}">
            </div>
        </div>

        <div class="mt-10 pt-6 border-t flex justify-end gap-4">
            <a href="{{ route('inclusive-radar.locations.index') }}" class="bg-gray-200 px-8 py-3 rounded-lg font-bold hover:bg-gray-300 text-gray-700">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-12 py-3 rounded-lg shadow-xl font-bold text-lg transition-colors">
                Atualizar Localização
            </button>
        </div>
    </form>
</div>

<script>
    // Inicializa o mapa nas coordenadas atuais do prédio
    const currentLat = {{ old('latitude', $location->latitude) }};
    const currentLng = {{ old('longitude', $location->longitude) }};

    const map = L.map('map').setView([currentLat, currentLng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let marker;
    let existingMarkers = L.layerGroup().addTo(map);

    const instSelect = document.getElementById('institution_select');
    const coordIndicator = document.getElementById('coord_indicator');

    const goldIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], shadowSize: [41, 41]
    });

    const greyIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [20, 32], iconAnchor: [10, 32], shadowSize: [32, 32]
    });

    const institutionsData = @json($institutions);
    const editingLocationId = {{ $location->id }};

    function updateMarker(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng], { draggable: true, icon: goldIcon }).addTo(map);

        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        coordIndicator.innerText = `Posição: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        coordIndicator.classList.add('bg-green-600');

        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            updateMarker(pos.lat, pos.lng);
        });
    }

    function loadExistingMarkers(instId) {
        existingMarkers.clearLayers();
        const inst = institutionsData.find(i => i.id == instId);

        if (inst && inst.locations) {
            inst.locations.forEach(loc => {
                // NÃO plota o pin cinza se for o próprio prédio que estamos editando
                if (loc.id !== editingLocationId) {
                    L.marker([loc.latitude, loc.longitude], { icon: greyIcon })
                        .addTo(existingMarkers)
                        .bindPopup(`<b>Outro local:</b> ${loc.name}`);
                }
            });
        }
    }

    // Carregamento inicial do Pin do prédio e outros pins do campus
    updateMarker(currentLat, currentLng);
    loadExistingMarkers(instSelect.value);

    instSelect.addEventListener('change', function() {
        const instId = this.value;
        if (instId) {
            const inst = institutionsData.find(i => i.id == instId);
            map.flyTo([inst.latitude, inst.longitude], inst.default_zoom || 16);
            loadExistingMarkers(instId);
            // Ao trocar de instituição, movemos o pin dourado para o centro da nova inst
            updateMarker(inst.latitude, inst.longitude);
        }
    });

    map.on('click', function(e) {
        updateMarker(e.latlng.lat, e.latlng.lng);
    });
</script>
</body>
</html>
