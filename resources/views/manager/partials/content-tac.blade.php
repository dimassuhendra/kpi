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
                <div
                    class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-24 h-24 bg-{{ $card['color'] }}-500/5 rounded-bl-full translate-x-10 -translate-y-10">
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-8 h-8 rounded-xl bg-{{ $card['color'] }}-50 flex items-center justify-center text-{{ $card['color'] }}-500 text-xs">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                        <p class="text-[10px] uppercase font-black text-slate-400 tracking-[0.2em]">{{ $card['label'] }}
                        </p>
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
                <div class="lg:col-span-2 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                    <div class="mb-8 px-2">
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest">Daily Activity Trend
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium uppercase mt-1 tracking-wider">Visualisasi
                            beban
                            kerja harian tim selama 7 hari terakhir.</p>
                    </div>
                    <div style="height:320px;"><canvas id="chartDailyTrend"></canvas></div>
                </div>

                <div class="flex flex-col gap-6">
                    {{-- Donut 1 --}}
                    <div
                        class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex-1 flex flex-col items-center justify-center">
                        <div class="text-center mb-4">
                            <h4 class="text-[9px] font-black uppercase text-slate-500 tracking-[0.2em]">Temuan vs
                                Laporan
                            </h4>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter">Inisiatif Mandiri
                                vs
                                Penugasan</p>
                        </div>
                        <div class="relative w-full h-40">
                            <canvas id="donutInisiatif"></canvas>
                            <div id="donutInisiatifLegend"
                                class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            </div>
                        </div>
                    </div>
                    {{-- Donut 2 --}}
                    <div
                        class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex-1 flex flex-col items-center justify-center">
                        <div class="text-center mb-4">
                            <h4 class="text-[9px] font-black uppercase text-slate-500 tracking-[0.2em]">Mandiri vs
                                Bantuan
                            </h4>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter">Penyelesaian Case
                                Tanpa Eskalasi</p>
                        </div>
                        <div class="relative w-full h-40">
                            <canvas id="donutMandiri"></canvas>
                            <div id="donutMandiriLegend"
                                class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 2: STAFF & MIX --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
                    <div class="mb-8 px-2">
                        <h3 class="font-black text-slate-800 uppercase text-xs tracking-widest">Staff Productivity Ratio
                        </h3>
                        <p class="text-[10px] text-slate-400 font-medium uppercase mt-1 tracking-wider">Perbandingan
                            distribusi pekerjaan teknis, umum, dan dukungan per staff.</p>
                    </div>
                    <div style="height:350px;"><canvas id="chartStaffProductivity"></canvas></div>
                </div>

                <div class="flex flex-col gap-6">
                    <div
                        class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm flex-1 flex flex-col items-center justify-center">
                        <div class="text-center mb-4">
                            <h4 class="text-[9px] font-black uppercase text-slate-500 tracking-widest">Workload Mix</h4>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-tighter">Proporsi Case vs
                                Activity Tim</p>
                        </div>
                        <div class="relative w-full h-40">
                            <canvas id="chartWorkloadMix"></canvas>
                            <div id="workloadLegend"
                                class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex-1">
                        <h4 class="text-[9px] font-black uppercase text-slate-400 mb-2 tracking-widest text-center">Avg
                            Response Limit</h4>
                        <p
                            class="text-[8px] text-slate-400 font-bold tracking-tighter text-center mb-4 uppercase text-rose-400 italic">
                            Target: Di bawah 15 Menit</p>
                        <div style="height:120px;"><canvas id="barResponseThreshold"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- ROW 3: MINI CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $miniCharts = [
                        [
                            'title' => 'Total Cases',
                            'id' => 'chartCountCase',
                            'desc' => 'Akumulasi case teknis ditangani.',
                        ],
                        ['title' => 'Avg Response', 'id' => 'chartAvgTime', 'desc' => 'Rata-rata respon (Menit).'],
                        ['title' => 'Inisiatif', 'id' => 'chartInisiatif', 'desc' => 'Jumlah temuan mandiri staff.'],
                        ['title' => 'Mandiri', 'id' => 'chartMandiri', 'desc' => 'Case yang selesai tanpa bantuan.'],
                    ];
                @endphp
                @foreach ($miniCharts as $mc)
                    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                        <div class="text-center mb-4">
                            <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest">
                                {{ $mc['title'] }}
                            </p>
                            <p class="text-[7px] text-slate-400 font-bold uppercase tracking-tighter">
                                {{ $mc['desc'] }}
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

        // --- CONFIG & COLORS ---
        const colors = {
            emerald: '#10b981',
            amber: '#f59e0b',
            sky: '#0ea5e9',
            indigo: '#6366f1',
            rose: '#f43f5e',
            slate: '#f1f5f9'
        };

        Chart.defaults.color = '#94a3b8';
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.font.weight = '700';
        Chart.defaults.plugins.legend.display = false;

        // --- HELPER PERSENTASE ---
        function setDonutLabel(id, val1, val2, label) {
            const total = val1 + val2;
            const percent = total > 0 ? Math.round((val1 / total) * 100) : 0;
            document.getElementById(id).innerHTML = `
            <span class="text-2xl font-black text-slate-800 leading-none">${percent}%</span>
            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">${label}</span>
        `;
        }

        // --- 1. DAILY TREND ---
        const ctxTrend = document.getElementById('chartDailyTrend').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                        label: 'Cases',
                        data: @json($trendCases),
                        borderColor: colors.emerald,
                        tension: 0.4,
                        pointRadius: 4,
                        fill: true,
                        backgroundColor: 'rgba(16, 185, 129, 0.05)'
                    },
                    {
                        label: 'Activities',
                        data: @json($trendActivities),
                        borderColor: colors.amber,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        grid: {
                            color: '#f8fafc'
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // --- 2. DONUT CHARTS ---
        const configDonut = (id, data, colorArr) => new Chart(document.getElementById(id), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: data,
                    backgroundColor: colorArr,
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                cutout: '82%',
                responsive: true,
                maintainAspectRatio: false
            }
        });

        configDonut('donutInisiatif', [{{ $summaryData['proaktif'] }}, {{ $summaryData['penugasan'] }}], [colors.amber,
            colors.emerald
        ]);
        setDonutLabel('donutInisiatifLegend', {{ $summaryData['proaktif'] }}, {{ $summaryData['penugasan'] }},
            'Inisiatif');

        configDonut('donutMandiri', [{{ $summaryData['mandiri'] }}, {{ $summaryData['bantuan'] }}], [colors.emerald,
            colors.slate
        ]);
        setDonutLabel('donutMandiriLegend', {{ $summaryData['mandiri'] }}, {{ $summaryData['bantuan'] }}, 'Mandiri');

        configDonut('chartWorkloadMix', [{{ $workloadMix['case'] ?? 0 }}, {{ $workloadMix['activity'] ?? 0 }}], [colors
            .sky, colors.amber
        ]);
        setDonutLabel('workloadLegend', {{ $workloadMix['case'] ?? 0 }}, {{ $workloadMix['activity'] ?? 0 }},
            'Technical');

        // --- 3. PRODUCTIVITY RATIO ---
        new Chart(document.getElementById('chartStaffProductivity'), {
            type: 'bar',
            data: {
                labels: @json($staffChartData->pluck('nama_lengkap')),
                datasets: [{
                        label: 'Technical',
                        data: @json($staffChartData->pluck('total_case')),
                        backgroundColor: colors.sky,
                        borderRadius: 6
                    },
                    {
                        label: 'General',
                        data: @json($staffChartData->pluck('total_activity')),
                        backgroundColor: colors.amber,
                        borderRadius: 6
                    },
                    {
                        label: 'Self-Inisiatif',
                        data: @json($staffChartData->pluck('inisiatif_count')),
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
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {
                            color: '#f8fafc'
                        }
                    }
                }
            }
        });

        // --- 4. THRESHOLD ---
        new Chart(document.getElementById('barResponseThreshold'), {
            type: 'bar',
            data: {
                labels: ['Avg'],
                datasets: [{
                    data: [{{ $summaryData['avg_time'] }}],
                    backgroundColor: {{ $summaryData['avg_time'] }} > 15 ? colors.rose : colors.emerald,
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

        // --- 5. MINI CHARTS WITH AXIS ---
        const staffShort = @json($staffChartData->pluck('nama_lengkap')).map(n => n.split(' ')[0]);
        const miniOpt = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        font: {
                            size: 8
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    ticks: {
                        font: {
                            size: 8
                        }
                    },
                    grid: {
                        color: '#f1f5f9'
                    },
                    border: {
                        display: false
                    }
                }
            }
        };

        new Chart(document.getElementById('chartCountCase'), {
            type: 'bar',
            data: {
                labels: staffShort,
                datasets: [{
                    data: @json($staffChartData->pluck('total_case')),
                    backgroundColor: colors.sky,
                    borderRadius: 4
                }]
            },
            options: miniOpt
        });
        new Chart(document.getElementById('chartAvgTime'), {
            type: 'line',
            data: {
                labels: staffShort,
                datasets: [{
                    data: @json($staffChartData->pluck('avg_time')),
                    borderColor: colors.amber,
                    tension: 0.4,
                    pointRadius: 2
                }]
            },
            options: miniOpt
        });
        new Chart(document.getElementById('chartInisiatif'), {
            type: 'bar',
            data: {
                labels: staffShort,
                datasets: [{
                    data: @json($staffChartData->pluck('inisiatif_count')),
                    backgroundColor: colors.emerald,
                    borderRadius: 4
                }]
            },
            options: miniOpt
        });
        new Chart(document.getElementById('chartMandiri'), {
            type: 'bar',
            data: {
                labels: staffShort,
                datasets: [{
                    data: @json($staffChartData->pluck('mandiri_count')),
                    backgroundColor: colors.indigo,
                    borderRadius: 4
                }]
            },
            options: miniOpt
        });
    </script>
