Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6c757d';

const dashboardPeso = new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
    maximumFractionDigits: 0,
});

const dashboardPieColors = ['#1f4d3a', '#2e7d5b', '#c07c3b', '#e0a458', '#8aa89a'];

const dashboardColorWithAlpha = (hex, alpha) => {
    const value = hex.replace('#', '');
    const r = parseInt(value.substring(0, 2), 16);
    const g = parseInt(value.substring(2, 4), 16);
    const b = parseInt(value.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
};

let dashboardBarChart = null;
let dashboardPieChart = null;

window.renderDashboardCharts = (data) => {
    const barCanvas = document.getElementById('dashboardBarChart');
    const pieCanvas = document.getElementById('dashboardPieChart');
    if (!barCanvas || !pieCanvas || !data || !data.budget || !Array.isArray(data.budget.sectors)) {
        return;
    }

    const sectors = data.budget.sectors;
    const labels = sectors.map((sector) => sector.name);
    const allocated = sectors.map((sector) => Number(sector.allocated) || 0);
    const disbursed = sectors.map((sector) => Number(sector.disbursed) || 0);
    const allocatedMillions = allocated.map((value) => value / 1000000);
    const disbursedMillions = disbursed.map((value) => value / 1000000);

    const sectorColors = sectors.map((_, index) => dashboardPieColors[index % dashboardPieColors.length]);
    const allocatedColors = sectorColors;
    const disbursedColors = sectorColors.map((color) => dashboardColorWithAlpha(color, 0.45));

    if (dashboardBarChart === null) {
        dashboardBarChart = new Chart(barCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Allocated', data: allocatedMillions, backgroundColor: allocatedColors, borderRadius: 4 },
                    { label: 'Disbursed', data: disbursedMillions, backgroundColor: disbursedColors, borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            generateLabels: (chart) => chart.data.datasets.map((dataset, index) => ({
                                text: dataset.label,
                                fillStyle: index === 0 ? dashboardPieColors[0] : dashboardColorWithAlpha(dashboardPieColors[0], 0.45),
                                strokeStyle: 'transparent',
                                lineWidth: 0,
                                hidden: !chart.isDatasetVisible(index),
                                datasetIndex: index
                            }))
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => ` ${ctx.dataset.label}: ${dashboardPeso.format((ctx.parsed.y || 0) * 1000000)}`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { callback: (value) => `₱${Number(value).toLocaleString('en-PH')}M` }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    } else {
        dashboardBarChart.data.labels = labels;
        dashboardBarChart.data.datasets[0].data = allocatedMillions;
        dashboardBarChart.data.datasets[0].backgroundColor = allocatedColors;
        dashboardBarChart.data.datasets[1].data = disbursedMillions;
        dashboardBarChart.data.datasets[1].backgroundColor = disbursedColors;
        dashboardBarChart.update();
    }

    if (dashboardPieChart === null) {
        dashboardPieChart = new Chart(pieCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: allocated,
                    backgroundColor: dashboardPieColors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 12 } },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const total = ctx.dataset.data.reduce((sum, value) => sum + Number(value), 0);
                                const pct = total > 0 ? ((Number(ctx.parsed) / total) * 100).toFixed(1) : '0.0';
                                return ` ${ctx.label}: ${dashboardPeso.format(Number(ctx.parsed) || 0)} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        dashboardPieChart.data.labels = labels;
        dashboardPieChart.data.datasets[0].data = allocated;
        dashboardPieChart.update();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // ONLY RUN ON DASHBOARD PAGE
    if (document.getElementById('dashboardBarChart')) {
        window.renderDashboardCharts(window.DASHBOARD_DATA || null);
    }

    // ONLY RUN ON RESIDENTS PAGE
    if (document.getElementById('residentsLineChart')) {
        const trend = window.RESIDENT_TREND || { labels: [], data: [] };
        new Chart(document.getElementById('residentsLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [{ label: 'Registered Residents', data: trend.data, borderColor: '#1f4d3a', backgroundColor: 'rgba(31, 77, 58, 0.1)', fill: true, tension: 0.4, pointRadius: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false, grid: { color: '#f1f1f1' } }, x: { grid: { display: false } } } }
        });
    }
});
