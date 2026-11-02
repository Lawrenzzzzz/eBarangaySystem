Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6c757d';

document.addEventListener('DOMContentLoaded', () => {
    
    // ONLY RUN ON DASHBOARD PAGE
    if (document.getElementById('dashboardBarChart')) {
        new Chart(document.getElementById('dashboardBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Infrastructure', 'Healthcare', 'Education', 'Social Services'],
                datasets: [
                    { label: 'Allocated', data: [7.8, 5.5, 4.4, 3.8], backgroundColor: '#1f4d3a', borderRadius: 4 },
                    { label: 'Disbursed', data: [7.1, 4.9, 2.7, 2.9], backgroundColor: '#c07c3b', borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } } }
        });

        new Chart(document.getElementById('dashboardPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Infrastructure', 'Healthcare', 'Education', 'Social Services', 'Disaster & P&O'],
                datasets: [{
                    data: [7.8, 5.5, 4.4, 3.8, 3.1],
                    backgroundColor: ['#1f4d3a', '#2e7d5b', '#c07c3b', '#e0a458', '#8aa89a'],
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
                                const total = ctx.dataset.data.reduce((sum, value) => sum + value, 0);
                                const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                return ` ${ctx.label}: ₱${ctx.parsed}M (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ONLY RUN ON BUDGET PAGE
    if (document.getElementById('budgetHorizontalBarChart')) {
        new Chart(document.getElementById('budgetHorizontalBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Infrastructure', 'Healthcare', 'Education', 'Social Services', 'Disaster & P&O'],
                datasets: [
                    { label: 'Allocated', data: [7.8, 5.5, 4.4, 3.8, 3.1], backgroundColor: '#1f4d3a', borderRadius: 4 },
                    { label: 'Disbursed', data: [7.1, 4.9, 2.7, 2.9, 1.8], backgroundColor: '#c07c3b', borderRadius: 4 }
                ]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: { beginAtZero: true, grid: { display: false } }, y: { grid: { display: false } } } }
        });
    }

    // ONLY RUN ON RESIDENTS PAGE
    if (document.getElementById('residentsLineChart')) {
        new Chart(document.getElementById('residentsLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['2016', '2017', '2018', '2019', '2020', '2021'],
                datasets: [{ label: 'Registered Residents', data: [9800, 10560, 11310, 11800, 12100, 12480], borderColor: '#1f4d3a', backgroundColor: 'rgba(31, 77, 58, 0.1)', fill: true, tension: 0.4, pointRadius: 0 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: false, grid: { color: '#f1f1f1' } }, x: { grid: { display: false } } } }
        });
    }
});