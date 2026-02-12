{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script> --}}
<style>
    .description-text {
        line-height: 1.4;
        min-height: 28px;
    }

    .info-card {
        transition: all 0.3s ease;
    }

    /* Memastikan tombol export tidak mengganggu layout */
    .export-btn {
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .export-btn:hover {
        opacity: 1;
    }
</style>

{{-- Row 1: Donut & Bar Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Card 1: Donut Chart + Persentase --}}
    <div id="card-donut"
        class="lg:col-span-1 info-card p-6 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-start mb-2">
            <div class="pr-4">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                    <i class="fas fa-chart-pie mr-2 text-emerald-600"></i> Workload Distribution
                </h3>
                <p class="text-[9px] text-slate-400 mt-1 italic description-text">
                    Persentase kontribusi kategori pekerjaan terhadap total beban kerja divisi.
                </p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <button onclick="exportCard('card-donut', 'Workload-Distribution')"
                    class="export-btn text-slate-400 hover:text-emerald-600">
                    <i class="fas fa-camera text-xs"></i>
                </button>
                <select id="donutStaffFilter" onchange="updateDonutChart(this.value)"
                    class="text-[9px] font-black border-none bg-slate-100 rounded-lg px-2 py-1 focus:ring-emerald-500 text-slate-600 outline-none">
                    <option value="all">ALL STAFF</option>
                    @foreach ($staffWorkloadDist as $nama => $data)
                        <option value="{{ $nama }}">{{ strtoupper($nama) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex-grow flex items-center justify-center relative my-4" style="min-height: 230px;">
            <canvas id="infraWorkloadChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span id="donutTotalNumber" class="text-2xl font-black text-slate-700">0</span>
                <span class="text-[8px] font-bold text-slate-400 uppercase">Total Task</span>
            </div>
        </div>

        <div id="donutLegend" class="mt-4 grid grid-cols-2 gap-3 border-t border-slate-50 pt-5">
            {{-- Diisi via JS --}}
        </div>
    </div>

    {{-- Card 2: Bar Chart Productivity --}}
    <div id="card-bar"
        class="lg:col-span-2 info-card p-6 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="flex justify-between items-start mb-2">
            <div class="pr-4">
                <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                    <i class="fas fa-users-cog mr-2 text-emerald-600"></i> Staff Technical Focus
                </h3>
                <p class="text-[9px] text-slate-400 mt-1 italic description-text">
                    Perbandingan spesialisasi teknis antar staff untuk efisiensi alokasi SDM.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="exportCard('card-bar', 'Staff-Focus')"
                    class="export-btn text-slate-400 hover:text-emerald-600">
                    <i class="fas fa-camera text-xs"></i>
                </button>
                <div class="flex p-1 bg-slate-100 rounded-xl">
                    <button onclick="updateBarChartMode('total')" id="btn-mode-total"
                        class="px-4 py-1.5 text-[9px] font-black rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-emerald-100/50">TOTAL</button>
                    <button onclick="updateBarChartMode('staff')" id="btn-mode-staff"
                        class="px-4 py-1.5 text-[9px] font-black rounded-lg transition-all text-slate-400 hover:text-slate-600">STAFF</button>
                </div>
            </div>
        </div>
        <div class="flex-grow mt-4" style="min-height: 280px;">
            <canvas id="staffInfraChart"></canvas>
        </div>
    </div>
</div>

{{-- Row 2: Trend Chart --}}
<div id="card-trend" class="info-card p-6 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100 mb-6">
    <div class="flex justify-between items-start mb-6">
        <div>
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                <i class="fas fa-chart-line mr-2 text-emerald-600"></i> Monthly Category Trend
            </h3>
            <p class="text-[9px] text-slate-400 mt-1 italic description-text">
                Tren volume harian (Tanggal 1-{{ $trendLabels[count($trendLabels) - 1] ?? '30' }}) untuk mendeteksi
                anomali sistem secara real-time.
            </p>
        </div>
        <button onclick="exportCard('card-trend', 'Monthly-Trend')"
            class="export-btn flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-lg text-slate-400 hover:text-emerald-600 border border-slate-100">
            <i class="fas fa-download text-[10px]"></i> <span class="text-[9px] font-black">SAVE IMG</span>
        </button>
    </div>
    <div style="height: 320px;">
        <canvas id="infraTrendChart"></canvas>
    </div>
</div>

<script>
    // Konfigurasi Global
    const colorPalette = {
        'Network': '#059669',
        'CCTV': '#0ea5e9',
        'GPS': '#f59e0b',
        'Lainnya': '#94a3b8'
    };

    // Data dari Backend
    const infraWorkloadAll = @json($infraWorkload);
    const staffWorkloadDist = @json($staffWorkloadDist);
    const staffDataRaw = @json($staffInfraData);
    const categories = @json($availableCategories);
    const trendLabels = @json($trendLabels);
    const trendData = @json($infraTrendData);

    let donutChartInstance = null;
    let barChartInstance = null;

    // --- FUNGSI EXPORT ---
    function exportCard(elementId, fileName) {
        const element = document.getElementById(elementId);
        html2canvas(element, {
            backgroundColor: "#ffffff",
            scale: 2,
            useCORS: true
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `${fileName}-${new Date().getTime()}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    }

    // --- UPDATE DONUT CHART ---
    function updateDonutChart(staffName) {
        if (!donutChartInstance) return;
        const targetData = (staffName === 'all') ? infraWorkloadAll : (staffWorkloadDist[staffName] || {});

        const labels = Object.keys(targetData);
        const values = Object.values(targetData);
        const total = values.reduce((a, b) => a + b, 0);

        donutChartInstance.data.labels = labels;
        donutChartInstance.data.datasets[0].data = values;
        donutChartInstance.data.datasets[0].backgroundColor = labels.map(k => colorPalette[k] || '#cbd5e1');
        donutChartInstance.update();

        document.getElementById('donutTotalNumber').innerText = total;

        const legendContainer = document.getElementById('donutLegend');
        legendContainer.innerHTML = labels.length ? '' :
            '<div class="col-span-2 text-center text-[10px] font-bold text-slate-300">No records found</div>';

        labels.forEach((label, i) => {
            const pct = total > 0 ? ((values[i] / total) * 100).toFixed(1) : 0;
            legendContainer.innerHTML += `
                <div class="flex flex-col gap-1 p-2 bg-slate-50/50 rounded-lg border border-slate-50">
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: ${colorPalette[label] || '#cbd5e1'}"></span>
                        <span class="text-[9px] uppercase font-black text-slate-400">${label}</span>
                    </div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm font-black text-slate-700">${values[i]}</span>
                        <span class="text-[9px] font-black text-emerald-600">${pct}%</span>
                    </div>
                </div>`;
        });
    }

    // --- UPDATE BAR CHART ---
    function updateBarChartMode(mode) {
        if (!barChartInstance) return;
        const btnTotal = document.getElementById('btn-mode-total');
        const btnStaff = document.getElementById('btn-mode-staff');

        if (mode === 'total') {
            barChartInstance.data.labels = ['Total Kolektif'];
            barChartInstance.data.datasets.forEach(ds => {
                ds.data = [staffDataRaw.reduce((acc, curr) => acc + (Number(curr[ds.label]) || 0), 0)];
            });
            btnTotal.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-emerald-100/50";
            btnStaff.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all text-slate-400 hover:text-slate-600";
        } else {
            barChartInstance.data.labels = staffDataRaw.map(s => s.nama);
            barChartInstance.data.datasets.forEach(ds => {
                ds.data = staffDataRaw.map(s => Number(s[ds.label]) || 0);
            });
            btnStaff.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all bg-white text-emerald-700 shadow-sm border border-emerald-100/50";
            btnTotal.className =
                "px-4 py-1.5 text-[9px] font-black rounded-lg transition-all text-slate-400 hover:text-slate-600";
        }
        barChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Chart Defaults
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '700';
        Chart.defaults.color = '#94a3b8';

        // Initialize Donut
        const ctxDonut = document.getElementById('infraWorkloadChart');
        if (ctxDonut) {
            donutChartInstance = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        borderWidth: 5,
                        borderColor: '#fff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            updateDonutChart('all');
        }

        // Initialize Bar
        const ctxBar = document.getElementById('staffInfraChart');
        if (ctxBar) {
            barChartInstance = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: categories.map(cat => ({
                        label: cat,
                        data: [],
                        backgroundColor: colorPalette[cat] || '#cbd5e1',
                        borderRadius: 5,
                        barPercentage: 0.6
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f8fafc'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 9
                                }
                            }
                        }
                    }
                }
            });
            updateBarChartMode('total');
        }

        // Initialize Line Trend
        const ctxTrend = document.getElementById('infraTrendChart');
        if (ctxTrend) {
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: Object.keys(trendData).map(cat => ({
                        label: cat,
                        data: trendData[cat],
                        borderColor: colorPalette[cat],
                        backgroundColor: colorPalette[cat] + '10',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: (context) => context.dataset.data[context.dataIndex] >
                            0 ? 3 : 0,
                        pointHoverRadius: 6
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                font: {
                                    size: 9
                                }
                            }
                        },
                        tooltip: {
                            padding: 12,
                            cornerRadius: 10
                        }
                    }
                }
            });
        }
    });
</script>
