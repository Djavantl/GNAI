<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Instituição Base - Radar Inclusivo</title>
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
        <div class="bg-blue-600 p-3 rounded-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Nova Instituição Base</h1>
            <p class="text-slate-500">Defina o ponto central e as informações da sede para o mapa de barreiras.</p>
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

    <form action="{{ route('inclusive-radar.institutions.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- LADO ESQUERDO: DADOS --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-slate-50 p-6 rounded-xl border border-slate-200 space-y-4">
                    <h2 class="text-lg font-bold text-slate-700 mb-4">Informações Gerais</h2>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Nome da Instituição</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"
                               placeholder="Ex: IFBA - Campus Guanambi">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Sigla / Nome Curto</label>
                        <input type="text" name="short_name" value="{{ old('short_name') }}"
                               class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none"
                               placeholder="Ex: IFBA-GBI">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-600 mb-1">Cidade</label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                   class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1">UF</label>
                            <input type="text" name="state" value="{{ old('state') }}" maxlength="2" required
                                   class="w-full border-slate-300 p-2.5 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none text-center uppercase">
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl border border-blue-200 space-y-4">
                    <h2 class="text-lg font-bold text-blue-800 mb-4">Coordenadas da Sede</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-blue-700 uppercase">Latitude</label>
                            <input type="text" name="latitude" id="lat" value="{{ old('latitude') }}" readonly required
                                   class="w-full bg-white border-blue-300 p-2 rounded-lg border font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-blue-700 uppercase">Longitude</label>
                            <input type="text" name="longitude" id="lng" value="{{ old('longitude') }}" readonly required
                                   class="w-full bg-white border-blue-300 p-2 rounded-lg border font-mono text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1">Zoom Padrão do Mapa</label>
                        <input type="range" name="default_zoom" min="1" max="20" value="{{ old('default_zoom', 16) }}"
                               class="w-full h-2 bg-blue-200 rounded-lg appearance-none cursor-pointer" id="zoom_range">
                        <div class="flex justify-between text-xs text-blue-600 font-bold mt-1">
                            <span>Cidade (10)</span>
                            <span id="zoom_val">16</span>
                            <span>Rua (20)</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-5 h-5 text-blue-600 rounded">
                    <label for="is_active" class="font-bold text-slate-700">Instituição Ativa</label>
                </div>
            </div>

            {{-- LADO DIREITO: MAPA --}}
            <div class="lg:col-span-7">
                <div class="sticky top-6">
                    <label class="block font-bold text-slate-700 mb-3 flex justify-between">
                        <span>Localize a Instituição no Mapa</span>
                        <span class="text-xs font-normal text-slate-500 italic">Clique para definir a sede</span>
                    </label>
                    <div id="map"></div>
                    <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg flex gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-amber-800">
                            <strong>Dica:</strong> As barreiras relatadas serão exibidas ao redor deste ponto. Certifique-se de marcar o centro do campus ou da sede administrativa.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 pt-8 border-t flex justify-end gap-4">
            <a href="{{ route('inclusive-radar.institutions.index') }}" class="px-8 py-3 bg-slate-200 text-slate-700 rounded-lg font-bold hover:bg-slate-300 transition">Cancelar</a>
            <button type="submit" class="px-12 py-3 bg-blue-600 text-white rounded-lg font-bold shadow-lg hover:bg-blue-700 transition">Salvar Instituição</button>
        </div>
    </form>
</div>

<script>
    // Inicia no Brasil se não houver old value
    const startLat = {{ old('latitude') ?? -14.2350 }};
    const startLng = {{ old('longitude') ?? -51.9253 }};
    const startZoom = {{ old('default_zoom') ?? 4 }};

    const map = L.map('map').setView([startLat, startLng], startZoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    // Se já tinha valor (old), coloca o marcador
    @if(old('latitude') && old('longitude'))
        marker = L.marker([{{ old('latitude') }}, {{ old('longitude') }}]).addTo(map);
    @endif

    map.on('click', function(e) {
        const { lat, lng } = e.latlng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }

        document.getElementById('lat').value = lat.toFixed(8);
        document.getElementById('lng').value = lng.toFixed(8);
    });

    // Sincroniza o valor visual do zoom
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
