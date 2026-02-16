<div class="max-w-7xl mx-auto space-y-10 pb-10">

    {{-- A. STATS CARDS (Tetap dipertahankan sebagai header ringkasan) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $cards = [
                [
                    'label' => 'Pending Approval',
                    'value' => $stats['pending'],
                    'unit' => 'Reports',
                    'color' => 'rose',
                    'icon' => 'fa-clock',
                    'progress' => 35,
                ],
                [
                    'label' => 'Efficiency Rate',
                    'value' => number_format($stats['avg_response_time'], 1),
                    'unit' => 'Min/Case',
                    'color' => 'emerald',
                    'icon' => 'fa-bolt',
                    'progress' => 75,
                ],
                [
                    'label' => 'Monthly Resolved',
                    'value' => $stats['resolved_month'],
                    'unit' => 'Tickets',
                    'color' => 'sky',
                    'icon' => 'fa-check-double',
                    'progress' => 90,
                ],
                [
                    'label' => 'Staff Active Today',
                    'value' => $stats['active_today'],
                    'unit' => 'Members',
                    'color' => 'amber',
                    'icon' => 'fa-users',
                    'progress' => 60,
                ],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 w-24 h-24 bg-{{ $card['color'] }}-500/5 rounded-bl-full translate-x-10 -translate-y-10">
                </div>
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-8 h-8 rounded-xl bg-{{ $card['color'] }}-50 flex items-center justify-center text-{{ $card['color'] }}-500 text-xs">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                    <p class="text-[10px] uppercase font-black text-slate-400 tracking-[0.2em]">{{ $card['label'] }}</p>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-4xl font-black text-slate-800 leading-none tracking-tighter">{{ $card['value'] }}
                    </h2>
                    <span
                        class="text-[10px] font-bold text-{{ $card['color'] }}-500 mb-1 uppercase">{{ $card['unit'] }}</span>
                </div>
                <div class="w-full h-1 bg-slate-50 mt-6 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $card['color'] }}-500" style="width: {{ $card['progress'] }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- B. PERFORMANCE METRICS --}}
    <div class="space-y-8">

        {{-- BARIS 1: 4 KOLOM (DONUTS & THRESHOLD) --}}
        {{-- BARIS 1: 4 KOLOM (DONUTS & THRESHOLD) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Donut 1: Temuan vs Laporan --}}
            <div
                class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center relative h-[320px]">
                <div class="w-full flex justify-between items-start absolute top-6 px-6 z-10">
                    <h4 class="text-[8px] font-black uppercase text-slate-500 tracking-widest"><i
                            class="fas fa-chart-pie mr-2 text-emerald-600"></i> Temuan vs Laporan</h4>
                    <select onchange="updateDynamicChart('donutInisiatif', this.value)"
                        class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                        <option value="all">ALL</option>
                        @foreach ($staffChartData as $index => $staff)
                            <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative w-full h-40 mt-8">
                    <canvas id="donutInisiatif"></canvas>
                    <div id="donutInisiatifLegend"
                        class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    </div>
                </div>
                <div class="flex justify-center gap-3 mt-4">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span><span
                            class="text-[7px] font-bold text-slate-400">TEMUAN</span></div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><span
                            class="text-[7px] font-bold text-slate-400">LAPORAN</span></div>
                </div>
            </div>

            {{-- Donut 2: Mandiri vs Bantuan --}}
            <div
                class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center relative h-[320px]">
                <div class="w-full flex justify-between items-start absolute top-6 px-6 z-10">
                    <h4 class="text-[8px] font-black uppercase text-slate-500 tracking-widest"><i
                            class="fas fa-chart-pie mr-2 text-emerald-600"></i> Mandiri vs Bantuan</h4>
                    <select onchange="updateDynamicChart('donutMandiri', this.value)"
                        class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                        <option value="all">ALL</option>
                        @foreach ($staffChartData as $index => $staff)
                            <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative w-full h-40 mt-8">
                    <canvas id="donutMandiri"></canvas>
                    <div id="donutMandiriLegend"
                        class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"></div>
                </div>
                <div class="flex justify-center gap-3 mt-4">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><span
                            class="text-[7px] font-bold text-slate-400">MANDIRI</span></div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-200"></span><span
                            class="text-[7px] font-bold text-slate-400">BANTUAN</span></div>
                </div>
            </div>

            {{-- Donut 3: Workload Mix --}}
            <div
                class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center relative h-[320px]">
                <div class="w-full flex justify-between items-start absolute top-6 px-6 z-10">
                    <h4 class="text-[8px] font-black uppercase text-slate-500 tracking-widest"><i
                            class="fas fa-chart-pie mr-2 text-emerald-600"></i> Workload Mix</h4>
                    <select onchange="updateDynamicChart('chartWorkloadMix', this.value)"
                        class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                        <option value="all">ALL</option>
                        @foreach ($staffChartData as $index => $staff)
                            <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative w-full h-40 mt-8">
                    <canvas id="chartWorkloadMix"></canvas>
                    <div id="workloadLegend"
                        class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"></div>
                </div>
                <div class="flex justify-center gap-3 mt-4">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-sky-500"></span><span
                            class="text-[7px] font-bold text-slate-400">CASE</span></div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span><span
                            class="text-[7px] font-bold text-slate-400">ACTIVITY</span></div>
                </div>
            </div>

            {{-- Bar 4: Avg Response Limit --}}
            <div
                class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex flex-col items-center justify-center relative h-[320px]">
                <div class="w-full flex justify-between items-start absolute top-6 px-6 z-10">
                    <h4 class="text-[8px] font-black uppercase text-slate-500 tracking-widest"><i
                            class="fas fa-chart-pie mr-2 text-emerald-600"></i> Response Limit</h4>
                    <select onchange="updateDynamicChart('barResponseThreshold', this.value)"
                        class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                        <option value="all">ALL</option>
                        @foreach ($staffChartData as $index => $staff)
                            <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full px-4 mt-12">
                    <p class="text-[8px] text-rose-400 font-bold text-center mb-2 uppercase italic">Target: < 15
                            Menit</p>
                            <div style="height:100px;"><canvas id="barResponseThreshold"></canvas></div>
                </div>
            </div>
        </div>

        {{-- BARIS 2: 1 KOLOM (DAILY TREND FULL WIDTH) --}}
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <div class="mb-8 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-500">
                        <i class="fas fa-chart-line mr-2 text-emerald-600"></i> Daily Activity Trend
                    </h3>
                    <p class="text-[9px] text-slate-400 mt-1 italic">Grafik beban kerja harian tim & kustomisasi
                        tanggal.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100">
                        <input type="date" id="start_date_input"
                            class="text-[9px] font-bold bg-transparent border-none focus:ring-0 p-1">
                        <span class="text-slate-400 text-[9px]">-</span>
                        <input type="date" id="end_date_input"
                            class="text-[9px] font-bold bg-transparent border-none focus:ring-0 p-1">
                        <button onclick="changeFilter('custom', this)"
                            class="filter-btn p-1.5 hover:bg-emerald-500 hover:text-white rounded-lg transition">
                            <i class="fas fa-search text-[9px]"></i>
                        </button>
                    </div>

                    <div class="flex bg-slate-50 p-1 rounded-xl border border-slate-100">
                        <button onclick="changeFilter('today', this)"
                            class="filter-btn px-3 py-1.5 text-[9px] font-bold rounded-lg hover:text-emerald-600 transition">TODAY</button>
                        <button onclick="changeFilter('weekly', this)"
                            class="filter-btn px-3 py-1.5 text-[9px] font-bold rounded-lg hover:text-emerald-600 transition">WEEKLY</button>
                        <button onclick="changeFilter('monthly', this)"
                            class="filter-btn px-3 py-1.5 text-[9px] font-bold rounded-lg bg-emerald-500 text-white shadow-sm transition">MONTHLY</button>
                    </div>

                    <select id="staffSelector" onchange="updateDynamicChart('chartDailyTrend', this.value)"
                        class="text-[9px] font-bold border-none bg-slate-50 rounded-lg focus:ring-0 cursor-pointer px-3 py-2">
                        <option value="all">ALL TEAM</option>
                        @foreach ($staffChartData as $index => $staff)
                            <option value="{{ $index }}">{{ strtoupper($staff['nama']) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="height:350px;">
                <canvas id="chartDailyTrend"></canvas>
            </div>
        </div>

        {{-- BARIS 3: 4 KOLOM (MINI CARDS) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $miniCharts = [
                    ['title' => 'Total Cases', 'id' => 'chartCountCase', 'desc' => 'Akumulasi case teknis.'],
                    ['title' => 'Avg Response', 'id' => 'chartAvgTime', 'desc' => 'Rata-rata respon (Menit).'],
                    ['title' => 'Temuan TAC', 'id' => 'chartInisiatif', 'desc' => 'Jumlah temuan mandiri.'],
                    ['title' => 'Penyelesaian TAC', 'id' => 'chartMandiri', 'desc' => 'Case tanpa bantuan tim infra.'],
                ];
            @endphp
            @foreach ($miniCharts as $mc)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm group">
                    <div class="text-left mb-4">
                        <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest"><i
                                class="fas fa-chart-bar mr-2 text-emerald-600"></i> {{ $mc['title'] }}
                        </p>
                        <p class="text-[7px] text-slate-400 font-bold uppercase tracking-tighter">{{ $mc['desc'] }}
                        </p>
                    </div>
                    <div style="height:150px;"><canvas id="{{ $mc['id'] }}"></canvas></div>
                </div>
            @endforeach
        </div>

    </div>
</div>

{{-- SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.0.1"></script>

<script>
    Chart.register(window['chartjs-plugin-annotation']);

    const colors = {
        emerald: '#10b981',
        amber: '#f59e0b',
        sky: '#0ea5e9',
        indigo: '#6366f1',
        rose: '#f43f5e',
        slate: '#f1f5f9'
    };

    // Store Original Data for "ALL TEAM" reset
    const rawStaffData = @json($staffChartData);
    const globalSummary = @json($summaryData);
    const globalWorkload = @json($workloadMix);
    const globalTrend = {
        cases: @json($trendCases),
        activities: @json($trendActivities),
        labels: @json($trendLabels)
    };
    let masterStaffData = @json($staffChartData);
    let masterTrendLabels = @json($trendLabels);
    let masterTrendCases = @json($trendCases);
    let masterTrendActivities = @json($trendActivities);

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.weight = '700';
    Chart.defaults.plugins.legend.display = false;


    async function changeFilter(type, element = null) {
        // UI: Update tombol aktif
        if (element && element.tagName === 'BUTTON') {
            const parent = element.closest('.flex');
            if (parent) {
                parent.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('bg-emerald-500', 'text-white', 'shadow-sm');
                });
                element.classList.add('bg-emerald-500', 'text-white', 'shadow-sm');
            }
        }

        // Ambil input tanggal
        const startDate = document.getElementById('start_date_input').value;
        const endDate = document.getElementById('end_date_input').value;
        const divisiId = new URLSearchParams(window.location.search).get('divisi_id') || '1';

        // Susun URL
        let url = `?divisi_id=${divisiId}&filter=${type}`;
        if (type === 'custom') {
            if (!startDate || !endDate) return alert('Pilih rentang tanggal dulu bos!');
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();

            // Update data master & UI (Gunakan fungsi updateDashboardUI yang sudah dibuat sebelumnya)
            masterStaffData = data.staffChartData;
            masterTrendLabels = data.trend.labels;
            masterTrendCases = data.trend.cases;
            masterTrendActivities = data.trend.activities;

            document.getElementById('staffSelector').value = 'all';
            updateDashboardUI(data);
        } catch (error) {
            console.error("Gagal update data:", error);
        }
    }

    // Logika Filter Per Staff (Dropdown)
    function updateDynamicChart(chartId, value) {
        const chart = Chart.getChart(chartId);
        if (!chart) return;

        let dCases = (value === 'all') ? masterTrendCases : masterStaffData[value].daily_history.cases;
        let dActs = (value === 'all') ? masterTrendActivities : masterStaffData[value].daily_history.activities;

        chart.data.datasets[0].data = dCases;
        chart.data.datasets[1].data = dActs;
        chart.update();
    }

    // Fungsi Update Semua Visual Dashboard
    function updateDashboardUI(data) {
        // 1. Update Chart Utama (Trend)
        updateChart('chartDailyTrend', [data.trend.cases, data.trend.activities], data.trend.labels);

        // 4. Update Angka Statistik (H2)
        const stats = document.querySelectorAll('h2.text-4xl');
        if (stats.length >= 4) {
            stats[0].innerText = data.stats.pending;
            stats[1].innerText = parseFloat(data.stats.avg_response_time).toFixed(1);
            stats[2].innerText = data.stats.resolved_month;
            stats[3].innerText = data.stats.active_today;
        }
    }

    function updateChart(id, datasets, labels = null) {
        const chart = Chart.getChart(id);
        if (!chart) return;
        if (labels) chart.data.labels = labels;
        datasets.forEach((d, i) => {
            if (chart.data.datasets[i]) chart.data.datasets[i].data = d;
        });
        chart.update();
    }

    // --- HELPER FUNCTIONS ---
    function setDonutLabel(id, val1, val2, label) {
        const total = Number(val1) + Number(val2);
        const percent = total > 0 ? Math.round((val1 / total) * 100) : 0;
        const el = document.getElementById(id);
        if (el) {
            el.innerHTML = `
                <span class="text-2xl font-black text-slate-800 leading-none">${percent}%</span>
                <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1">${label}</span>
            `;
        }
    }

    function exportChart(chartId, filename) {
        const canvas = document.getElementById(chartId);
        const link = document.createElement('a');
        link.download = `${filename}-${new Date().toISOString().slice(0,10)}.png`;
        link.href = canvas.toDataURL('image/png', 1.0);
        link.click();
    }

    function updateDynamicChart(chartId, index) {
        const chart = Chart.getChart(chartId);
        if (!chart) return;

        let newData, label1, label2;

        if (index === 'all') {
            // Restore Original
            if (chartId === 'chartDailyTrend') {
                chart.data.datasets[0].data = globalTrend.cases;
                chart.data.datasets[1].data = globalTrend.activities;
            } else if (chartId === 'donutInisiatif') {
                chart.data.datasets[0].data = [globalSummary.proaktif, globalSummary.penugasan];
                setDonutLabel('donutInisiatifLegend', globalSummary.proaktif, globalSummary.penugasan, 'Temuan TAC');
            } else if (chartId === 'donutMandiri') {
                chart.data.datasets[0].data = [globalSummary.mandiri, globalSummary.bantuan];
                setDonutLabel('donutMandiriLegend', globalSummary.mandiri, globalSummary.bantuan, 'Penyelesaian TAC');
            } else if (chartId === 'chartWorkloadMix') {
                chart.data.datasets[0].data = [globalWorkload.case, globalWorkload.activity];
                setDonutLabel('workloadLegend', globalWorkload.case, globalWorkload.activity, 'Case');
            } else if (chartId === 'barResponseThreshold') {
                chart.data.datasets[0].data = [globalSummary.avg_time];
                chart.data.datasets[0].backgroundColor = globalSummary.avg_time > 15 ? colors.rose : colors.emerald;
            }
        } else {
            // Staff Specific
            const s = rawStaffData[index];
            if (chartId === 'chartDailyTrend' && s.daily_history) {
                chart.data.datasets[0].data = s.daily_history.cases;
                chart.data.datasets[1].data = s.daily_history.activities;
            } else if (chartId === 'donutInisiatif') {
                chart.data.datasets[0].data = [s.inisiatif_count, (s.total_case - s.inisiatif_count)];
                setDonutLabel('donutInisiatifLegend', s.inisiatif_count, (s.total_case - s.inisiatif_count),
                    'Temuan TAC');
            } else if (chartId === 'donutMandiri') {
                chart.data.datasets[0].data = [s.mandiri_count, (s.total_case - s.mandiri_count)];
                setDonutLabel('donutMandiriLegend', s.mandiri_count, (s.total_case - s.mandiri_count),
                    'Penyelesaian TAC');
            } else if (chartId === 'chartWorkloadMix') {
                chart.data.datasets[0].data = [s.cases, s.activities];
                setDonutLabel('workloadLegend', s.cases, s.activities, 'Case');
            } else if (chartId === 'barResponseThreshold') {
                chart.data.datasets[0].data = [s.avg_time];
                chart.data.datasets[0].backgroundColor = s.avg_time > 15 ? colors.rose : colors.emerald;
            }
        }
        chart.update();
    }

    // --- INITIALIZATION ---

    // 1. Daily Trend
    new Chart(document.getElementById('chartDailyTrend'), {
        type: 'line',
        data: {
            labels: globalTrend.labels,
            datasets: [{
                    label: 'Cases',
                    data: globalTrend.cases,
                    borderColor: colors.emerald,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.05)'
                },
                {
                    label: 'Activities',
                    data: globalTrend.activities,
                    borderColor: colors.amber,
                    borderDash: [5, 5],
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true, // Mulai dari angka 0
                    ticks: {
                        // Memaksa angka menjadi bulat (menghilangkan desimal)
                        stepSize: 1,
                        callback: function(value) {
                            if (value % 1 === 0) {
                                return value;
                            }
                        }
                    }
                }
            },
            // Tambahan agar tooltip tidak muncul desimal jika datanya bulat
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });

    // 2. Donuts
    const donutOpt = {
        cutout: '82%',
        responsive: true,
        maintainAspectRatio: false
    };

    new Chart(document.getElementById('donutInisiatif'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [globalSummary.proaktif, globalSummary.penugasan],
                backgroundColor: [colors.amber, colors.emerald],
                borderWidth: 0
            }]
        },
        options: donutOpt
    });
    setDonutLabel('donutInisiatifLegend', globalSummary.proaktif, globalSummary.penugasan, 'Temuan TAC');

    new Chart(document.getElementById('donutMandiri'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [globalSummary.mandiri, globalSummary.bantuan],
                backgroundColor: [colors.emerald, colors.slate],
                borderWidth: 0
            }]
        },
        options: donutOpt
    });
    setDonutLabel('donutMandiriLegend', globalSummary.mandiri, globalSummary.bantuan, 'Penyelesaian TAC');

    new Chart(document.getElementById('chartWorkloadMix'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [globalWorkload.case, globalWorkload.activity],
                backgroundColor: [colors.sky, colors.amber],
                borderWidth: 0
            }]
        },
        options: donutOpt
    });
    setDonutLabel('workloadLegend', globalWorkload.case, globalWorkload.activity, 'Case');

    // 3. Productivity Ratio
    new Chart(document.getElementById('chartStaffProductivity'), {
        type: 'bar',
        data: {
            labels: rawStaffData.map(s => s.nama_lengkap),
            datasets: [{
                    label: 'Technical',
                    data: rawStaffData.map(s => s.total_case),
                    backgroundColor: colors.sky,
                    borderRadius: 6
                },
                {
                    label: 'General',
                    data: rawStaffData.map(s => s.activities),
                    backgroundColor: colors.amber,
                    borderRadius: 6
                },
                {
                    label: 'Self-Inisiatif',
                    data: rawStaffData.map(s => s.inisiatif_count),
                    backgroundColor: colors.emerald,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true
                }
            }
        }
    });

    // 4. Threshold
    new Chart(document.getElementById('barResponseThreshold'), {
        type: 'bar',
        data: {
            labels: ['Avg'],
            datasets: [{
                data: [globalSummary.avg_time],
                backgroundColor: globalSummary.avg_time > 15 ? colors.rose : colors.emerald,
                borderRadius: 20
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    max: 30,
                    display: false
                },
                y: {
                    display: false
                }
            },
            plugins: {
                annotation: {
                    annotations: {
                        line1: {
                            type: 'line',
                            xMin: 15,
                            xMax: 15,
                            borderColor: colors.rose,
                            borderDash: [4, 4],
                            borderWidth: 2
                        }
                    }
                }
            }
        }
    });

    // 5. Mini Charts
    const miniOpt = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                ticks: {
                    size: 8
                }
            },
            y: {
                ticks: {
                    size: 8
                },
                grid: {
                    color: '#f1f5f9'
                }
            }
        }
    };
    const staffLabels = rawStaffData.map(s => s.nama.split(' ')[0]);

    new Chart(document.getElementById('chartCountCase'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: rawStaffData.map(s => s.total_case),
                backgroundColor: colors.sky,
                borderRadius: 4
            }]
        },
        options: miniOpt
    });
    new Chart(document.getElementById('chartAvgTime'), {
        type: 'line',
        data: {
            labels: staffLabels,
            datasets: [{
                data: rawStaffData.map(s => s.avg_time),
                borderColor: colors.amber,
                tension: 0.4
            }]
        },
        options: miniOpt
    });
    new Chart(document.getElementById('chartInisiatif'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: rawStaffData.map(s => s.inisiatif_count),
                backgroundColor: colors.emerald,
                borderRadius: 4
            }]
        },
        options: miniOpt
    });
    new Chart(document.getElementById('chartMandiri'), {
        type: 'bar',
        data: {
            labels: staffLabels,
            datasets: [{
                data: rawStaffData.map(s => s.mandiri_count),
                backgroundColor: colors.indigo,
                borderRadius: 4
            }]
        },
        options: miniOpt
    });
</script>
