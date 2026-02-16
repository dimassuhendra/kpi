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
    <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                <i class="fas fa-chart-line mr-2 text-emerald-600"></i> Monthly Category Trend
            </h3>
            <p class="text-[9px] text-slate-400 mt-1 italic description-text">
                Tren volume harian untuk mendeteksi anomali sistem secara real-time.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100">
                <input type="date" id="start_date_infra"
                    class="text-[9px] font-bold bg-transparent border-none focus:ring-0 p-1">
                <span class="text-slate-400 text-[9px]">-</span>
                <input type="date" id="end_date_infra"
                    class="text-[9px] font-bold bg-transparent border-none focus:ring-0 p-1">
                <button onclick="changeInfraFilter('custom', this)"
                    class="filter-btn-infra p-1.5 hover:bg-emerald-500 hover:text-white rounded-lg transition">
                    <i class="fas fa-search text-[9px]"></i>
                </button>
            </div>

            <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-100">
                <button onclick="changeInfraFilter('today', this)"
                    class="filter-btn-infra px-3 py-1.5 text-[9px] font-bold rounded-lg hover:text-emerald-600 transition">TODAY</button>
                <button onclick="changeInfraFilter('weekly', this)"
                    class="filter-btn-infra px-3 py-1.5 text-[9px] font-bold rounded-lg hover:text-emerald-600 transition">WEEKLY</button>
                <button onclick="changeInfraFilter('monthly', this)"
                    class="filter-btn-infra px-3 py-1.5 text-[9px] font-bold rounded-lg bg-emerald-500 text-white shadow-sm transition">MONTHLY</button>
            </div>

            <select id="infraTrendStaffSelector" onchange="updateInfraTrendByStaff(this.value)"
                class="text-[9px] font-bold border-none bg-slate-50 rounded-lg focus:ring-0 cursor-pointer px-3 py-2">
                <option value="all">ALL STAFF</option>
                @foreach ($staffInfraData as $staff)
                    <option value="{{ $staff['nama'] }}">{{ strtoupper($staff['nama']) }}</option>
                @endforeach
            </select>

            <button onclick="exportCard('card-trend', 'Monthly-Trend')"
                class="export-btn flex items-center gap-2 px-3 py-1.5 bg-slate-50 rounded-lg text-slate-400 hover:text-emerald-600 border border-slate-100">
                <i class="fas fa-download text-[10px]"></i> <span class="text-[9px] font-black">SAVE IMG</span>
            </button>
        </div>
    </div>
    <div style="height: 320px;">
        <canvas id="infraTrendChart"></canvas>
    </div>
</div>

<script>
    // 1. Konfigurasi Global
    const colorPalette = {
        'Network': '#059669',
        'CCTV': '#0ea5e9',
        'GPS': '#f59e0b',
        'Lainnya': '#94a3b8'
    };

    // Data Awal dari Backend
    let infraWorkloadAll = @json($infraWorkload);
    let staffWorkloadDist = @json($staffWorkloadDist);
    let staffDataRaw = @json($staffInfraData);
    let categories = @json($availableCategories);
    let masterTrendData = @json($infraTrendData);
    let masterTrendLabels = @json($trendLabels);

    let donutChartInstance = null;
    let barChartInstance = null;
    let infraTrendChartInstance = null;

    // --- FUNGSI AJAX FILTER (Hanya untuk Trend) ---
    async function changeInfraFilter(type, element = null) {
        if (element && element.tagName === 'BUTTON') {
            element.parentElement.querySelectorAll('.filter-btn-infra').forEach(btn => {
                btn.classList.remove('bg-emerald-500', 'text-white', 'shadow-sm');
            });
            element.classList.add('bg-emerald-500', 'text-white', 'shadow-sm');
        }

        const startDate = document.getElementById('start_date_infra').value;
        const endDate = document.getElementById('end_date_infra').value;
        const divisiId = new URLSearchParams(window.location.search).get('divisi_id') || '2';

        let url = `?divisi_id=${divisiId}&filter=${type}`;
        if (type === 'custom') {
            if (!startDate || !endDate) return alert('Pilih tanggal dulu!');
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();

            // 1. Update Data Trend (Labels & Data Points)
            // Controller Anda mengirim trendLabels di dalam data.trend.labels (berdasarkan kode controller sebelumnya)
            const newLabels = data.trend ? data.trend.labels : data.trendLabels;
            const newData = data.infraTrendData;

            if (infraTrendChartInstance) {
                infraTrendChartInstance.data.labels = newLabels;
                infraTrendChartInstance.data.datasets.forEach(ds => {
                    ds.data = newData[ds.label] || [];
                });
                infraTrendChartInstance.update();
            }
            // Re-sync master data untuk filter staff trend
            masterTrendData = newData;
            masterTrendLabels = newLabels;

        } catch (error) {
            console.error("Gagal update data trend:", error);
        }
    }

    // --- REFRESH DONUT CHART ---
    function updateDonutChart(staffName) {
        if (!donutChartInstance) return;

        // Proteksi jika data per staff tidak ada, gunakan data all
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
            '<div class="col-span-2 text-center text-slate-400 text-[10px]">No Data</div>';

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

    // --- REFRESH BAR CHART ---
    function updateBarChartMode(mode) {
        if (!barChartInstance) return;
        const btnTotal = document.getElementById('btn-mode-total');
        const btnStaff = document.getElementById('btn-mode-staff');

        if (mode === 'total') {
            barChartInstance.data.labels = ['Total Kolektif'];
            barChartInstance.data.datasets.forEach(ds => {
                ds.data = [staffDataRaw.reduce((acc, curr) => acc + (Number(curr[ds.label]) || 0), 0)];
            });
            btnTotal.classList.add('bg-white', 'text-emerald-700', 'shadow-sm');
            btnStaff.classList.remove('bg-white', 'text-emerald-700', 'shadow-sm');
        } else {
            barChartInstance.data.labels = staffDataRaw.map(s => s.nama);
            barChartInstance.data.datasets.forEach(ds => {
                ds.data = staffDataRaw.map(s => Number(s[ds.label]) || 0);
            });
            btnStaff.classList.add('bg-white', 'text-emerald-700', 'shadow-sm');
            btnTotal.classList.remove('bg-white', 'text-emerald-700', 'shadow-sm');
        }
        barChartInstance.update();
    }

    // --- INITIALIZE ON LOAD ---
    document.addEventListener('DOMContentLoaded', function() {
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";

        // Donut
        const ctxDonut = document.getElementById('infraWorkloadChart');
        if (ctxDonut) {
            donutChartInstance = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        borderWidth: 4,
                        borderColor: '#fff'
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

        // Bar
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
                        borderRadius: 5
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
            updateBarChartMode('total');
        }

        // Trend (Line)
        const ctxTrend = document.getElementById('infraTrendChart');
        if (ctxTrend) {
            infraTrendChartInstance = new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: masterTrendLabels,
                    datasets: Object.keys(masterTrendData).map(cat => ({
                        label: cat,
                        data: masterTrendData[cat],
                        borderColor: colorPalette[cat],
                        backgroundColor: colorPalette[cat] + '15',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
</script>
