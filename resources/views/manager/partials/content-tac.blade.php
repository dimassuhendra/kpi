<div class="max-w-7xl mx-auto space-y-10 pb-10">

    {{-- A. STATS CARDS --}}
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
                        class="w-8 h-8 rounded-xl bg-{{ $card['color'] }}-5-5 flex items-center justify-center text-{{ $card['color'] }}-500 text-xs">
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
        {{-- ROW 1: TREND & RATIO --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Chart Daily Trend --}}
            <div class="lg:col-span-2 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                <div class="mb-8 px-2 flex justify-between items-start">
                    <div>
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest">Daily Activity Trend
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium uppercase mt-1 tracking-wider">Beban kerja
                            harian tim/staff.</p>
                    </div>
                    <div class="flex gap-2">
                        <select onchange="updateDynamicChart('chartDailyTrend', this.value)"
                            class="text-[9px] font-bold border-none bg-slate-50 rounded-lg focus:ring-0 cursor-pointer">
                            <option value="all">ALL TEAM</option>
                            @foreach ($staffChartData as $index => $staff)
                                <option value="{{ $index }}">{{ strtoupper($staff['nama']) }}</option>
                            @endforeach
                        </select>
                        <button onclick="exportChart('chartDailyTrend', 'Trend-Harian')"
                            class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 rounded-lg hover:bg-slate-200 transition-colors">
                            <i class="fas fa-download text-xs"></i>
                        </button>
                    </div>
                </div>
                <div style="height:320px;"><canvas id="chartDailyTrend"></canvas></div>
            </div>

            <div class="flex flex-col gap-6">
                {{-- Donut 1: Temuan vs Laporan --}}
                <div
                    class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex-1 flex flex-col items-center justify-center relative">
                    <div class="w-full flex justify-between items-start absolute top-6 px-6">
                        <h4 class="text-[9px] font-black uppercase text-slate-500 tracking-[0.2em]">Temuan vs Laporan
                        </h4>
                        <div class="flex gap-1">
                            <select onchange="updateDynamicChart('donutInisiatif', this.value)"
                                class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                                <option value="all">ALL</option>
                                @foreach ($staffChartData as $index => $staff)
                                    <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                                @endforeach
                            </select>
                            <button onclick="exportChart('donutInisiatif', 'Temuan-vs-Laporan')"
                                class="text-slate-300 hover:text-slate-500"><i
                                    class="fas fa-camera text-[10px]"></i></button>
                        </div>
                    </div>
                    <div class="relative w-full h-40 mt-8">
                        <canvas id="donutInisiatif"></canvas>
                        <div id="donutInisiatifLegend"
                            class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-4">
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 mt-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase">Temuan TAC</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase">Laporan Masuk</span>
                        </div>
                    </div>
                </div>

                {{-- Donut 2: Mandiri vs Bantuan --}}
                <div
                    class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex-1 flex flex-col items-center justify-center relative">
                    <div class="w-full flex justify-between items-start absolute top-6 px-6">
                        <h4 class="text-[9px] font-black uppercase text-slate-500 tracking-[0.2em]">Mandiri vs Bantuan
                        </h4>
                        <div class="flex gap-1">
                            <select onchange="updateDynamicChart('donutMandiri', this.value)"
                                class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                                <option value="all">ALL</option>
                                @foreach ($staffChartData as $index => $staff)
                                    <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                                @endforeach
                            </select>
                            <button onclick="exportChart('donutMandiri', 'Mandiri-vs-Bantuan')"
                                class="text-slate-300 hover:text-slate-500"><i
                                    class="fas fa-camera text-[10px]"></i></button>
                        </div>
                    </div>
                    <div class="relative w-full h-40 mt-8">
                        <canvas id="donutMandiri"></canvas>
                        <div id="donutMandiriLegend"
                            class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pt-4">
                        </div>
                    </div>
                    <div class="flex justify-center gap-4 mt-4">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase">Mandiri</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-slate-200"></span>
                            <span class="text-[8px] font-black text-slate-400 uppercase">Bantuan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 2: STAFF & MIX --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                <div class="mb-8 px-2 flex justify-between items-start">
                    <div>
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest">Staff Productivity Ratio
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium uppercase mt-1 tracking-wider">Distribusi
                            pekerjaan per staff.</p>
                    </div>
                    <button onclick="exportChart('chartStaffProductivity', 'Produktivitas-Staff')"
                        class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 rounded-lg hover:bg-slate-200">
                        <i class="fas fa-download text-xs"></i>
                    </button>
                </div>
                <div style="height:350px;"><canvas id="chartStaffProductivity"></canvas></div>
            </div>

            <div class="flex flex-col gap-6">
                {{-- Workload Mix --}}
                <div
                    class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm flex-1 flex flex-col items-center justify-center relative">
                    <div class="absolute top-6 right-6 flex gap-2">
                        <select onchange="updateDynamicChart('chartWorkloadMix', this.value)"
                            class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                            <option value="all">ALL</option>
                            @foreach ($staffChartData as $index => $staff)
                                <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                            @endforeach
                        </select>
                        <button onclick="exportChart('chartWorkloadMix', 'Workload-Mix')"
                            class="text-slate-300 hover:text-slate-500"><i
                                class="fas fa-camera text-[10px]"></i></button>
                    </div>
                    <div class="text-center mb-4 mt-4">
                        <h4 class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Workload Mix</h4>
                    </div>
                    <div class="relative w-full h-40">
                        <canvas id="chartWorkloadMix"></canvas>
                        <div id="workloadLegend"
                            class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        </div>
                    </div>
                </div>

                {{-- Avg Response Limit --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex-1 relative">
                    <div class="absolute top-4 right-4 flex gap-2">
                        <select onchange="updateDynamicChart('barResponseThreshold', this.value)"
                            class="text-[8px] font-bold border-none bg-slate-50 rounded-md p-1 focus:ring-0">
                            <option value="all">ALL</option>
                            @foreach ($staffChartData as $index => $staff)
                                <option value="{{ $index }}">{{ explode(' ', $staff['nama'])[0] }}</option>
                            @endforeach
                        </select>
                        <button onclick="exportChart('barResponseThreshold', 'Response-Time')"
                            class="text-slate-300 hover:text-slate-500"><i
                                class="fas fa-camera text-[10px]"></i></button>
                    </div>
                    <h4 class="text-[9px] font-black uppercase text-slate-400 mb-2 tracking-widest text-center">Avg
                        Response Limit</h4>
                    <p
                        class="text-[8px] text-slate-400 font-bold tracking-tighter text-center mb-4 uppercase text-rose-400 italic">
                        Target: < 15 Menit</p>
                            <div style="height:120px;"><canvas id="barResponseThreshold"></canvas></div>
                </div>
            </div>
        </div>

        {{-- ROW 3: MINI CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $miniCharts = [
                    ['title' => 'Total Cases', 'id' => 'chartCountCase', 'desc' => 'Akumulasi case teknis.'],
                    ['title' => 'Avg Response', 'id' => 'chartAvgTime', 'desc' => 'Rata-rata respon (Menit).'],
                    ['title' => 'Inisiatif', 'id' => 'chartInisiatif', 'desc' => 'Jumlah temuan mandiri.'],
                    ['title' => 'Mandiri', 'id' => 'chartMandiri', 'desc' => 'Case tanpa bantuan.'],
                ];
            @endphp
            @foreach ($miniCharts as $mc)
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm relative group">
                    <button onclick="exportChart('{{ $mc['id'] }}', '{{ $mc['title'] }}')"
                        class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity text-slate-300">
                        <i class="fas fa-camera text-xs"></i>
                    </button>
                    <div class="text-center mb-4">
                        <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest">{{ $mc['title'] }}
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

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.weight = '700';
    Chart.defaults.plugins.legend.display = false;

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
                setDonutLabel('workloadLegend', globalWorkload.case, globalWorkload.activity, 'Technical');
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
                setDonutLabel('workloadLegend', s.cases, s.activities, 'Technical');
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
            maintainAspectRatio: false
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
    setDonutLabel('workloadLegend', globalWorkload.case, globalWorkload.activity, 'Technical');

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
