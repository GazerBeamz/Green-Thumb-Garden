// Static data for orders
const orderData = {
    daily: {
        jan: Array(31).fill(0).map((_, i) => 50 + i * 10), // Jan 1-31: 50, 60, ..., 350
        feb: Array(28).fill(0).map((_, i) => 60 + i * 12), // Feb 1-28: 60, 72, ..., 384
        mar: Array(31).fill(0).map((_, i) => 70 + i * 15), // Mar 1-31: 70, 85, ..., 520
        apr: Array(30).fill(0).map((_, i) => 50 + i * 13)  // Apr 1-30: 50, 63, ..., 427
    },
    monthly: [350, 384, 520, 427, 450, 480, 500, 510, 490, 470, 460, 430], // Highest orders per month (Jan-Dec)
    yearly: [400, 450, 475] // Average high orders per year (2023-2025)
};

document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById('orderChart').getContext('2d');
    let chartInstance;

    // Create gradient for line and fill
    const gradientLine = ctx.createLinearGradient(0, 0, 0, 400);
    gradientLine.addColorStop(0, '#4ade80');
    gradientLine.addColorStop(1, '#22c55e');

    const gradientFill = ctx.createLinearGradient(0, 0, 0, 400);
    gradientFill.addColorStop(0, 'rgba(74, 222, 128, 0.3)');
    gradientFill.addColorStop(1, 'rgba(34, 197, 94, 0.1)');

    // Initial chart setup (default to Daily, April)
    const initialLabels = Array.from({ length: 30 }, (_, i) => `04/${String(i + 1).padStart(2, '0')}`);
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: initialLabels,
            datasets: [{
                label: 'Orders',
                data: orderData.daily.apr,
                borderColor: gradientLine,
                backgroundColor: gradientFill,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fff',
                pointBorderColor: gradientLine,
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 6,
                    caretSize: 6,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return `Orders: ${context.parsed.y}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Orders',
                        font: { size: 14, weight: '600' },
                        color: 'var(--text-color)'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        font: { size: 12 },
                        color: 'var(--text-color)',
                        stepSize: 100
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        font: { size: 14, weight: '600' },
                        color: 'var(--text-color)'
                    },
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { size: 12 },
                        color: 'var(--text-color)',
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            },
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement.length ? 'pointer' : 'default';
            }
        }
    });

    // Handle view mode change
    document.getElementById('viewMode').addEventListener('change', (e) => {
        const viewMode = e.target.value;
        const monthFilter = document.getElementById('monthFilter');
        monthFilter.classList.toggle('d-none', viewMode !== 'daily');

        if (viewMode === 'daily') {
            const selectedMonth = monthFilter.value;
            updateDailyChart(selectedMonth);
        } else if (viewMode === 'monthly') {
            updateMonthlyChart();
        } else {
            updateYearlyChart();
        }
    });

    // Handle month filter change for daily view
    document.getElementById('monthFilter').addEventListener('change', (e) => {
        const selectedMonth = e.target.value;
        updateDailyChart(selectedMonth);
    });

    // Update chart for Daily view
    function updateDailyChart(month) {
        const daysInMonth = month === 'feb' ? 28 : month === 'apr' ? 30 : 31;
        const monthNum = month === 'jan' ? '01' : month === 'feb' ? '02' : month === 'mar' ? '03' : '04';
        chartInstance.data.labels = Array.from({ length: daysInMonth }, (_, i) => `${monthNum}/${String(i + 1).padStart(2, '0')}`);
        chartInstance.data.datasets[0].data = orderData.daily[month];
        chartInstance.options.scales.x.title.text = 'Date';
        chartInstance.update();
    }

    // Update chart for Monthly view
    function updateMonthlyChart() {
        chartInstance.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        chartInstance.data.datasets[0].data = orderData.monthly;
        chartInstance.options.scales.x.title.text = 'Month';
        chartInstance.update();
    }

    // Update chart for Yearly view
    function updateYearlyChart() {
        chartInstance.data.labels = ['2023', '2024', '2025'];
        chartInstance.data.datasets[0].data = orderData.yearly;
        chartInstance.options.scales.x.title.text = 'Year';
        chartInstance.update();
    }
});