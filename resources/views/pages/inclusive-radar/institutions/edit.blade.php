<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Instituição Base - Radar Inclusivo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 500px; width: 100%; border-radius: 12px; z-index: 1; border: 2px solid #e2e8f0; }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-12">
<div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-lg">
    <div class="flex items-center gap-4 mb-8 border-b pb-6">
        <div class="bg-amber-500 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Editar Instituição Base</h1>
            <p class="text-slate-500">Atualize a localização central ou as informações de identificação do campus.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r shadow-sm">
            <ul class="list-disc ml-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inclusive-radar.institutions.update', $institution) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- LADO ESQUERDO: DADOS --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 space-y-4">
                    <h2 class="text-lg font-bold text-slate-700 mb-4">Informações Gerais</h2>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Nome da Instituição</label>
                        <input type="text" name="name" value="{{ old('name', $institution->name) }}" required
                               class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none"
                               placeholder="Ex: IFBA - Campus Guanambi">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Sigla / Nome Curto</label>
                        <input type="text" name="short_name" value="{{ old('short_name', $institution->short_name) }}"
                               class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none"
                               placeholder="Ex: IFBA-GBI">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-600 mb-1">Cidade</label>
                            <input type="text" name="city" value="{{ old('city', $institution->city) }}" required
                                   class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1">UF</label>
                            <input type="text" name="state" value="{{ old('state', $institution->state) }}" maxlength="2" required
                                   class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-amber-500 outline-none text-center uppercase">
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 p-6 rounded-xl border border-amber-200 space-y-4">
                    <h2 class="text-lg font-bold text-amber-800 mb-4">Coordenadas da Sede</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-amber-700 uppercase">Latitude</label>
                            <input type="text" name="latitude" id="lat" value="{{ old('latitude', $institution->latitude) }}" readonly required
                                   class="w-full bg-white border-amber-300 p-2 rounded-lg border font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-amber-700 uppercase">Longitude</label>
                            <input type="text" name="longitude" id="lng" value="{{ old('longitude', $institution->longitude) }}" readonly required
                                   class="w-full bg-white border-amber-300 p-2 rounded-lg border font-mono text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Zoom Padrão do Mapa</label>
                        <input type="range" name="default_zoom" min="1" max="20" value="{{ old('default_zoom', $institution->default_zoom) }}"
                               class="w-full h-2 bg-amber-200 rounded-lg appearance-none cursor-pointer" id="zoom_range">
                        <div class="flex justify-between text-xs text-amber-600 font-bold mt-1">
                            <span>Cidade (10)</span>
                            <span id="zoom_val">{{ old('default_zoom', $institution->default_zoom) }}</span>
                            <span>Rua (20)</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $institution->is_active) ? 'checked' : '' }}
                    class="w-5 h-5 text-amber-600 rounded">
                    <label for="is_active" class="font-bold text-slate-700">Instituição Ativa</label>
                </div>
            </div>

            {{-- LADO DIREITO: MAPA --}}
            <div class="lg:col-span-7">
                <div class="sticky top-6">
                    <label class="block font-bold text-slate-700 mb-3 flex justify-between">
                        <span>Ajuste a Posição no Mapa</span>
                        <span class="text-xs font-normal text-slate-500 italic text-right">Clique no mapa para mudar o centro</span>
                    </label>
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t flex justify-end gap-4">
            <a href="{{ route('inclusive-radar.institutions.index') }}" class="px-8 py-3 bg-slate-200 text-slate-700 rounded-lg font-bold hover:bg-slate-300 transition">Cancelar</a>
            <button type="submit" class="px-12 py-3 bg-amber-600 text-white rounded-lg font-bold shadow-lg hover:bg-amber-700 transition">Atualizar Instituição</button>
        </div>
    </form>
</div>

<script>
    // Dados da Instituição e suas Localizações vindos do PHP
    const institution = @json($institution);

    const currentLat = {{ old('latitude', $institution->latitude) }};
    const currentLng = {{ old('longitude', $institution->longitude) }};
    const currentZoom = {{ old('default_zoom', $institution->default_zoom) }};

    const map = L.map('map').setView([currentLat, currentLng], currentZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Ícone Cinza para as Localizações (Prédios/Setores)
    const greyIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [20, 32], iconAnchor: [10, 32], shadowSize: [32, 32]
    });

    // 1. PLOTAR LOCALIZAÇÕES EXISTENTES (Pins Cinzas)
    if (institution.locations && institution.locations.length > 0) {
        institution.locations.forEach(loc => {
            L.marker([loc.latitude, loc.longitude], { icon: greyIcon })
                .addTo(map)
                .bindPopup(`<b>Localização:</b> ${loc.name}<br><small>${loc.type ?? ''}</small>`);
        });
    }

    // 2. MARCADOR DA SEDE (Ponto Central - Azul)
    // Usamos o marcador padrão para a sede para diferenciar dos prédios
    let marker = L.marker([currentLat, currentLng], { draggable: false }).addTo(map);
    marker.bindTooltip("Ponto Central da Sede (Arraste clicando no mapa)").openTooltip();

    // Evento de clique para reposicionar a sede
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng(e.latlng);

        document.getElementById('lat').value = lat.toFixed(8);
        document.getElementById('lng').value = lng.toFixed(8);
    });

    // Sincronização do Zoom
    const zoomRange = document.getElementById('zoom_range');
    const zoomVal = document.getElementById('zoom_val');

    zoomRange.addEventListener('input', function() {
        zoomVal.innerText = this.value;
        map.setZoom(this.value);
    });

    map.on('zoomend', function() {
        zoomRange.value = map.getZoom();
        zoomVal.innerText = map.getZoom();
    });
</script>
</body>
</html>
