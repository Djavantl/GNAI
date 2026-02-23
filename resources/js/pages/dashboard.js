document.addEventListener('DOMContentLoaded', function () {
    const data = window.dashboardData;
    const colors = data.colors;

    // 1. Gráfico de Barras: Alunos vs Equipe
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
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 2. Gráfico Circular (Doughnut): Status do PEI
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
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    }
                }
            }
        });
    }
});