document.addEventListener('DOMContentLoaded', function () {
    const data = window.dashboardData;
    const colors = data.colors;

    Chart.defaults.font.family = "'Inter', sans-serif";

    // --- 1. GRÁFICO DE BARRAS (Pessoas) ---
    const ctxBar = document.getElementById('barChartPeople');
    if (ctxBar) {
        new Chart(ctxBar.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Alunos', 'Equipe'],
                datasets: [{
                    label: 'Quantidade',
                    data: [data.students, data.professionals],
                    backgroundColor: [colors.primary, colors.secondary],
                    borderRadius: 10,
                    barThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // --- 2. GRÁFICO CIRCULAR (PEIs) ---
    const ctxPie = document.getElementById('pieChartPei');
    if (ctxPie) {
        new Chart(ctxPie.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Finalizados', 'Não Finalizados'],
                datasets: [{
                    data: [data.peiFinished, data.peiNotFinished],
                    backgroundColor: [colors.success, colors.warning],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                }
            }
        });
    }

    // --- 3. GRÁFICO CIRCULAR (Barreiras) ---
    const ctxBarriers = document.getElementById('doughnutChartBarriers');
    if (ctxBarriers && data.barrierStatuses) {
        new Chart(ctxBarriers.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: data.barrierStatuses.map(s => s.label),
                datasets: [{
                    data: data.barrierStatuses.map(s => s.count),
                    backgroundColor: data.barrierStatuses.map(s => colors[s.color] || colors.primary),
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 10, font: { size: 11 } } }
                }
            }
        });
    }

    // --- 4. LÓGICA DO MAPA E FILTROS ---
    const mapContainer = document.getElementById('mapDashboard');
    if (mapContainer && data.mapBarriers) {

        const getMarkerIcon = (colorName) => {
            const colorMap = {
                'secondary': 'grey',
                'info': 'blue',
                'warning': 'yellow',
                'success': 'green',
                'danger': 'red'
            };

            const color = colorMap[colorName] || 'blue';
            return L.icon({
                iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        };

        const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        });

        const googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '© Google Maps',
            maxZoom: 21
        });

        const map = L.map('mapDashboard', {
            center: [-14.235, -42.769],
            zoom: 15,
            layers: [streetLayer],
            zoomControl: true,
            scrollWheelZoom: true
        });

        const baseMaps = {
            "Mapa de Ruas (OSM)": streetLayer,
            "Satélite (Google)": googleSat
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        const markersGroup = L.layerGroup().addTo(map);

        const renderMap = () => {
            markersGroup.clearLayers();
            const overlay = document.getElementById('map-blocked-overlay');
            const messageSpan = document.getElementById('blocked-message');

            const activeStatuses = Array.from(document.querySelectorAll('.status-specific:checked'))
                .map(s => String(s.value).trim());

            const visibleBarriers = data.mapBarriers.filter(b =>
                activeStatuses.includes(String(b.status).trim())
            );

            const hasData = visibleBarriers.length > 0;
            const allVisibleAreBlocked = hasData && visibleBarriers.every(b => (b.blocks_map === true || b.blocks_map === 1));

            if (!hasData || allVisibleAreBlocked) {
                overlay.classList.remove('d-none');
                overlay.style.display = 'flex';
                messageSpan.innerText = !hasData ? "Nenhuma barreira encontrada." : "As barreiras não se aplicam ao mapa.";
            } else {
                overlay.classList.add('d-none');
                overlay.style.display = 'none';
            }

            const bounds = [];
            visibleBarriers.forEach(barrier => {
                const isBlocked = (barrier.blocks_map === true || barrier.blocks_map === 1);

                if (!isBlocked && barrier.lat && barrier.lng) {
                    const marker = L.marker([barrier.lat, barrier.lng], {
                        icon: getMarkerIcon(barrier.color)
                    })
                        .bindPopup(`
                        <div class="text-center">
                            <h6 class="mb-1 fw-bold">${barrier.name}</h6>
                            <small class="text-muted d-block mb-2">${barrier.category_name}</small>
                            <span class="badge bg-${barrier.color} mb-2">${barrier.status_label}</span><br>
                            <a href="${barrier.url}" class="btn btn-sm btn-primary text-white py-1 px-2" style="font-size: 11px">Ver Detalhes</a>
                        </div>
                    `);
                    markersGroup.addLayer(marker);
                    bounds.push([barrier.lat, barrier.lng]);
                }
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
            }
        };

        const allSwitch = document.getElementById('switch_all');
        const specificSwitches = document.querySelectorAll('.status-specific');

        allSwitch.addEventListener('change', function() {
            specificSwitches.forEach(s => s.checked = this.checked);
            renderMap();
        });

        specificSwitches.forEach(s => {
            s.addEventListener('change', function() {
                allSwitch.checked = Array.from(specificSwitches).every(opt => opt.checked);
                renderMap();
            });
        });

        setTimeout(() => {
            map.invalidateSize();
            renderMap();
        }, 200);
    }
});
